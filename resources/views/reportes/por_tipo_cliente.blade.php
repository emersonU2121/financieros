@extends('layouts.app')

@section('title', 'Reporte por Tipo de Cliente')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Cartera por Tipo de Cliente y Clasificación</h1>
            <p class="page-subtitle">
                Muestra el saldo total de cartera por tipo de cliente
                (<strong>NATURAL</strong> / <strong>JURIDICA</strong>)
                y su clasificación (A, B, C, D).
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('reportes.por_tipo_cliente.export', ['formato' => 'pdf']) }}" class="btn btn-light">
                PDF
            </a>
            <a href="{{ route('reportes.por_tipo_cliente.export', ['formato' => 'csv']) }}" class="btn btn-outline-light">
                Excel (CSV)
            </a>
            <a href="{{ route('reportes.por_tipo_cliente.export', ['formato' => 'excel']) }}" class="btn btn-outline-light">
                Excel (.xls)
            </a>
        </div>
    </div>

    @php
        $totalesTipo = [];
        foreach ($estadistica as $tipo => $cats) {
            $totalesTipo[$tipo] = array_sum($cats);
        }
        $totalGeneral = array_sum($totalesTipo);
    @endphp

    <section class="card card-filters">
        <h2 class="card-title">Resumen</h2>
        <div class="filters-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
            <div class="form-group">
                <label>Total NATURAL</label>
                <div class="input" style="border-style:dashed; font-weight:600;">
                    ${{ number_format($totalesTipo['NATURAL'] ?? 0, 2) }}
                </div>
            </div>
            <div class="form-group">
                <label>Total JURIDICA</label>
                <div class="input" style="border-style:dashed; font-weight:600;">
                    ${{ number_format($totalesTipo['JURIDICA'] ?? 0, 2) }}
                </div>
            </div>
            <div class="form-group">
                <label>Total general</label>
                <div class="input" style="border-style:dashed; color:#22c55e; font-weight:600;">
                    ${{ number_format($totalGeneral, 2) }}
                </div>
            </div>
        </div>
    </section>

    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Detalle por tipo y clasificación</h2>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                <tr>
                    <th>Tipo de cliente</th>
                    <th>Clasificación A</th>
                    <th>Clasificación B</th>
                    <th>Clasificación C</th>
                    <th>Clasificación D</th>
                    <th>Total tipo</th>
                </tr>
                </thead>
                <tbody>
                @foreach($estadistica as $tipo => $cats)
                    <tr>
                        <td>{{ ucfirst(strtolower($tipo)) }}</td>
                        <td>${{ number_format($cats['A'] ?? 0, 2) }}</td>
                        <td>${{ number_format($cats['B'] ?? 0, 2) }}</td>
                        <td>${{ number_format($cats['C'] ?? 0, 2) }}</td>
                        <td>${{ number_format($cats['D'] ?? 0, 2) }}</td>
                        <td>${{ number_format(array_sum($cats), 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
