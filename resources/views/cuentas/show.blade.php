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

        {{-- MODIFICACIÓN: Agrupamos el estado y el botón en un contenedor flexible --}}
        <div style="display: flex; gap: 10px; align-items: center;">
            <span class="status-pill status-{{ strtolower($cuenta->estado) }}">
                {{ strtoupper($cuenta->estado) }}
            </span>


        </div>
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

{{-- MODAL DE REACTIVACIÓN --}}
@if($cuenta->estado == 'INCOBRABLE')
<div class="card" align="center" style="margin: 1rem auto; max-width: 400px; padding: 1rem;">
    {{-- AQUI ESTÁ EL CAMBIO: Agregamos style="max-width: 400px;" --}}
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;"> 
        
        <div class="modal-content card" style="padding: 0; border: 1px solid rgba(148, 163, 184, 0.2);">
            
            <div class="modal-header" style="border-bottom: 1px solid #1f2933; padding: 1rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 700; color: var(--text-main); font-size: 1.1rem;">
                    Reactivar Cuenta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" 
                        style="filter: invert(1); opacity: 0.7;"></button>
            </div>

            <form action="{{ route('cuentas.reactivarIncobrable', $cuenta->id) }}" method="POST">
                @csrf
                
                <div class="modal-body" style="padding: 1.5rem;">
                    
                    {{-- Alerta más compacta --}}
                    <div style="background: rgba(234, 179, 8, 0.1); border: 1px solid rgba(234, 179, 8, 0.3); color: #facc15; padding: 0.8rem; border-radius: 0.5rem; margin-bottom: 1.2rem; font-size: 0.85rem; line-height: 1.4;">
                        <strong>Atención:</strong> Se calcularán intereses por los días inactivos.
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="fecha_nuevo_inicio" style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display:block;">
                            Fecha de Reactivación
                        </label>
                        <input type="date" 
                               name="fecha_nuevo_inicio" 
                               class="input" 
                               style="width: 100%; color-scheme: dark;" 
                               value="{{ date('Y-m-d') }}" 
                               required>
                    </div>

                    <div class="form-group">
                        <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display:block;">
                            Saldo Capital Actual
                        </label>
                        <input type="text" 
                               class="input" 
                               value="${{ number_format($cuenta->monto_capital_actual, 2) }}" 
                               disabled
                               style="width: 100%; opacity: 0.7; background: rgba(15, 23, 42, 0.5);">
                    </div>
                </div>

                <div class="modal-footer" style="border-top: 1px solid #1f2933; padding: 0.8rem 1.5rem; background: rgba(15, 23, 42, 0.3);">
                    <button type="button" class="btn btn-light btn-xs" data-bs-dismiss="modal" style="padding: 0.4rem 1rem;">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-xs" style="padding: 0.4rem 1rem;">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection