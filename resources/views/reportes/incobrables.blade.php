@extends('layouts.app')

@section('title', 'Reporte de Cuentas Incobrables')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Cuentas Incobrables</h1>
            <p class="page-subtitle">
                Muestra las cuentas marcadas como <strong>INCOBRABLE</strong>.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('reportes.incobrables.export', ['formato' => 'pdf']) }}" class="btn btn-light">
                PDF
            </a>
            <a href="{{ route('reportes.incobrables.export', ['formato' => 'csv']) }}" class="btn btn-outline-light">
                Excel (CSV)
            </a>
            <a href="{{ route('reportes.incobrables.export', ['formato' => 'excel']) }}" class="btn btn-outline-light">
                Excel (.xls)
            </a>
        </div>
    </div>

    @php
        $totalCuentas = $cuentas->count();
        $saldoIncobrable = $cuentas->sum('monto_capital_actual');
    @endphp

    <section class="card card-filters">
        <h2 class="card-title">Resumen</h2>
        <div class="filters-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
            <div class="form-group">
                <label>Total de cuentas incobrables</label>
                <div class="input" style="border-style:dashed; font-weight:600;">
                    {{ $totalCuentas }}
                </div>
            </div>
            <div class="form-group">
                <label>Saldo incobrable</label>
                <div class="input" style="border-style:dashed; color:#ef4444; font-weight:600;">
                    ${{ number_format($saldoIncobrable, 2) }}
                </div>
            </div>
        </div>
    </section>

    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Detalle de cuentas incobrables</h2>
            <span class="badge">
                {{ $totalCuentas }} {{ \Illuminate\Support\Str::plural('registro', $totalCuentas) }}
            </span>
        </div>

        @if($cuentas->isEmpty())
            <p class="empty-state">
                No hay cuentas incobrables registradas.
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
                        <th>Capital incobrable</th>
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
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
