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

    // Puedes cambiar a paginate(10) si quieres paginación
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
            'cliente_id'            => 'required|exists:clientes,id',
            'politica_credito_id'   => 'required|exists:politicas_credito,id',
            'usuario_responsable_id'=> 'required|exists:users,id',
            'numero_factura'        => 'required|string|max:50',
            'fecha_factura'         => 'required|date',
            'tipo_documento'        => 'nullable|string|max:100',
            'monto_capital_inicial' => 'required|numeric|min:0.01',
            'fecha_inicio'          => 'required|date',
            'fiador_nombre'         => 'nullable|string|max:255',
            'fiador_dui'            => 'nullable|string|max:20',
            'fiador_direccion'      => 'nullable|string|max:255',
            'fiador_telefono'       => 'nullable|string|max:30',
        ]);

        return DB::transaction(function () use ($data, $request) {

            $cliente  = Cliente::findOrFail($data['cliente_id']);
            $politica = PoliticaCredito::findOrFail($data['politica_credito_id']);

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
                     ?? $request->usuario_responsable_id   // usa el analista
                     ?? 1,                                 // o ID 1 como fallback
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
        $cuenta->load(['cliente', 'politica', 'fiador', 'responsable', 'pagos', 'embargo']);

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

    // REACTIVAR INCOBRABLE
    public function reactivarIncobrable(CuentaPorCobrar $cuenta, Request $request)
    {
        if ($cuenta->estado !== 'INCOBRABLE') {
            return back()->with('error', 'Solo se pueden reactivar cuentas incobrables.');
        }

        $data = $request->validate([
            'fecha_nuevo_inicio' => 'required|date',
        ]);

        $politica     = $cuenta->politica;
        $fechaInicio  = Carbon::parse($data['fecha_nuevo_inicio']);
        $fechaVence   = $fechaInicio->copy()->addDays($politica->plazo_dias);

        $cuenta->fecha_inicio          = $fechaInicio;
        $cuenta->fecha_vencimiento     = $fechaVence;
        $cuenta->intereses_acumulados  = 0;
        $cuenta->comisiones_acumuladas = 0;
        $cuenta->estado                = 'VIGENTE';
        $cuenta->save();

        BitacoraAccion::create([
            'user_id'    => Auth::id(),
            'accion'     => 'REACTIVAR_INCOBRABLE',
            'entidad'    => 'CuentaPorCobrar',
            'entidad_id' => $cuenta->id,
            'detalle'    => 'Cuenta incobrable reactivada.',
        ]);

        return back()->with('success', 'Cuenta reactivada correctamente.');
    }

    // REFINANCIAR
    public function crearRefinanciamiento(CuentaPorCobrar $cuenta, Request $request)
    {
        if (!in_array($cuenta->estado, ['VIGENTE', 'EN_MORA'])) {
            return back()->with('error', 'Solo se pueden refinanciar cuentas vigentes o en mora.');
        }

        $data = $request->validate([
            'politica_credito_id' => 'required|exists:politicas_credito,id',
            'fecha_inicio'        => 'required|date',
            'motivo'              => 'nullable|string|max:500',
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
            'fecha_inicio'   => 'required|date',
            'estado_proceso' => 'nullable|string|max:100',
            'observaciones'  => 'nullable|string|max:1000',
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

    // ACTUALIZAR CUENTA
    public function update(Request $request, CuentaPorCobrar $cuenta)
    {
        $data = $request->validate([
            'cliente_id'            => 'required|exists:clientes,id',
            'politica_credito_id'   => 'required|exists:politicas_credito,id',
            'usuario_responsable_id'=> 'required|exists:users,id',
            'numero_factura'        => 'required|string|max:50',
            'fecha_factura'         => 'required|date',
            'tipo_documento'        => 'required|string|max:50',
            'fecha_inicio'          => 'required|date',
            'estado'                => 'required|in:VIGENTE,EN_MORA,INCOBRABLE,REFINANCIADO,CANCELADO',

            // campos de fiador opcionales
            'fiador_nombre'         => 'nullable|string|max:255',
            'fiador_dui'            => 'nullable|string|max:20',
            'fiador_direccion'      => 'nullable|string|max:255',
            'fiador_telefono'       => 'nullable|string|max:30',
        ]);

        return DB::transaction(function () use ($data, $cuenta) {

            // actualizar datos básicos
            $cuenta->cliente_id            = $data['cliente_id'];
            $cuenta->politica_credito_id   = $data['politica_credito_id'];
            $cuenta->usuario_responsable_id= $data['usuario_responsable_id'];
            $cuenta->numero_factura        = $data['numero_factura'];
            $cuenta->fecha_factura         = $data['fecha_factura'];
            $cuenta->tipo_documento        = $data['tipo_documento'];
            $cuenta->fecha_inicio          = $data['fecha_inicio'];
            $cuenta->estado                = $data['estado'];

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
