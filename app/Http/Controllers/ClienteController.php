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

    // CORRECCIÓN: Usar la query que construiste, no crear una nueva
    $clientes = $query->orderBy('nombre')->paginate(10);

    return view('clientes.index', compact('clientes'));
}

    // FORM CREAR
    public function create()
    {
        $clasificaciones = ClasificacionCliente::all();
        return view('clientes.create', compact('clasificaciones'));
    }

    // GUARDAR
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo'                => 'required|in:NATURAL,JURIDICA',
            'codigo'              => 'required|string|max:50|unique:clientes,codigo',
            'nombre'              => 'required|string|max:255',
            'giro'                => 'nullable|string|max:255',
            'direccion'           => 'nullable|string|max:255',
            'zona'                => 'nullable|string|max:100',
            'telefono'            => 'nullable|string|max:30',
            'dui'                 => 'nullable|string|max:20',
            'nit'                 => 'nullable|string|max:20',
            'nrc'                 => 'nullable|string|max:20',
            'estado_civil'        => 'nullable|string|max:50',
            'lugar_trabajo'       => 'nullable|string|max:255',
            'ingresos_mensuales'  => 'nullable|numeric|min:0',
            'egresos_mensuales'   => 'nullable|numeric|min:0',
            'total_activos'       => 'nullable|numeric|min:0',
            'total_pasivos'       => 'nullable|numeric|min:0',
            'ventas_anuales'      => 'nullable|numeric|min:0',
            'utilidad_neta'       => 'nullable|numeric|min:0',
            'clasificacion_id'    => 'nullable|exists:clasificaciones_clientes,id',
            'limite_credito'      => 'required|numeric|min:0',
            'activo'              => 'sometimes|boolean',
        ]);

        $data['activo'] = $request->has('activo') ? (bool)$request->activo : true;

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
        $data = $request->validate([
            'tipo'                => 'required|in:NATURAL,JURIDICA',
            'codigo'              => 'required|string|max:50|unique:clientes,codigo,' . $cliente->id,
            'nombre'              => 'required|string|max:255',
            'giro'                => 'nullable|string|max:255',
            'direccion'           => 'nullable|string|max:255',
            'zona'                => 'nullable|string|max:100',
            'telefono'            => 'nullable|string|max:30',
            'dui'                 => 'nullable|string|max:20',
            'nit'                 => 'nullable|string|max:20',
            'nrc'                 => 'nullable|string|max:20',
            'estado_civil'        => 'nullable|string|max:50',
            'lugar_trabajo'       => 'nullable|string|max:255',
            'ingresos_mensuales'  => 'nullable|numeric|min:0',
            'egresos_mensuales'   => 'nullable|numeric|min:0',
            'total_activos'       => 'nullable|numeric|min:0',
            'total_pasivos'       => 'nullable|numeric|min:0',
            'ventas_anuales'      => 'nullable|numeric|min:0',
            'utilidad_neta'       => 'nullable|numeric|min:0',
            'clasificacion_id'    => 'nullable|exists:clasificaciones_clientes,id',
            'limite_credito'      => 'required|numeric|min:0',
            'activo'              => 'sometimes|boolean',
        ]);

        $data['activo'] = $request->has('activo') ? (bool)$request->activo : $cliente->activo;

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
