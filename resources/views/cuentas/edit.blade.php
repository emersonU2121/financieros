@extends('layouts.app')

@section('title', 'Editar cuenta por cobrar')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Editar cuenta por cobrar</h1>
            <p class="page-subtitle">
                Ajusta los datos principales de la cuenta seleccionada.
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-header-row">
            <h2 class="card-title">Datos de la cuenta</h2>
            <span class="badge">ID #{{ $cuenta->id }}</span>
        </div>

        <form method="POST" action="{{ route('cuentas.update', $cuenta) }}">
            @csrf
            @method('PUT')

            <div class="filters-grid">
                {{-- Cliente --}}
                <div class="form-group">
                    <label for="cliente_id">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="input" required>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                {{ old('cliente_id', $cuenta->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->codigo }} · {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Política --}}
                <div class="form-group">
                    <label for="politica_credito_id">Política de crédito</label>
                    <select name="politica_credito_id" id="politica_credito_id" class="input" required>
                        @foreach($politicas as $p)
                            <option value="{{ $p->id }}"
                                {{ old('politica_credito_id', $cuenta->politica_credito_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nombre }} ({{ $p->plazo_dias }} días · {{ $p->tasa_interes_anual }}% anual)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Responsable --}}
                <div class="form-group">
                    <label for="usuario_responsable_id">Analista / Responsable</label>
                    <select name="usuario_responsable_id" id="usuario_responsable_id" class="input" required>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}"
                                {{ old('usuario_responsable_id', $cuenta->usuario_responsable_id) == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Número factura --}}
                <div class="form-group">
                    <label for="numero_factura">Número de factura</label>
                    <input type="text" name="numero_factura" id="numero_factura" class="input"
                           value="{{ old('numero_factura', $cuenta->numero_factura) }}" required>
                </div>

                {{-- Fecha factura --}}
                <div class="form-group">
                    <label for="fecha_factura">Fecha de factura</label>
                    <input type="date" name="fecha_factura" id="fecha_factura" class="input"
                           value="{{ old('fecha_factura', optional($cuenta->fecha_factura)->format('Y-m-d')) }}" required>
                </div>

                {{-- Tipo documento --}}
                <div class="form-group">
                    <label for="tipo_documento">Tipo de documento</label>
                    <input type="text" name="tipo_documento" id="tipo_documento" class="input"
                           value="{{ old('tipo_documento', $cuenta->tipo_documento) }}" required>
                </div>

                {{-- Fecha inicio --}}
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de inicio del crédito</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="input"
                           value="{{ old('fecha_inicio', optional($cuenta->fecha_inicio)->format('Y-m-d')) }}" required>
                </div>

                {{-- Estado --}}
                <div class="form-group">
                    <label for="estado">Estado de la cuenta</label>
                    <select name="estado" id="estado" class="input" required>
                        @foreach(['VIGENTE','EN_MORA','INCOBRABLE','REFINANCIADO','CANCELADO'] as $estado)
                            <option value="{{ $estado }}"
                                {{ old('estado', $cuenta->estado) == $estado ? 'selected' : '' }}>
                                {{ ucfirst(strtolower($estado)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fiador - Nombre --}}
                <div class="form-group">
                    <label for="fiador_nombre">Fiador - Nombre</label>
                    <input type="text" name="fiador_nombre" id="fiador_nombre" class="input"
                           value="{{ old('fiador_nombre', optional($cuenta->fiador)->nombre) }}">
                </div>

                {{-- Fiador - DUI --}}
                <div class="form-group">
                    <label for="fiador_dui">Fiador - DUI</label>
                    <input type="text" name="fiador_dui" id="fiador_dui" class="input"
                           value="{{ old('fiador_dui', optional($cuenta->fiador)->dui) }}">
                </div>

                {{-- Fiador - Dirección --}}
                <div class="form-group">
                    <label for="fiador_direccion">Fiador - Dirección</label>
                    <input type="text" name="fiador_direccion" id="fiador_direccion" class="input"
                           value="{{ old('fiador_direccion', optional($cuenta->fiador)->direccion) }}">
                </div>

                {{-- Fiador - Teléfono --}}
                <div class="form-group">
                    <label for="fiador_telefono">Fiador - Teléfono</label>
                    <input type="text" name="fiador_telefono" id="fiador_telefono" class="input"
                           value="{{ old('fiador_telefono', optional($cuenta->fiador)->telefono) }}">
                </div>
            </div>

            <div class="form-actions mt-20">
                <a href="{{ route('cuentas.show', $cuenta) }}" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
