@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="page">
    {{-- ENCABEZADO --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Clientes</h1>
            <p class="page-subtitle">
                Catálogo de clientes que pueden tener cuentas por cobrar. 
                Administra aquí su información básica, clasificación y límite de crédito.
            </p>
        </div>

        <a href="{{ route('clientes.create') }}" class="btn btn-primary">
            + Nuevo cliente
        </a>
    </div>

    {{-- FILTROS --}}
    <div class="card card-filters">
        <div class="card-header-row">
            <h2 class="card-title">Filtros de búsqueda</h2>
        </div>

        <form method="GET" action="{{ route('clientes.index') }}">
            <div class="filters-grid">
                {{-- Buscar --}}
                <div class="form-group">
                    <label for="buscar">Buscar</label>
                    <input
                        type="text"
                        id="buscar"
                        name="buscar"
                        class="input"
                        placeholder="Nombre, código, NIT o NRC"
                        value="{{ request('buscar') }}"
                    >
                </div>

                {{-- Tipo de cliente --}}
                <div class="form-group">
                    <label for="tipo">Tipo de cliente</label>
                    <select id="tipo" name="tipo" class="input">
                        <option value="">Todos</option>
                        <option value="NATURAL"  {{ request('tipo') === 'NATURAL' ? 'selected' : '' }}>Persona natural</option>
                        <option value="JURIDICA" {{ request('tipo') === 'JURIDICA' ? 'selected' : '' }}>Persona jurídica</option>
                    </select>
                </div>

                {{-- Solo activos --}}
                <div class="form-group">
                    <label for="solo_activos">Estado</label>
                    <select id="solo_activos" name="solo_activos" class="input">
                        <option value="">Todos</option>
                        <option value="1" {{ request('solo_activos') === '1' ? 'selected' : '' }}>Solo activos</option>
                        <option value="0" {{ request('solo_activos') === '0' ? 'selected' : '' }}>Solo inactivos</option>
                    </select>
                </div>

                {{-- Botones --}}
                <div class="filters-actions">
                    <button type="submit" class="btn btn-secondary">
                        Aplicar filtros
                    </button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-light">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- LISTADO --}}
    <div class="card">
        <div class="card-header-row">
            <h2 class="card-title">Listado de clientes</h2>
            <span class="badge">{{ $clientes->total() }} registros</span>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre / Razón social</th>
                        <th>Tipo</th>
                        <th>Clasificación</th>
                        <th>Límite de crédito (US$)</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th style="width: 180px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($clientes as $cliente)
                    <tr>
                        {{-- Código --}}
                        <td>{{ $cliente->codigo }}</td>

                        {{-- Nombre --}}
                        <td>
                            <strong>{{ $cliente->nombre }}</strong><br>
                            <span class="text-sm" style="font-size: 0.78rem; color: var(--text-muted);">
                                DUI: {{ $cliente->dui ?? '—' }} · NRC: {{ $cliente->nrc ?? '—' }}
                            </span>
                        </td>

                        {{-- Tipo --}}
                        <td>
                            {{ $cliente->tipo === 'JURIDICA' ? 'Jurídica' : 'Natural' }}
                        </td>

                        {{-- Clasificación --}}
                        <td>
                            {{ optional($cliente->clasificacion)->nombre ?? 'Sin clasificar' }}
                        </td>

                        {{-- Límite de crédito --}}
                        <td>
                            US$ {{ number_format($cliente->limite_credito, 2) }}
                        </td>

                        {{-- Teléfono --}}
                        <td>{{ $cliente->telefono ?? '—' }}</td>

                        {{-- Estado --}}
                        <td>
                            @if ($cliente->activo)
                                <span class="status-pill status-vigente">Activo</span>
                            @else
                                <span class="status-pill status-incobrable">Inactivo</span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-xs btn-light">
                                    Ver
                                </a>
                                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-xs btn-secondary">
                                    Editar
                                </a>
                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="btn btn-xs btn-light"
                                        onclick="return confirm('¿Deseas desactivar este cliente?')"
                                    >
                                        Desactivar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <p class="empty-state">
                                No se encontraron clientes. Registra uno nuevo con el botón
                                <strong>“+ Nuevo cliente”</strong>.
                            </p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN --}}
        <div class="mt-20">
            {{ $clientes->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
