@extends('layouts.app')

@section('title', 'Nueva Política de Crédito')

@section('content')
<div class="page">

    <div class="page-header">
        <div>
            <h1 class="page-title">Nueva Política de Crédito</h1>
            <p class="page-subtitle">
                Registra una nueva política que luego podrás asociar a las cuentas por cobrar.
            </p>
        </div>

        <a href="{{ route('politicas.index') }}" class="btn btn-light">
            ← Volver al listado
        </a>
    </div>

    <div class="card">
        <div class="card-header-row">
            <h2 class="card-title">Registrar nueva política de crédito</h2>
            <span class="badge">Definición de reglas de crédito</span>
        </div>

        <p style="font-size:0.86rem; color:var(--text-muted); margin-top:0; margin-bottom:1rem;">
            Define los parámetros bajo los cuales la empresa otorga créditos: plazo, tasas, comisiones
            y plazos para mora / incobrable, en coherencia con las políticas internas y normativa salvadoreña.
        </p>

        @if ($errors->any())
            <div class="card" style="background: rgba(239,68,68,0.06); border-color: rgba(239,68,68,0.6); margin-bottom:1rem;">
                <strong style="color:var(--danger); font-size:0.9rem;">Hay errores en el formulario:</strong>
                <ul style="margin:0.4rem 0 0 1.2rem; padding:0; font-size:0.8rem; color:var(--text-main);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('politicas.store') }}" method="POST">
            @csrf
            @include('politicas._form')
        </form>
    </div>

</div>
@endsection
