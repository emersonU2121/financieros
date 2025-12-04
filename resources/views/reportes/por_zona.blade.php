@extends('layouts.app')

@section('title', 'Reporte de Cartera por Zona')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Cartera por Zona Geográfica</h1>
            <p class="page-subtitle">
                Distribución de la cartera de clientes por zona geográfica (sin incluir cuentas canceladas).
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('reportes.por_zona.export', ['formato' => 'pdf']) }}" class="btn btn-light">
                PDF
            </a>
            <a href="{{ route('reportes.por_zona.export', ['formato' => 'csv']) }}" class="btn btn-outline-light">
                Excel (CSV)
            </a>
            <a href="{{ route('reportes.por_zona.export', ['formato' => 'excel']) }}" class="btn btn-outline-light">
                Excel (.xls)
            </a>
        </div>
    </div>

    @php
        $totalCuentas = collect($porZona)->sum('cantidad_cuentas');
        $saldoTotal   = collect($porZona)->sum('monto_total');
    @endphp

    <section class="card card-filters">
        <h2 class="card-title">Resumen</h2>
        <div class="filters-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
            <div class="form-group">
                <label>Total de cuentas</label>
                <div class="input" style="border-style:dashed; font-weight:600;">
                    {{ $totalCuentas }}
                </div>
            </div>
            <div class="form-group">
                <label>Saldo total</label>
                <div class="input" style="border-style:dashed; color:#0ea5e9; font-weight:600;">
                    ${{ number_format($saldoTotal, 2) }}
                </div>
            </div>
        </div>
    </section>

    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Detalle por zona</h2>
            <span class="badge">
                {{ count($porZona) }} {{ \Illuminate\Support\Str::plural('zona', count($porZona)) }}
            </span>
        </div>

        @if(empty($porZona))
            <p class="empty-state">
                No hay información de zonas para mostrar.
            </p>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Cantidad de cuentas</th>
                        <th>Saldo total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($porZona as $zona => $datos)
                        <tr>
                            <td>{{ $zona === 'SIN_ZONA' ? 'Sin zona registrada' : $zona }}</td>
                            <td>{{ $datos['cantidad_cuentas'] }}</td>
                            <td>${{ number_format($datos['monto_total'], 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
