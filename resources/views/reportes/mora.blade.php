@extends('layouts.app')

@section('title', 'Reporte de Cuentas en Mora')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Reporte de Cuentas en Mora</h1>
            <p class="page-subtitle">
                Muestra las cuentas cuyo vencimiento ya pasó (según la fecha de vencimiento) y que están en estado
                <strong>VIGENTE</strong> o <strong>EN_MORA</strong>.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('reportes.mora.export', ['formato' => 'pdf']) }}" class="btn btn-light">
                PDF
            </a>
            <a href="{{ route('reportes.mora.export', ['formato' => 'csv']) }}" class="btn btn-outline-light">
                Excel (CSV)
            </a>
            <a href="{{ route('reportes.mora.export', ['formato' => 'excel']) }}" class="btn btn-outline-light">
                Excel (.xls)
            </a>
        </div>
    </div>

    @php
        $totalCuentas = $cuentas->count();
        $saldoMora = $cuentas->sum('monto_capital_actual');
    @endphp

    <section class="card card-filters">
        <h2 class="card-title">Resumen de mora</h2>
        <div class="filters-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
            <div class="form-group">
                <label>Total de cuentas en mora</label>
                <div class="input" style="border-style:dashed; font-weight:600;">
                    {{ $totalCuentas }}
                </div>
            </div>
            <div class="form-group">
                <label>Saldo total en mora</label>
                <div class="input" style="border-style:dashed; color:#f97316; font-weight:600;">
                    ${{ number_format($saldoMora, 2) }}
                </div>
            </div>
        </div>
    </section>

    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Detalle de cuentas en mora</h2>
            <span class="badge">
                {{ $totalCuentas }} {{ \Illuminate\Support\Str::plural('registro', $totalCuentas) }}
            </span>
        </div>

        @if($cuentas->isEmpty())
            <p class="empty-state">
                No hay cuentas en mora registradas.
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
                        <th>Días de atraso</th>
                        <th>Capital actual</th>
                        <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cuentas as $cuenta)
                        @php
                            $diasAtraso = now()->diffInDays($cuenta->fecha_vencimiento, false);
                        @endphp
                        <tr>
                            <td>{{ $cuenta->numero_factura }}</td>
                            <td>{{ $cuenta->cliente->nombre_completo ?? $cuenta->cliente->nombre }}</td>
                            <td>{{ optional($cuenta->fecha_inicio)->format('d/m/Y') }}</td>
                            <td>{{ optional($cuenta->fecha_vencimiento)->format('d/m/Y') }}</td>
                            <td>{{ $diasAtraso < 0 ? abs($diasAtraso) : 0 }}</td>
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
