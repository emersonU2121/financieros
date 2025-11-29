@extends('layouts.app')

@section('title', 'Nueva cuenta por cobrar')

@section('content')
<div class="page">

    {{-- ENCABEZADO --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Nueva cuenta por cobrar</h1>
            <p class="page-subtitle">Registra una nueva cuenta generada por una factura a crédito.</p>
        </div>
        <a href="{{ route('cuentas.index') }}" class="btn btn-light">← Volver</a>
    </div>

    {{-- CARD PRINCIPAL --}}
    <div class="card">

        <div class="card-header-row">
            <h2 class="card-title">Formulario de registro</h2>
        </div>

        <form method="POST" action="{{ route('cuentas.store') }}">
            @csrf

            {{-- FORM GRID ESTILIZADO --}}
            <div class="filters-grid" style="margin-top: 1rem;">

                {{-- CLIENTE --}}
                <div class="form-group">
                    <label for="cliente_id">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="input" required>
                        <option value="">Seleccione un cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id')==$cliente->id?'selected':'' }}>
                                {{ $cliente->codigo }} · {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- POLÍTICA DE CRÉDITO --}}
                <div class="form-group">
                    <label for="politica_credito_id">Política de crédito</label>
                    <select name="politica_credito_id" id="politica_credito_id" class="input" required>
                        <option value="">Seleccione una política</option>
                        @foreach($politicas as $p)
                            <option value="{{ $p->id }}" {{ old('politica_credito_id')==$p->id?'selected':'' }}>
                                {{ $p->nombre }} —
                                {{ $p->plazo_dias }} días · {{ $p->tasa_interes_anual }}% interés
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- RESPONSABLE --}}
                <div class="form-group">
                    <label for="usuario_responsable_id">Analista / Responsable</label>
                    <select name="usuario_responsable_id" id="usuario_responsable_id" class="input" required>
                        <option value="">Seleccione usuario</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}" {{ old('usuario_responsable_id')==$u->id?'selected':'' }}>
                                {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- FACTURA --}}
                <div class="form-group">
                    <label for="numero_factura">Número de factura</label>
                    <input type="text" class="input"
                           name="numero_factura"
                           value="{{ old('numero_factura') }}"
                           required>
                </div>

                <div class="form-group">
                    <label for="fecha_factura">Fecha de factura</label>
                    <input type="date" class="input"
                           name="fecha_factura"
                           value="{{ old('fecha_factura') }}"
                           required>
                </div>

                <div class="form-group">
                    <label for="tipo_documento">Tipo de documento</label>
                    <input type="text" class="input"
                           name="tipo_documento"
                           value="{{ old('tipo_documento','FACTURA_CREDITO') }}">
                </div>

                {{-- MONTO --}}
                <div class="form-group">
                    <label for="monto_capital_inicial">Monto financiado (capital)</label>
                    <input type="number" step="0.01" class="input"
                           name="monto_capital_inicial"
                           value="{{ old('monto_capital_inicial') }}"
                           required>
                </div>

                {{-- FECHA DE INICIO --}}
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de inicio del crédito</label>
                    <input type="date" class="input"
                           name="fecha_inicio"
                           value="{{ old('fecha_inicio') }}"
                           required>
                </div>

                {{-- FIADOR OPCIONAL --}}
                <div class="form-group">
                    <label for="fiador_nombre">Fiador — Nombre</label>
                    <input type="text" class="input"
                           name="fiador_nombre"
                           value="{{ old('fiador_nombre') }}">
                </div>

                <div class="form-group">
                    <label for="fiador_dui">Fiador — DUI</label>
                    <input type="text" class="input"
                           name="fiador_dui"
                           value="{{ old('fiador_dui') }}">
                </div>

                <div class="form-group">
                    <label for="fiador_direccion">Fiador — Dirección</label>
                    <input type="text" class="input"
                           name="fiador_direccion"
                           value="{{ old('fiador_direccion') }}">
                </div>

                <div class="form-group">
                    <label for="fiador_telefono">Fiador — Teléfono</label>
                    <input type="text" class="input"
                           name="fiador_telefono"
                           value="{{ old('fiador_telefono') }}">
                </div>
            </div>

            {{-- ACCIONES --}}
            <div class="filters-actions" style="margin-top: 1.8rem;">
                <a href="{{ route('cuentas.index') }}" class="btn btn-light">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar cuenta</button>
            </div>

        </form>
    </div>
</div>
@endsection
