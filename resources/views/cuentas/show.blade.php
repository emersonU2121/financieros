@extends('layouts.app')

@section('title', 'Detalle de cuenta')

@section('content')

<div class="page">

    {{-- ENCABEZADO --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Cuenta #{{ $cuenta->id }}</h1>
            <p class="page-subtitle">
                Factura {{ $cuenta->numero_factura }} • Cliente: {{ $cuenta->cliente->nombre }}
            </p>
        </div>

        <span class="status-pill status-{{ strtolower($cuenta->estado) }}">
            {{ strtoupper($cuenta->estado) }}
        </span>
    </div>

    {{-- INFORMACIÓN PRINCIPAL --}}
    <div class="card">
        <h2 class="card-title">Información general</h2>

        <div class="form-grid" style="margin-top: 1rem;">
            
            <div>
                <p class="text-muted">Cliente</p>
                <p>{{ $cuenta->cliente->codigo }} · {{ $cuenta->cliente->nombre }}</p>
            </div>

            <div>
                <p class="text-muted">Política aplicada</p>
                <p>{{ $cuenta->politica->nombre }} ({{ $cuenta->politica->plazo_dias }} días)</p>
            </div>

            <div>
                <p class="text-muted">Fechas</p>
                <p>
                    Inicio: {{ $cuenta->fecha_inicio->format('d/m/Y') }} <br>
                    Vence: {{ $cuenta->fecha_vencimiento->format('d/m/Y') }}
                </p>
            </div>

            <div>
                <p class="text-muted">Montos</p>
                <p>
                    Capital inicial: ${{ number_format($cuenta->monto_capital_inicial, 2) }} <br>
                    Capital actual: ${{ number_format($cuenta->monto_capital_actual, 2) }}
                </p>
            </div>

            <div>
                <p class="text-muted">Intereses / Comisiones</p>
                <p>
                    Intereses acumulados: ${{ number_format($cuenta->intereses_acumulados, 2) }} <br>
                    Comisiones acumuladas: ${{ number_format($cuenta->comisiones_acumuladas, 2) }}
                </p>
            </div>

        </div>
    </div>

    {{-- PAGOS REGISTRADOS --}}
    <div class="card">
        <div class="card-header-row">
            <h2 class="card-title">Pagos registrados</h2>

            @if(!in_array($cuenta->estado, ['CANCELADO', 'INCOBRABLE', 'REFINANCIADO']))
                <a href="#form-pago" class="btn btn-primary btn-xs">
                    + Registrar pago
                </a>
            @endif
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                <tr>
                    <th>Recibo</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Interés</th>
                    <th>Comisión</th>
                    <th>Capital</th>
                    <th>Forma pago</th>
                </tr>
                </thead>

                <tbody>
                @forelse($cuenta->pagos as $pago)
                    <tr>
                        <td>{{ $pago->numero_recibo }}</td>
                        <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                        <td>${{ number_format($pago->monto_total, 2) }}</td>
                        <td>${{ number_format($pago->monto_interes, 2) }}</td>
                        <td>${{ number_format($pago->monto_comision, 2) }}</td>
                        <td>${{ number_format($pago->monto_capital, 2) }}</td>
                        <td>{{ $pago->forma_pago ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-muted empty-state text-center">
                            No hay pagos registrados aún.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FORM DE PAGO --}}
    @if(!in_array($cuenta->estado, ['CANCELADO','INCOBRABLE','REFINANCIADO']))
    <div class="card" id="form-pago">
        <h2 class="card-title">Registrar pago</h2>
        <p class="page-subtitle">El sistema aplica primero intereses, luego comisiones y por último capital.</p>

        <form method="POST" action="{{ route('pagos.store') }}">
            @csrf
            <input type="hidden" name="cuenta_id" value="{{ $cuenta->id }}">

            <div class="form-grid">

                <div class="form-group">
                    <label for="fecha_pago">Fecha</label>
                    <input type="date" id="fecha_pago" name="fecha_pago"
                           class="input"
                           value="{{ old('fecha_pago', now()->format('Y-m-d')) }}" required>
                </div>

                <div class="form-group">
                    <label for="monto_total">Monto total pagado</label>
                    <input type="number" step="0.01" id="monto_total" name="monto_total"
                           class="input" value="{{ old('monto_total') }}" required>
                </div>

                <div class="form-group">
                    <label for="forma_pago">Forma de pago</label>
                    <input type="text" id="forma_pago" name="forma_pago"
                           class="input"
                           value="{{ old('forma_pago', 'EFECTIVO') }}">
                </div>

                <div class="form-group" style="grid-column:1 / -1;">
                    <label for="observaciones">Observaciones</label>
                    <textarea id="observaciones" name="observaciones" rows="2"
                              class="input">{{ old('observaciones') }}</textarea>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrar pago</button>
            </div>

        </form>

    </div>
    @endif

</div>

@endsection
