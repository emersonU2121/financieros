@extends('layouts.app')

@section('title', 'Editar Política de Crédito')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Editar Política de Crédito</h1>
            <p class="page-subtitle">
                Modifica los parámetros de esta política. Los cambios aplican hacia adelante sobre
                nuevas operaciones; su impacto sobre cuentas ya originadas se documenta en el módulo
                de análisis financiero.
            </p>
        </div>

        <a href="{{ route('politicas.index') }}" class="btn btn-light">
            ← Volver al listado
        </a>
    </div>

    <form action="{{ route('politicas.update', $politica) }}" method="POST">
        @csrf
        @method('PUT')

        @include('politicas._form', ['modo' => 'editar'])
    </form>
@endsection
