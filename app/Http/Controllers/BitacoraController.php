<?php

namespace App\Http\Controllers;

use App\Models\BitacoraAccion;
use App\Models\User;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Si quieres que sólo ADMIN vea la bitácora, podrías validar aquí el rol.
    }

    public function index(Request $request)
    {
        $query = BitacoraAccion::query()->with('user')->latest();

        // Filtros
        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }

        if ($request->filled('accion')) {
            $query->where('accion', 'like', '%' . $request->accion . '%');
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $registros  = $query->paginate(15)->withQueryString();
        $usuarios   = User::orderBy('name')->get();

        return view('bitacora.index', compact('registros', 'usuarios'));
    }
}
