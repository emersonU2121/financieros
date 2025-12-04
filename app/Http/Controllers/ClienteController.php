<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClasificacionCliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // LISTAR CLIENTES
    public function index(Request $request)
    {
        $query = Cliente::query()->with('clasificacion');

        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('codigo', 'like', "%{$buscar}%")
                    ->orWhere('nit', 'like', "%{$buscar}%")
                    ->orWhere('nrc', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->get('tipo')); // NATURAL / JURIDICA
        }

        // Filtro por estado (solo activos / solo inactivos)
        if ($request->filled('solo_activos')) {
            // '1' = activos, '0' = inactivos
            $query->where('activo', $request->solo_activos === '1');
        }

        $clientes = $query->orderBy('nombre')->paginate(10);

        return view('clientes.index', compact('clientes'));
    }

    // FORM CREAR
    public function create()
    {
        $clasificaciones = ClasificacionCliente::all();
        return view('clientes.create', compact('clasificaciones'));
    }

    // ---------------- REGLAS COMUNES ----------------
    protected function reglas(?Cliente $cliente = null): array
    {
        $id = $cliente?->id;

        return [
            'tipo'               => 'required|in:NATURAL,JURIDICA',
            'codigo'             => 'required|string|max:50|unique:clientes,codigo,' . ($id ?? 'NULL') . ',id',

            // Solo letras y espacios (sin números)
            'nombre'             => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s]+$/u',
            ],

            'giro'               => 'nullable|string|max:255',
            'direccion'          => 'nullable|string|max:255',
            'zona'               => 'nullable|string|max:100',

            // Teléfono solo números y guiones
            'telefono'           => [
                'nullable',
                'max:30',
                'regex:/^[0-9\-]+$/',
            ],

            // DUI con formato 00000000-0, sólo números y el guion
            'dui'                => [
                'nullable',
                'max:10',
                'regex:/^\d{8}-\d{1}$/',
            ],

            // NIT y NRC: sólo números y guiones (no letras)
            'nit'                => [
                'nullable',
                'max:20',
                'regex:/^[0-9\-]+$/',
            ],
            'nrc'                => [
                'nullable',
                'max:20',
                'regex:/^[0-9\-]+$/',
            ],

            // Estado civil solo letras y espacios
            'estado_civil'       => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[\pL\s]+$/u',
            ],

            'lugar_trabajo'      => 'nullable|string|max:255',

            'ingresos_mensuales' => 'nullable|numeric|min:0',
            'egresos_mensuales'  => 'nullable|numeric|min:0',
            'total_activos'      => 'nullable|numeric|min:0',
            'total_pasivos'      => 'nullable|numeric|min:0',
            'ventas_anuales'     => 'nullable|numeric|min:0',
            'utilidad_neta'      => 'nullable|numeric|min:0',

            'clasificacion_id'   => 'nullable|exists:clasificaciones_clientes,id',
            'limite_credito'     => 'required|numeric|min:0',
            'activo'             => 'sometimes|boolean',
        ];
    }

    protected function mensajes(): array
    {
        return [
            'tipo.required'   => 'El tipo de cliente es obligatorio.',
            'tipo.in'         => 'El tipo de cliente debe ser NATURAL o JURIDICA.',

            'codigo.required' => 'El código del cliente es obligatorio.',
            'codigo.unique'   => 'Ya existe un cliente con ese código.',
            'codigo.max'      => 'El código no debe tener más de 50 caracteres.',

            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'nombre.regex'    => 'El nombre solo puede contener letras y espacios.',
            'nombre.max'      => 'El nombre no debe tener más de 255 caracteres.',

            'telefono.regex'  => 'El teléfono solo puede contener números y guiones.',
            'telefono.max'    => 'El teléfono no debe tener más de 30 caracteres.',

            'dui.regex'       => 'El DUI debe tener el formato 00000000-0 y solo números.',
            'dui.max'         => 'El DUI no debe tener más de 10 caracteres.',

            'nit.regex'       => 'El NIT solo puede contener números y guiones.',
            'nrc.regex'       => 'El NRC solo puede contener números y guiones.',

            'estado_civil.regex' => 'El estado civil solo puede contener letras y espacios.',

            'limite_credito.required' => 'El límite de crédito es obligatorio.',
            'limite_credito.numeric'  => 'El límite de crédito debe ser un número.',
            'limite_credito.min'      => 'El límite de crédito no puede ser negativo.',
        ];
    }

    // GUARDAR
    public function store(Request $request)
    {
        $data = $request->validate(
            $this->reglas(),
            $this->mensajes()
        );

        $data['activo'] = $request->has('activo') ? (bool) $request->activo : true;

        Cliente::create($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    // VER DETALLE
    public function show(Cliente $cliente)
    {
        $cliente->load(['clasificacion', 'cuentas.pagos']);
        return view('clientes.show', compact('cliente'));
    }

    // FORM EDITAR
    public function edit(Cliente $cliente)
    {
        $clasificaciones = ClasificacionCliente::all();
        return view('clientes.edit', compact('cliente', 'clasificaciones'));
    }

    // ACTUALIZAR
    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate(
            $this->reglas($cliente),
            $this->mensajes()
        );

        $data['activo'] = $request->has('activo') ? (bool) $request->activo : $cliente->activo;

        $cliente->update($data);

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    // DESACTIVAR (no borrar duro)
    public function destroy(Cliente $cliente)
    {
        $cliente->update(['activo' => false]);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente desactivado correctamente.');
    }
}
