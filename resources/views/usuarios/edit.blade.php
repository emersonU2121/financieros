@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Editar usuario</h1>
            <p class="page-subtitle">
                Actualiza los datos del usuario y su rol dentro del sistema.
            </p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-light">
            ← Volver al listado
        </a>
    </div>

    @include('partials.alerts')

    <section class="card">
        <h2 class="card-title">Datos del usuario</h2>

        <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
            @method('PUT')
            @include('usuarios._form', ['textoBoton' => 'Actualizar usuario'])
        </form>
    </section>
</div>
@endsection
