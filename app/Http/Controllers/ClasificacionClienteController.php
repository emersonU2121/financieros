<?php

namespace App\Http\Controllers;

use App\Models\ClasificacionCliente;
use Illuminate\Http\Request;

class ClasificacionClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $clasificaciones = ClasificacionCliente::orderBy('codigo')->get();
        return view('clasificaciones.index', compact('clasificaciones'));
    }

    public function create()
    {
        return view('clasificaciones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo'      => 'required|string|max:5|unique:clasificaciones_clientes,codigo',
            'descripcion' => 'required|string|max:255',
        ]);

        ClasificacionCliente::create($data);

        return redirect()->route('clasificaciones.index')
            ->with('success', 'Clasificación creada correctamente.');
    }

    public function edit(ClasificacionCliente $clasificacion)
    {
        return view('clasificaciones.edit', compact('clasificacion'));
    }

    public function update(Request $request, ClasificacionCliente $clasificacion)
    {
        $data = $request->validate([
            'codigo'      => 'required|string|max:5|unique:clasificaciones_clientes,codigo,' . $clasificacion->id,
            'descripcion' => 'required|string|max:255',
        ]);

        $clasificacion->update($data);

        return redirect()->route('clasificaciones.index')
            ->with('success', 'Clasificación actualizada correctamente.');
    }

    public function destroy(ClasificacionCliente $clasificacion)
    {
        if ($clasificacion->clientes()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay clientes asociados.');
        }

        $clasificacion->delete();

        return redirect()->route('clasificaciones.index')
            ->with('success', 'Clasificación eliminada correctamente.');
    }
}
