<?php

namespace App\Http\Controllers;

use App\Models\PoliticaCredito;
use Illuminate\Http\Request;

class PoliticaCreditoController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
       
    

    public function index()
    {
        $politicas = PoliticaCredito::orderBy('nombre')->paginate(10);
        return view('politicas.index', compact('politicas'));
    }

    public function create()
    {
        return view('politicas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:255',
            'plazo_dias'          => 'required|integer|min:1',
            'tasa_interes_anual'  => 'required|numeric|min:0',
            'tasa_mora_anual'     => 'nullable|numeric|min:0',
            'comision_inicial'    => 'nullable|numeric|min:0',
            'dias_gracia'         => 'nullable|integer|min:0',
            'dias_para_mora'      => 'nullable|integer|min:0',
            'dias_para_incobrable'=> 'nullable|integer|min:1',
            'requiere_fiador'     => 'sometimes|boolean',
        ]);

        $data['tasa_mora_anual']   = $data['tasa_mora_anual'] ?? 0;
        $data['comision_inicial']  = $data['comision_inicial'] ?? 0;
        $data['dias_gracia']       = $data['dias_gracia'] ?? 0;
        $data['dias_para_mora']    = $data['dias_para_mora'] ?? 0;
        $data['dias_para_incobrable'] = $data['dias_para_incobrable'] ?? 90;
        $data['requiere_fiador']   = $request->has('requiere_fiador');

        PoliticaCredito::create($data);

        return redirect()->route('politicas.index')
            ->with('success', 'Política de crédito creada correctamente.');
    }

    public function edit(PoliticaCredito $politica)
    {
        return view('politicas.edit', compact('politica'));
    }

    public function update(Request $request, PoliticaCredito $politica)
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:255',
            'plazo_dias'          => 'required|integer|min:1',
            'tasa_interes_anual'  => 'required|numeric|min:0',
            'tasa_mora_anual'     => 'nullable|numeric|min:0',
            'comision_inicial'    => 'nullable|numeric|min:0',
            'dias_gracia'         => 'nullable|integer|min:0',
            'dias_para_mora'      => 'nullable|integer|min:0',
            'dias_para_incobrable'=> 'nullable|integer|min:1',
            'requiere_fiador'     => 'sometimes|boolean',
        ]);

        $data['tasa_mora_anual']   = $data['tasa_mora_anual'] ?? 0;
        $data['comision_inicial']  = $data['comision_inicial'] ?? 0;
        $data['dias_gracia']       = $data['dias_gracia'] ?? 0;
        $data['dias_para_mora']    = $data['dias_para_mora'] ?? 0;
        $data['dias_para_incobrable'] = $data['dias_para_incobrable'] ?? 90;
        $data['requiere_fiador']   = $request->has('requiere_fiador');

        $politica->update($data);

        return redirect()->route('politicas.index')
            ->with('success', 'Política de crédito actualizada correctamente.');
    }

    public function destroy(PoliticaCredito $politica)
    {
        if ($politica->cuentas()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay créditos asociados.');
        }

        $politica->delete();

        return redirect()->route('politicas.index')
            ->with('success', 'Política de crédito eliminada correctamente.');
    }
}


