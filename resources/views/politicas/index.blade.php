@extends('layouts.app')

@section('title', 'Políticas de Crédito')

@section('content')
<div class="page">

    <div class="page-header">
        <div>
            <h1 class="page-title">Políticas de Crédito</h1>
            <p class="page-subtitle">
                Configura los parámetros de crédito que se aplicarán a las cuentas por cobrar.
            </p>
        </div>

        <a href="{{ route('politicas.create') }}" class="btn btn-primary">
            + Nueva política
        </a>
    </div>

    <div class="card">

        <div class="card-header-row">
            <h2 class="card-title">Listado de políticas registradas</h2>
            <span class="badge">
                {{ $politicas->count() }} registro{{ $politicas->count() === 1 ? '' : 's' }}
            </span>
        </div>

        @if($politicas->isEmpty())
            <p class="empty-state">
                No hay políticas de crédito registradas. Crea una nueva con el botón “Nueva política”.
            </p>
        @else
            <div class="table-wrapper mt-20">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Plazo (días)</th>
                            <th>Tasa interés anual</th>
                            <th>Tasa mora anual</th>
                            <th>Comisión crédito</th>
                            <th>Días gracia</th>
                            <th>Días para mora</th>
                            <th>Días incobrable</th>
                            <th>Fiador</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($politicas as $politica)
                            <tr>
                                <td>{{ $politica->nombre }}</td>
                                <td>{{ $politica->plazo_dias }}</td>
                                <td>{{ number_format($politica->tasa_interes_anual, 2) }}%</td>
                                <td>{{ number_format($politica->tasa_mora_anual, 2) }}%</td>
                                <td>{{ number_format($politica->comision_inicial, 2) }}%</td>
                                <td>{{ $politica->dias_gracia }}</td>
                                <td>{{ $politica->dias_para_mora }}</td>
                                <td>{{ $politica->dias_para_incobrable }}</td>
                                <td>
                                    @if($politica->requiere_fiador)
                                        <span class="status-pill status-refinanciado">Requiere</span>
                                    @else
                                        <span class="status-pill status-cancelado">No requiere</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('politicas.edit', $politica) }}" class="btn btn-xs btn-secondary">Editar</a>
                                        <form action="{{ route('politicas.destroy', $politica) }}" method="POST" onsubmit="return confirm('¿Eliminar esta política?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-light">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>

</div>
@endsection
