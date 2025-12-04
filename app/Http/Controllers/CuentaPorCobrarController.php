<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\Cliente;
use App\Models\PoliticaCredito;
use App\Models\Fiador;
use App\Models\Embargo;
use App\Models\Refinanciamiento;
use App\Models\BitacoraAccion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CuentaPorCobrarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // LISTADO GENERAL
    public function index(Request $request)
    {
        // Lista de clientes para el combo de filtros
        $clientes = Cliente::orderBy('nombre')->get();

        // Consulta base
        $cuentasQuery = CuentaPorCobrar::with('cliente');

        // Filtro por cliente (id)
        if ($request->filled('cliente')) {
            $cuentasQuery->where('cliente_id', $request->cliente);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $cuentasQuery->where('estado', $request->estado);
        }

        // Filtro por rango de fechas (fecha_inicio)
        if ($request->filled('desde')) {
            $cuentasQuery->whereDate('fecha_inicio', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $cuentasQuery->whereDate('fecha_inicio', '<=', $request->hasta);
        }

        $cuentas = $cuentasQuery
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return view('cuentas.index', compact('cuentas', 'clientes'));
    }

    // FORM CREAR
    public function create()
    {
        $clientes  = Cliente::where('activo', true)->orderBy('nombre')->get();
        $politicas = PoliticaCredito::orderBy('nombre')->get();
        $usuarios  = User::orderBy('name')->get();

        return view('cuentas.create', compact('clientes', 'politicas', 'usuarios'));
    }

    // GUARDAR NUEVA CUENTA
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'             => ['required', 'exists:clientes,id'],
            'politica_credito_id'    => ['required', 'exists:politicas_credito,id'],
            'usuario_responsable_id' => ['required', 'exists:users,id'],

            'numero_factura'         => ['required', 'string', 'max:50'],
            'fecha_factura'          => ['required', 'date'],
            'tipo_documento'         => ['nullable', 'string', 'max:100'],

            // 💰 capital: obligatorio, numérico, no 0 ni negativo
            'monto_capital_inicial'  => ['required', 'numeric', 'min:0.01'],

            // fecha de inicio no puede ser antes que la factura
            'fecha_inicio'           => ['required', 'date', 'after_or_equal:fecha_factura'],

            // Fiador (opcionales pero con formato correcto si se llenan)
            'fiador_nombre'          => ['nullable', 'string', 'max:255'],
            // DUI salvadoreño 00000000-0
            'fiador_dui'             => ['nullable', 'regex:/^[0-9]{8}-[0-9]{1}$/'],
            'fiador_direccion'       => ['nullable', 'string', 'max:255'],
            // Teléfono: dígitos, espacios o guiones, entre 8 y 15 caracteres
            'fiador_telefono'        => ['nullable', 'regex:/^[0-9\-\s\+]{8,15}$/'],
        ], [
            'monto_capital_inicial.min'      => 'El monto capital inicial debe ser al menos 0.01.',
            'fiador_dui.regex'               => 'El DUI del fiador debe tener el formato 00000000-0.',
            'fiador_telefono.regex'          => 'El teléfono del fiador solo debe contener números, espacios, + o guiones, entre 8 y 15 caracteres.',
            'fecha_inicio.after_or_equal'    => 'La fecha de inicio del crédito no puede ser anterior a la fecha de la factura.',
        ]);

        return DB::transaction(function () use ($data, $request) {

            $cliente  = Cliente::findOrFail($data['cliente_id']);
            $politica = PoliticaCredito::findOrFail($data['politica_credito_id']);

            // 🔹 Política institucional: monto financiado mínimo $10.00
            $montoMinimoPolitica = 10.00;
            if ($data['monto_capital_inicial'] < $montoMinimoPolitica) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'monto_capital_inicial' => 'Según la política de crédito, el monto financiado mínimo es de $' . number_format($montoMinimoPolitica, 2) . '.',
                    ]);
            }

            // Validar límite de crédito
            $deudaActual = $cliente->cuentas()
                ->whereNotIn('estado', ['CANCELADO', 'INCOBRABLE'])
                ->sum('monto_capital_actual');

            $nuevoTotal = $deudaActual + $data['monto_capital_inicial'];

            if ($nuevoTotal > $cliente->limite_credito) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'monto_capital_inicial' => 'Se excede el límite de crédito autorizado para este cliente.'
                    ]);
            }

            // Calcular fecha de vencimiento
            $fechaInicio      = Carbon::parse($data['fecha_inicio']);
            $fechaVencimiento = $fechaInicio->copy()->addDays($politica->plazo_dias);

            // Crear fiador si aplica
            $fiadorId = null;
            if ($politica->requiere_fiador && $request->filled('fiador_nombre')) {
                $fiador = Fiador::create([
                    'nombre'    => $request->fiador_nombre,
                    'dui'       => $request->fiador_dui,
                    'direccion' => $request->fiador_direccion,
                    'telefono'  => $request->fiador_telefono,
                ]);
                $fiadorId = $fiador->id;
            }

            // Comisión inicial (porcentaje sobre capital inicial)
            $comisionInicial = 0;
            if ($politica->comision_inicial > 0) {
                $comisionInicial = round(
                    $data['monto_capital_inicial'] * ($politica->comision_inicial / 100),
                    2
                );
            }

            $cuenta = CuentaPorCobrar::create([
                'cliente_id'             => $cliente->id,
                'politica_credito_id'    => $politica->id,
                'fiador_id'              => $fiadorId,
                'usuario_responsable_id' => $data['usuario_responsable_id'],
                'numero_factura'         => $data['numero_factura'],
                'fecha_factura'          => $data['fecha_factura'],
                'tipo_documento'         => $data['tipo_documento'] ?? 'FACTURA_CREDITO',
                'monto_capital_inicial'  => $data['monto_capital_inicial'],
                'monto_capital_actual'   => $data['monto_capital_inicial'],
                'intereses_acumulados'   => 0,
                'comisiones_acumuladas'  => $comisionInicial,
                'fecha_inicio'           => $fechaInicio,
                'fecha_vencimiento'      => $fechaVencimiento,
                'estado'                 => 'VIGENTE',
            ]);

            BitacoraAccion::create([
                'user_id'    => Auth::id()
                    ?? $request->usuario_responsable_id
                    ?? 1,
                'accion'     => 'CREAR_CREDITO',
                'entidad'    => 'CuentaPorCobrar',
                'entidad_id' => $cuenta->id,
                'detalle'    => 'Cuenta creada desde factura ' . $cuenta->numero_factura,
            ]);

            return redirect()->route('cuentas.show', $cuenta)
                ->with('success', 'Cuenta por cobrar creada correctamente.');
        });
    }

    // VER DETALLE
    public function show(CuentaPorCobrar $cuenta)
    {
    // 1. ¡ACTUALIZAR DEUDA AL MOMENTO!
    $this->actualizarInteresesDiarios($cuenta); // <--- AGREGA ESTA LÍNEA

    // 2. Cargar relaciones
    $cuenta->load(['cliente', 'politica', 'fiador', 'responsable', 'pagos', 'embargo']);

    // 3. Revisar si cambió de estado (tu función original)
    $this->actualizarEstadoSegunPolitica($cuenta, true);

    return view('cuentas.show', compact('cuenta'));
    }

    // MARCAR INCOBRABLE
    public function marcarIncobrable(CuentaPorCobrar $cuenta)
    {
        if ($cuenta->estado === 'CANCELADO') {
            return back()->with('error', 'No se puede marcar como incobrable una cuenta cancelada.');
        }

        $cuenta->estado = 'INCOBRABLE';
        $cuenta->save();

        BitacoraAccion::create([
            'user_id'    => Auth::id(),
            'accion'     => 'MARCAR_INCOBRABLE',
            'entidad'    => 'CuentaPorCobrar',
            'entidad_id' => $cuenta->id,
            'detalle'    => 'Cuenta marcada como incobrable.',
        ]);

        return back()->with('success', 'Cuenta marcada como incobrable.');
    }

   // REACTIVAR INCOBRABLE (Ajustado a tu Política de Crédito)
    public function reactivarIncobrable(CuentaPorCobrar $cuenta, Request $request)
    {
        if ($cuenta->estado !== 'INCOBRABLE') {
            return back()->with('error', 'Solo se pueden reactivar cuentas incobrables.');
        }

        $data = $request->validate([
            'fecha_nuevo_inicio' => ['required', 'date'],
        ]);

        return DB::transaction(function () use ($cuenta, $data) {
            
            // Cargamos la política asociada
            $politica = $cuenta->politica;
            
            // FECHAS
            $fechaUltimoMovimiento = Carbon::parse($cuenta->updated_at); 
            $fechaNuevaReactivacion = Carbon::parse($data['fecha_nuevo_inicio']);
            
            // Validación de fechas para evitar negativos
            if ($fechaNuevaReactivacion->lt($fechaUltimoMovimiento)) {
                $fechaNuevaReactivacion = Carbon::now();
            }

            // 1. Calcular días transcurridos
            $diasTranscurridos = $fechaUltimoMovimiento->diffInDays($fechaNuevaReactivacion);

            // 2. Definir qué tasa usar
            // OPCIÓN A: Usar la Tasa Normal
            $tasaAnual = $politica->tasa_interes_anual; 

            // OPCIÓN B (Opcional): Si prefieres cobrar MORA por el tiempo muerto, descomenta la siguiente línea:
            // $tasaAnual = ($politica->tasa_mora_anual > 0) ? $politica->tasa_mora_anual : $politica->tasa_interes_anual;

            // 3. Calcular Interés
            $tasaDiaria = ($tasaAnual / 100) / 365;
            $interesGenerado = 0;
            
            if ($diasTranscurridos > 0) {
                $interesGenerado = round($cuenta->monto_capital_actual * $tasaDiaria * $diasTranscurridos, 2);
            }

            // 4. Actualizar saldos (Sumamos, no reemplazamos)
            $cuenta->intereses_acumulados += $interesGenerado;
            
            // Actualizar fechas y estado
            $cuenta->fecha_inicio = $fechaNuevaReactivacion;
            // Usamos 'plazo_dias' que vi en tu controlador de Politicas
            $cuenta->fecha_vencimiento = $fechaNuevaReactivacion->copy()->addDays($politica->plazo_dias);
            
            $cuenta->estado = 'VIGENTE';
            $cuenta->save();

            // 5. Bitácora
            BitacoraAccion::create([
                'user_id'    => Auth::id(),
                'accion'     => 'REACTIVAR_INCOBRABLE',
                'entidad'    => 'CuentaPorCobrar',
                'entidad_id' => $cuenta->id,
                'detalle'    => "Reactivada tras $diasTranscurridos días. Interés sumado: $$interesGenerado (Tasa: $tasaAnual%).",
            ]);

            return back()->with('success', "Cuenta reactivada. Se generaron $$interesGenerado de intereses por el tiempo inactivo.");
        });
    }
    // REFINANCIAR
    public function crearRefinanciamiento(CuentaPorCobrar $cuenta, Request $request)
    {
        if (!in_array($cuenta->estado, ['VIGENTE', 'EN_MORA'])) {
            return back()->with('error', 'Solo se pueden refinanciar cuentas vigentes o en mora.');
        }

        $data = $request->validate([
            'politica_credito_id' => ['required', 'exists:politicas_credito,id'],
            'fecha_inicio'        => ['required', 'date'],
            'motivo'              => ['nullable', 'string', 'max:500'],
        ]);

        return DB::transaction(function () use ($cuenta, $data) {

            $politicaNueva = PoliticaCredito::findOrFail($data['politica_credito_id']);
            $fechaInicio   = Carbon::parse($data['fecha_inicio']);
            $fechaVence    = $fechaInicio->copy()->addDays($politicaNueva->plazo_dias);

            $saldoRefinanciar = $cuenta->monto_capital_actual
                + $cuenta->intereses_acumulados
                + $cuenta->comisiones_acumuladas;

            if ($saldoRefinanciar <= 0) {
                return back()->with('error', 'No hay saldo por refinanciar.');
            }

            $cuentaNueva = CuentaPorCobrar::create([
                'cliente_id'             => $cuenta->cliente_id,
                'politica_credito_id'    => $politicaNueva->id,
                'fiador_id'              => $cuenta->fiador_id,
                'usuario_responsable_id' => $cuenta->usuario_responsable_id,
                'numero_factura'         => $cuenta->numero_factura . '-REF',
                'fecha_factura'          => $fechaInicio,
                'tipo_documento'         => 'REFINANCIAMIENTO',
                'monto_capital_inicial'  => $saldoRefinanciar,
                'monto_capital_actual'   => $saldoRefinanciar,
                'intereses_acumulados'   => 0,
                'comisiones_acumuladas'  => 0,
                'fecha_inicio'           => $fechaInicio,
                'fecha_vencimiento'      => $fechaVence,
                'estado'                 => 'VIGENTE',
            ]);

            $cuenta->estado = 'REFINANCIADO';
            $cuenta->save();

            Refinanciamiento::create([
                'cuenta_origen_id' => $cuenta->id,
                'cuenta_nueva_id'  => $cuentaNueva->id,
                'fecha'            => Carbon::now(),
                'motivo'           => $data['motivo'] ?? null,
            ]);

            BitacoraAccion::create([
                'user_id'    => Auth::id(),
                'accion'     => 'REFINANCIAR',
                'entidad'    => 'CuentaPorCobrar',
                'entidad_id' => $cuenta->id,
                'detalle'    => 'Refinanciada. Nueva cuenta ID: ' . $cuentaNueva->id,
            ]);

            return redirect()->route('cuentas.show', $cuentaNueva)
                ->with('success', 'Refinanciamiento creado correctamente.');
        });
    }

    // MARCAR EMBARGO
    public function marcarEmbargo(CuentaPorCobrar $cuenta, Request $request)
    {
        $data = $request->validate([
            'fecha_inicio'   => ['required', 'date'],
            'estado_proceso' => ['nullable', 'string', 'max:100'],
            'observaciones'  => ['nullable', 'string', 'max:1000'],
        ]);

        return DB::transaction(function () use ($cuenta, $data) {

            $cuenta->estado = 'EMBARGO';
            $cuenta->save();

            Embargo::updateOrCreate(
                ['cuenta_id' => $cuenta->id],
                [
                    'fecha_inicio'   => $data['fecha_inicio'],
                    'estado_proceso' => $data['estado_proceso'] ?? null,
                    'observaciones'  => $data['observaciones'] ?? null,
                ]
            );

            BitacoraAccion::create([
                'user_id'    => Auth::id(),
                'accion'     => 'MARCAR_EMBARGO',
                'entidad'    => 'CuentaPorCobrar',
                'entidad_id' => $cuenta->id,
                'detalle'    => 'Cuenta en embargo.',
            ]);

            return back()->with('success', 'Embargo registrado correctamente.');
        });
    }

    // FUNCIÓN DE APOYO PARA ESTADO
    protected function actualizarEstadoSegunPolitica(CuentaPorCobrar $cuenta, bool $persistir = true): void
    {
        $politica = $cuenta->politica;
        $hoy      = Carbon::today();
        $venc     = Carbon::parse($cuenta->fecha_vencimiento);

        $nuevoEstado = $cuenta->estado;

        if (!in_array($cuenta->estado, ['CANCELADO', 'INCOBRABLE', 'REFINANCIADO'])) {
            if ($hoy->greaterThan($venc->copy()->addDays($politica->dias_para_incobrable))) {
                $nuevoEstado = 'INCOBRABLE';
            } elseif ($hoy->greaterThan($venc->copy()->addDays($politica->dias_para_mora))) {
                $nuevoEstado = 'EN_MORA';
            } else {
                $nuevoEstado = 'VIGENTE';
            }
        }

        if ($nuevoEstado !== $cuenta->estado) {
            $cuenta->estado = $nuevoEstado;
            if ($persistir) {
                $cuenta->save();
            }
        }
    }

    public function edit(CuentaPorCobrar $cuenta)
    {
        $clientes  = Cliente::orderBy('nombre')->get();
        $politicas = PoliticaCredito::orderBy('nombre')->get();
        $usuarios  = User::orderBy('name')->get();

        return view('cuentas.edit', compact('cuenta', 'clientes', 'politicas', 'usuarios'));
    }

  
    private function actualizarInteresesDiarios(CuentaPorCobrar $cuenta)
    {
    // 1. Definir fechas
    $ultimoCalculo = Carbon::parse($cuenta->updated_at); // La última vez que se tocó la cuenta
    $hoy = Carbon::now();

    // Si la cuenta se actualizó hoy mismo, no hacemos nada para ahorrar recursos
    if ($ultimoCalculo->isSameDay($hoy)) {
        return;
    }

    // 2. Calcular días transcurridos desde el último cálculo
    $diasTranscurridos = $ultimoCalculo->diffInDays($hoy);

    if ($diasTranscurridos > 0 && $cuenta->monto_capital_actual > 0) {
        
        $politica = $cuenta->politica;
        $interesASumar = 0;

        // A. CASO 1: CUENTA VIGENTE (Interés Corriente Normal)
        if ($cuenta->estado == 'VIGENTE' && $hoy->lte($cuenta->fecha_vencimiento)) {
            // Capital * (TasaNormal / 365) * Días
            $tasaDiaria = ($politica->tasa_interes_anual / 100) / 365;
            $interesASumar = $cuenta->monto_capital_actual * $tasaDiaria * $diasTranscurridos;
        }
        
        // B. CASO 2: CUENTA VENCIDA (Interés Moratorio)
        // Si hoy ya pasó la fecha de vencimiento, cobramos MORA
        elseif ($hoy->greaterThan($cuenta->fecha_vencimiento)) {
            
            // Usamos la tasa de mora. Si es 0, usamos la normal (según tu política)
            $tasaAplicar = ($politica->tasa_mora_anual > 0) 
                            ? $politica->tasa_mora_anual 
                            : $politica->tasa_interes_anual;
            
            $tasaDiaria = ($tasaAplicar / 100) / 365;
            $interesASumar = $cuenta->monto_capital_actual * $tasaDiaria * $diasTranscurridos;

            // Actualizar estado visualmente si corresponde
            if ($cuenta->estado == 'VIGENTE') {
                $cuenta->estado = 'EN_MORA';
            }
        }

        // 3. Guardar cambios
        if ($interesASumar > 0) {
            $cuenta->intereses_acumulados += $interesASumar;
            // El save() actualiza el campo 'updated_at' automáticamente a "hoy",
            // así que mañana el cálculo partirá desde aquí.
            $cuenta->save(); 
        }
    }
}

    // ACTUALIZAR CUENTA
    public function update(Request $request, CuentaPorCobrar $cuenta)
    {
        $data = $request->validate([
            'cliente_id'             => ['required', 'exists:clientes,id'],
            'politica_credito_id'    => ['required', 'exists:politicas_credito,id'],
            'usuario_responsable_id' => ['required', 'exists:users,id'],
            'numero_factura'         => ['required', 'string', 'max:50'],
            'fecha_factura'          => ['required', 'date'],
            'tipo_documento'         => ['required', 'string', 'max:50'],
            'fecha_inicio'           => ['required', 'date', 'after_or_equal:fecha_factura'],
            'estado'                 => ['required', 'in:VIGENTE,EN_MORA,INCOBRABLE,REFINANCIADO,CANCELADO'],

            // campos de fiador opcionales pero validados
            'fiador_nombre'          => ['nullable', 'string', 'max:255'],
            'fiador_dui'             => ['nullable', 'regex:/^[0-9]{8}-[0-9]{1}$/'],
            'fiador_direccion'       => ['nullable', 'string', 'max:255'],
            'fiador_telefono'        => ['nullable', 'regex:/^[0-9\-\s\+]{8,15}$/'],
        ], [
            'fiador_dui.regex'            => 'El DUI del fiador debe tener el formato 00000000-0.',
            'fiador_telefono.regex'       => 'El teléfono del fiador solo debe contener números, espacios, + o guiones, entre 8 y 15 caracteres.',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio del crédito no puede ser anterior a la fecha de la factura.',
        ]);

        return DB::transaction(function () use ($data, $cuenta) {

            // actualizar datos básicos
            $cuenta->cliente_id             = $data['cliente_id'];
            $cuenta->politica_credito_id    = $data['politica_credito_id'];
            $cuenta->usuario_responsable_id = $data['usuario_responsable_id'];
            $cuenta->numero_factura         = $data['numero_factura'];
            $cuenta->fecha_factura          = $data['fecha_factura'];
            $cuenta->tipo_documento         = $data['tipo_documento'];
            $cuenta->fecha_inicio           = $data['fecha_inicio'];
            $cuenta->estado                 = $data['estado'];

            // recalcular fecha de vencimiento por si cambiaste política o fecha_inicio
            $politica = $cuenta->politica; // relación
            if ($politica && $cuenta->fecha_inicio) {
                $inicio = Carbon::parse($cuenta->fecha_inicio);
                $cuenta->fecha_vencimiento = $inicio->copy()->addDays($politica->plazo_dias);
            }

            // actualizar fiador si manejas la relación embebida en la cuenta
            if ($cuenta->fiador) {
                $cuenta->fiador->update([
                    'nombre'    => $data['fiador_nombre']    ?? $cuenta->fiador->nombre,
                    'dui'       => $data['fiador_dui']       ?? $cuenta->fiador->dui,
                    'direccion' => $data['fiador_direccion'] ?? $cuenta->fiador->direccion,
                    'telefono'  => $data['fiador_telefono']  ?? $cuenta->fiador->telefono,
                ]);
            }

            $cuenta->save();

            return redirect()
                ->route('cuentas.show', $cuenta)
                ->with('success', 'Cuenta actualizada correctamente.');
        });
    }




    
}
