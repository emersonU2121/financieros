@extends('layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Nuevo Usuario</h1>
            <p class="page-subtitle">
                Registra un nuevo usuario del sistema y asigna su rol (analista, administrador, etc.).
            </p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-light">
            ← Volver al listado
        </a>
    </div>

    @include('partials.alerts')

    <section class="card">
        <h2 class="card-title">Formulario de registro</h2>

        <form method="POST" action="{{ route('usuarios.store') }}">
            @include('usuarios._form', ['textoBoton' => 'Guardar usuario'])
        </form>
    </section>
</div>
@endsection
