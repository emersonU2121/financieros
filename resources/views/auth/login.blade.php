@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Iniciar sesión</h1>
            <p class="page-subtitle">
                Accede al módulo de cuentas por cobrar con tu usuario del sistema.
            </p>
        </div>
    </div>

    <div class="card" style="max-width: 480px; margin: 0 auto;">
        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1rem; font-size: 0.85rem;">
                <ul style="margin:0; padding-left: 1.1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group" style="margin-bottom: 0.9rem;">
                <label for="email">Correo electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="input"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
            </div>

            <div class="form-group" style="margin-bottom: 0.9rem;">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="input"
                    required
                >
            </div>

            <div class="form-group" style="display:flex; align-items:center; gap:0.4rem; margin-bottom: 1rem;">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    style="width: 14px; height: 14px;"
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label for="remember" style="margin:0; font-size:0.8rem; color: var(--text-muted);">
                    Mantener sesión iniciada
                </label>
            </div>

            <div class="form-actions" style="display:flex; justify-content:flex-end; gap:0.6rem;">
                <button type="submit" class="btn btn-primary">
                    Entrar al sistema
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
