@extends('layouts.app')

@section('title', 'Reporte de Cartera')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Reporte de Cartera General</h1>
            <p class="page-subtitle">
                Resumen de todas las cuentas por cobrar: vigentes, en mora, refinanciadas,
                canceladas e incobrables.
            </p>
        </div>

        <a href="{{ route('cuentas.index') }}" class="btn btn-light">
            ← Volver a Cuentas por Cobrar
        </a>
    </div>

    {{-- Resumen rápido (si tu controlador manda estos datos, los mostrará; si no, no pasa nada) --}}
    @php
        // Si el controlador solo envía $cuentas, calculamos algo básico aquí:
        $totalCuentas   = isset($cuentas) ? $cuentas->count() : 0;
        $saldoVigente   = isset($cuentas) ? $cuentas->where('estado', 'VIGENTE')->sum('monto_capital_actual') : 0;
        $saldoMora      = isset($cuentas) ? $cuentas->where('estado', 'EN_MORA')->sum('monto_capital_actual') : 0;
        $saldoTotal     = isset($cuentas) ? $cuentas->sum('monto_capital_actual') : 0;
    @endphp

    <section class="card card-filters">
        <h2 class="card-title">Resumen de la cartera</h2>

        <div class="filters-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
            <div class="form-group">
                <label>Total de cuentas</label>
                <div class="input" style="border-style:dashed; font-weight:600;">
                    {{ $totalCuentas }}
                </div>
            </div>

            <div class="form-group">
                <label>Saldo vigente</label>
                <div class="input" style="border-style:dashed; color:#22c55e; font-weight:600;">
                    ${{ number_format($saldoVigente, 2) }}
                </div>
            </div>

            <div class="form-group">
                <label>Saldo total de cartera</label>
                <div class="input" style="border-style:dashed; color:#0ea5e9; font-weight:600;">
                    ${{ number_format($saldoTotal, 2) }}
                </div>
            </div>
        </div>
    </section>

    {{-- Detalle de cuentas --}}
    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Detalle de cuentas por cobrar</h2>
            <span class="badge">
                {{ $totalCuentas }} {{ \Illuminate\Support\Str::plural('registro', $totalCuentas) }}
            </span>
        </div>

        @if(!isset($cuentas) || $cuentas->isEmpty())
            <p class="empty-state">
                No hay cuentas registradas para mostrar en el reporte de cartera.
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
                            <th>Capital inicial</th>
                            <th>Capital actual</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cuentas as $cuenta)
                            <tr>
                                <td>{{ $cuenta->numero_factura }}</td>
                                <td>{{ $cuenta->cliente->nombre_completo ?? $cuenta->cliente->nombre }}</td>
                                <td>{{ optional($cuenta->fecha_inicio)->format('d/m/Y') }}</td>
                                <td>{{ optional($cuenta->fecha_vencimiento)->format('d/m/Y') }}</td>
                                <td>${{ number_format($cuenta->monto_capital_inicial, 2) }}</td>
                                <td>${{ number_format($cuenta->monto_capital_actual, 2) }}</td>
                                <td>
                                    <span class="status-pill status-{{ strtolower($cuenta->estado) }}">
                                        {{ ucfirst(strtolower($cuenta->estado)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
