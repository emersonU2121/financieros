<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

      public function __construct()
    {
        $this->middleware('auth');
    }
    // LISTADO
    public function index()
    {
        $usuarios = User::with('role')
            ->orderBy('name')
            ->paginate(10);

        return view('usuarios.index', compact('usuarios'));
    }

    // FORM CREAR
    public function create()
    {
        $roles = Role::orderBy('nombre')->get();
        return view('usuarios.create', compact('roles'));
    }

    // GUARDAR
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role_id'  => 'nullable|exists:roles,id',
            'activo'   => 'sometimes|boolean',
        ]);

        $data['activo']   = $request->has('activo');
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    // FORM EDITAR
    public function edit(User $usuario)
    {
        $roles = Role::orderBy('nombre')->get();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    // ACTUALIZAR
    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id'  => 'nullable|exists:roles,id',
            'activo'   => 'sometimes|boolean',
        ]);

        $data['activo'] = $request->has('activo');

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $usuario->update($data);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // DESACTIVAR (no borramos duro por ahora)
    public function destroy(User $usuario)
    {
        $usuario->update(['activo' => false]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }
}
