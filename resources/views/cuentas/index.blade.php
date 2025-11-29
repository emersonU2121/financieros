@extends('layouts.app')

@section('title', 'Cuentas por Cobrar')

@section('content')
<div class="page">

    {{-- HEADER PRINCIPAL --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Cuentas por Cobrar</h1>
            <p class="page-subtitle">
                Créditos originados en ventas a crédito. Control de intereses, comisiones y estado de la cartera.
            </p>
        </div>

        <a href="{{ route('cuentas.create') }}" class="btn btn-primary">
            + Nueva cuenta
        </a>
    </div>

    {{-- FILTROS --}}
    <section class="card card-filters">
        <div class="card-header-row">
            <h2 class="card-title">Filtros de búsqueda</h2>
            <span class="badge">Refina la cartera por cliente, estado y rango de fechas</span>
        </div>

        <form method="GET" action="{{ route('cuentas.index') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label for="cliente">Cliente</label>
                    <select name="cliente" id="cliente" class="input">
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                {{ (request('cliente') == $cliente->id) ? 'selected' : '' }}>
                                {{ $cliente->nombre_completo ?? $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="input">
                        <option value="">Todos</option>
                        <option value="VIGENTE"      {{ request('estado')=='VIGENTE' ? 'selected' : '' }}>Vigente</option>
                        <option value="EN_MORA"      {{ request('estado')=='EN_MORA' ? 'selected' : '' }}>En mora</option>
                        <option value="INCOBRABLE"   {{ request('estado')=='INCOBRABLE' ? 'selected' : '' }}>Incobrable</option>
                        <option value="REFINANCIADO" {{ request('estado')=='REFINANCIADO' ? 'selected' : '' }}>Refinanciado</option>
                        <option value="CANCELADO"    {{ request('estado')=='CANCELADO' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="desde">Desde (fecha inicio)</label>
                    <input type="date" name="desde" id="desde"
                           value="{{ request('desde') }}" class="input">
                </div>

                <div class="form-group">
                    <label for="hasta">Hasta (fecha inicio)</label>
                    <input type="date" name="hasta" id="hasta"
                           value="{{ request('hasta') }}" class="input">
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-secondary">Filtrar</button>
                    <a href="{{ route('cuentas.index') }}" class="btn btn-light">Limpiar</a>
                </div>
            </div>
        </form>
    </section>

    {{-- TABLA DE RESULTADOS --}}
    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Cartera de créditos</h2>
            <span class="badge">
                {{ $cuentas->count() }} {{ \Illuminate\Support\Str::plural('registro', $cuentas->count()) }}
            </span>
        </div>

        @if($cuentas->isEmpty())
            <p class="empty-state">
                No hay cuentas registradas con los filtros actuales.
            </p>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>N° Factura</th>
                            <th>Cliente</th>
                            <th>Fecha inicio</th>
                            <th>Vencimiento</th>
                            <th>Capital actual</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($cuentas as $cuenta)
                        <tr>
                            <td>{{ $cuenta->numero_factura }}</td>
                            <td>{{ $cuenta->cliente->nombre_completo ?? $cuenta->cliente->nombre }}</td>
                            <td>{{ optional($cuenta->fecha_inicio)->format('d/m/Y') }}</td>
                            <td>{{ optional($cuenta->fecha_vencimiento)->format('d/m/Y') }}</td>
                            <td>${{ number_format($cuenta->monto_capital_actual, 2) }}</td>
                            <td>
                                <span class="status-pill status-{{ strtolower($cuenta->estado) }}">
                                    {{ ucfirst(strtolower($cuenta->estado)) }}
                                </span>
                            </td>
                            <td class="table-actions">
                                <a href="{{ route('cuentas.show', $cuenta) }}" class="btn btn-xs btn-outline">
                                    Ver
                                </a>
                                <a href="{{ route('cuentas.edit', $cuenta) }}" class="btn btn-xs btn-secondary">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
