@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Usuarios del sistema</h1>
            <p class="page-subtitle">
                Gestión de cuentas de acceso y asignación de roles.
            </p>
        </div>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            + Nuevo usuario
        </a>
    </div>

    @include('partials.alerts')

    <section class="card">
        <div class="card-header-row">
            <h2 class="card-title">Listado de usuarios</h2>
            <span class="badge">
                {{ $usuarios->total() }} registros
            </span>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->role->nombre ?? 'Sin rol' }}</td>
                        <td>
                            @if($usuario->activo)
                                <span class="status-pill status-vigente">Activo</span>
                            @else
                                <span class="status-pill status-incobrable">Inactivo</span>
                            @endif
                        </td>
                        <td class="table-actions">
                            <a href="{{ route('usuarios.edit', $usuario) }}"
                               class="btn btn-xs btn-secondary">
                                Editar
                            </a>

                            <form action="{{ route('usuarios.destroy', $usuario) }}"
                                  method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-xs btn-light"
                                        onclick="return confirm('¿Desactivar este usuario?')">
                                    Desactivar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <p class="empty-state">No hay usuarios registrados aún.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-20">
            {{ $usuarios->links() }}
        </div>
    </section>
</div>
@endsection
