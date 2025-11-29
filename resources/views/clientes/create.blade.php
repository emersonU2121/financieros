@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@section('content')

<div class="page">

    {{-- ENCABEZADO --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Nuevo Cliente</h1>
            <p class="page-subtitle">
                Registra un nuevo cliente para asociarlo a cuentas por cobrar, límites de crédito y clasificaciones de riesgo.
            </p>
        </div>

        <a href="{{ route('clientes.index') }}" class="btn btn-light">← Volver al listado</a>
    </div>

    {{-- ERRORES --}}
    @if ($errors->any())
        <div class="card" style="border-left: 4px solid var(--danger); margin-bottom:1.5rem;">
            <h3 style="margin-top:0">Se encontraron errores:</h3>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf

        {{-- ============================= --}}
        {{-- SECCIÓN 1: DATOS GENERALES --}}
        {{-- ============================= --}}

        <div class="card">
            <h3 class="card-title">Datos generales del cliente</h3>

            <div class="filters-grid">

                {{-- Tipo --}}
                <div class="form-group">
                    <label>Tipo de cliente *</label>
                    <select name="tipo" class="input" required>
                        <option value="">Seleccione…</option>
                        <option value="NATURAL">Persona Natural</option>
                        <option value="JURIDICA">Persona Jurídica</option>
                    </select>
                </div>

                {{-- Código --}}
                <div class="form-group">
                    <label>Código interno *</label>
                    <input type="text" name="codigo" class="input" placeholder="Ej.: CLI-001" required>
                </div>

                {{-- Nombre --}}
                <div class="form-group" style="grid-column: span 2;">
                    <label>Nombre / Razón social *</label>
                    <input type="text" name="nombre" class="input" placeholder="Nombre completo o empresa" required>
                </div>

                {{-- Giro --}}
                <div class="form-group" style="grid-column: span 2;">
                    <label>Giro del negocio / profesión</label>
                    <input type="text" name="giro" class="input" placeholder="Actividad económica">
                </div>

                {{-- Dirección --}}
                <div class="form-group" style="grid-column: span 2;">
                    <label>Dirección</label>
                    <input type="text" name="direccion" class="input" placeholder="Dirección principal">
                </div>

                {{-- Zona --}}
                <div class="form-group">
                    <label>Zona</label>
                    <input type="text" name="zona" class="input" placeholder="Urbano, rural, zona 1…">
                </div>

                {{-- Teléfono --}}
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="input" placeholder="2222-2222">
                </div>

                {{-- DUI --}}
                <div class="form-group">
                    <label>DUI</label>
                    <input type="text" name="dui" class="input" placeholder="Solo persona natural">
                </div>

                {{-- NIT --}}
                <div class="form-group">
                    <label>NIT</label>
                    <input type="text" name="nit" class="input">
                </div>

                {{-- NRC --}}
                <div class="form-group">
                    <label>NRC</label>
                    <input type="text" name="nrc" class="input">
                </div>

            </div>
        </div>

        <br>

        {{-- ============================= --}}
        {{-- SECCIÓN 2: PERSONA NATURAL --}}
        {{-- ============================= --}}

        <div class="card">
            <h3 class="card-title">Información para persona natural</h3>

            <div class="filters-grid">

                {{-- Estado civil --}}
                <div class="form-group">
                    <label>Estado civil</label>
                    <input type="text" name="estado_civil" class="input" placeholder="Soltero, casado, etc.">
                </div>

                {{-- Lugar de trabajo --}}
                <div class="form-group">
                    <label>Lugar de trabajo</label>
                    <input type="text" name="lugar_trabajo" class="input">
                </div>

                {{-- Ingresos --}}
                <div class="form-group">
                    <label>Ingresos mensuales (US$)</label>
                    <input type="number" name="ingresos_mensuales" class="input" min="0" step="0.01">
                </div>

                {{-- Egresos --}}
                <div class="form-group">
                    <label>Egresos mensuales (US$)</label>
                    <input type="number" name="egresos_mensuales" class="input" min="0" step="0.01">
                </div>

            </div>
        </div>

        <br>

        {{-- ============================= --}}
        {{-- SECCIÓN 3: PERSONA JURÍDICA --}}
        {{-- ============================= --}}

        <div class="card">
            <h3 class="card-title">Información financiera (empresa)</h3>

            <div class="filters-grid">

                {{-- Total activos --}}
                <div class="form-group">
                    <label>Total activos (US$)</label>
                    <input type="number" name="total_activos" class="input" min="0" step="0.01">
                </div>

                {{-- Total pasivos --}}
                <div class="form-group">
                    <label>Total pasivos (US$)</label>
                    <input type="number" name="total_pasivos" class="input" min="0" step="0.01">
                </div>

                {{-- Ventas anuales --}}
                <div class="form-group">
                    <label>Ventas anuales (US$)</label>
                    <input type="number" name="ventas_anuales" class="input" min="0" step="0.01">
                </div>

                {{-- Utilidad neta --}}
                <div class="form-group">
                    <label>Utilidad neta (US$)</label>
                    <input type="number" name="utilidad_neta" class="input" min="0" step="0.01">
                </div>

            </div>
        </div>

        <br>

        {{-- ============================= --}}
        {{-- SECCIÓN 4: CRÉDITO Y CLASIFICACIÓN --}}
        {{-- ============================= --}}

        <div class="card">
            <h3 class="card-title">Clasificación y crédito</h3>

            <div class="filters-grid">

                {{-- Clasificación --}}
                <div class="form-group">
                    <label>Clasificación de riesgo</label>
                    <select name="clasificacion_id" class="input">
                        <option value="">Sin clasificación</option>
                        @foreach ($clasificaciones as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Límite de crédito --}}
                <div class="form-group">
                    <label>Límite de crédito autorizado (US$) *</label>
                    <input type="number" name="limite_credito" class="input" min="0" step="0.01" required>
                </div>

                {{-- Activo --}}
                <div class="form-check">
    <input
        type="checkbox"
        name="activo"
        id="activo"
        value="1"
        {{ old('activo', 1) ? 'checked' : '' }}
    >
    <label for="activo">Cliente activo</label>
</div>


            </div>
        </div>

        <br>

        {{-- BOTONES --}}
        <div class="card" style="background: transparent; box-shadow:none;">
            <button class="btn btn-primary">Guardar cliente</button>
            <a href="{{ route('clientes.index') }}" class="btn btn-light">Cancelar</a>
        </div>

    </form>

</div>

@endsection
