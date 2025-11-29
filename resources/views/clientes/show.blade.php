@extends('layouts.app')

@section('title', 'Detalle de cliente')

@section('content')
<div class="page">

    {{-- Encabezado --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $cliente->codigo }} · {{ $cliente->nombre }}
            </h1>
            <p class="page-subtitle">
                Tipo: {{ $cliente->tipo === 'NATURAL' ? 'Persona natural' : 'Persona jurídica' }}
                @if($cliente->clasificacion)
                    · Clasificación: {{ $cliente->clasificacion->nombre }}
                @endif
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-secondary">
                Editar
            </a>
            <a href="{{ route('clientes.index') }}" class="btn btn-light">
                ← Volver al listado
            </a>
        </div>
    </div>

    {{-- Datos generales --}}
    <section class="card">
        <h2 class="card-title">Datos generales</h2>

        <div class="form-grid">
            <div>
                <p class="text-muted">Código interno</p>
                <p>{{ $cliente->codigo }}</p>
            </div>

            <div>
                <p class="text-muted">Giro / actividad</p>
                <p>{{ $cliente->giro ?? '—' }}</p>
            </div>

            <div>
                <p class="text-muted">Teléfono</p>
                <p>{{ $cliente->telefono ?? '—' }}</p>
            </div>

            <div>
                <p class="text-muted">Dirección</p>
                <p>{{ $cliente->direccion ?? '—' }}</p>
            </div>

            <div>
                <p class="text-muted">Zona</p>
                <p>{{ $cliente->zona ?? '—' }}</p>
            </div>

            <div>
                <p class="text-muted">Estado</p>
                <p>
                    @if($cliente->activo)
                        <span class="status-pill status-vigente">Activo</span>
                    @else
                        <span class="status-pill status-incobrable">Inactivo</span>
                    @endif
                </p>
            </div>
        </div>
    </section>

    {{-- Identificación --}}
    <section class="card mt-20">
        <h2 class="card-title">Identificación</h2>

        <div class="form-grid">
            <div>
                <p class="text-muted">DUI</p>
                <p>{{ $cliente->dui ?? '—' }}</p>
            </div>
            <div>
                <p class="text-muted">NIT</p>
                <p>{{ $cliente->nit ?? '—' }}</p>
            </div>
            <div>
                <p class="text-muted">NRC</p>
                <p>{{ $cliente->nrc ?? '—' }}</p>
            </div>
        </div>
    </section>

    {{-- Información económica --}}
    <section class="card mt-20">
        <h2 class="card-title">Información financiera / laboral</h2>

        <div class="form-grid">
            <div>
                <p class="text-muted">Estado civil</p>
                <p>{{ $cliente->estado_civil ?? '—' }}</p>
            </div>
            <div>
                <p class="text-muted">Lugar de trabajo</p>
                <p>{{ $cliente->lugar_trabajo ?? '—' }}</p>
            </div>
            <div>
                <p class="text-muted">Ingresos mensuales (US$)</p>
                <p>
                    @if(!is_null($cliente->ingresos_mensuales))
                        ${{ number_format($cliente->ingresos_mensuales, 2) }}
                    @else
                        —
                    @endif
                </p>
            </div>
            <div>
                <p class="text-muted">Egresos mensuales (US$)</p>
                <p>
                    @if(!is_null($cliente->egresos_mensuales))
                        ${{ number_format($cliente->egresos_mensuales, 2) }}
                    @else
                        —
                    @endif
                </p>
            </div>

            <div>
                <p class="text-muted">Límite de crédito (US$)</p>
                <p>${{ number_format($cliente->limite_credito, 2) }}</p>
            </div>
            <div>
                <p class="text-muted">Clasificación de riesgo</p>
                <p>{{ $cliente->clasificacion->nombre ?? 'Sin clasificar' }}</p>
            </div>
        </div>
    </section>

    {{-- Cuentas por cobrar del cliente --}}
    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Cuentas por cobrar asociadas</h2>
            <span class="badge">
                {{ $cliente->cuentas->count() }} {{ \Illuminate\Support\Str::plural('cuenta', $cliente->cuentas->count()) }}
            </span>
        </div>

        @if($cliente->cuentas->isEmpty())
            <p class="empty-state">Este cliente aún no tiene cuentas por cobrar registradas.</p>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                    <tr>
                        <th>N° factura</th>
                        <th>Fecha inicio</th>
                        <th>Vence</th>
                        <th>Capital actual</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cliente->cuentas as $cuenta)
                        <tr>
                            <td>{{ $cuenta->numero_factura }}</td>
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
                                    Ver cuenta
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
