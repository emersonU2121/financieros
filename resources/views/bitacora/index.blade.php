@extends('layouts.app')

@section('title', 'Bitácora de acciones')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Bitácora de acciones</h1>
            <p class="page-subtitle">
                Registro de operaciones realizadas por los usuarios en el sistema.
            </p>
        </div>
    </div>

    {{-- Filtros --}}
    <section class="card card-filters">
        <h2 class="card-title">Filtros de búsqueda</h2>

        <form method="GET" action="{{ route('bitacora.index') }}" class="filters-grid">
            <div class="form-group">
                <label for="usuario_id">Usuario</label>
                <select name="usuario_id" id="usuario_id" class="input">
                    <option value="">Todos</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}"
                            {{ request('usuario_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="accion">Acción</label>
                <input type="text" name="accion" id="accion"
                       class="input"
                       value="{{ request('accion') }}"
                       placeholder="CREAR_CREDITO, REGISTRAR_PAGO, etc.">
            </div>

            <div class="form-group">
                <label for="desde">Desde</label>
                <input type="date" name="desde" id="desde"
                       class="input"
                       value="{{ request('desde') }}">
            </div>

            <div class="form-group">
                <label for="hasta">Hasta</label>
                <input type="date" name="hasta" id="hasta"
                       class="input"
                       value="{{ request('hasta') }}">
            </div>

            <div class="filters-actions">
                <button type="submit" class="btn btn-secondary">Filtrar</button>
                <a href="{{ route('bitacora.index') }}" class="btn btn-light">Limpiar</a>
            </div>
        </form>
    </section>

    {{-- Tabla --}}
    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Registro de acciones</h2>
            <span class="badge">
                {{ $registros->total() }} registros
            </span>
        </div>

        @if($registros->isEmpty())
            <p class="empty-state">
                No hay registros en la bitácora con los filtros actuales.
            </p>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Fecha / Hora</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Entidad</th>
                        <th>ID Entidad</th>
                        <th>Detalle</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($registros as $item)
                        <tr>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                {{ optional($item->user)->name ?? 'Usuario eliminado' }}
                                <br>
                                <span class="text-muted small">
                                    {{ optional($item->user)->email }}
                                </span>
                            </td>
                            <td><span class="badge">{{ $item->accion }}</span></td>
                            <td>{{ $item->entidad ?? '-' }}</td>
                            <td>{{ $item->entidad_id ?? '-' }}</td>
                            <td>{{ $item->detalle ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-20">
                {{ $registros->links() }}
            </div>
        @endif
    </section>
@endsection
