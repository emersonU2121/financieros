<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sistema Financiero - CxC')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSS principal --}}
    <link rel="stylesheet" href="{{ asset('css/financieros.css') }}">
</head>
<body>

<header class="topbar">
    <div class="topbar-left">
        <span class="topbar-logo">SISTEMA FINANCIERO</span>

        {{-- Si está autenticado mostramos el módulo actual --}}
        @auth
            <span class="topbar-module">· Cuentas por Cobrar</span>
        @endauth
    </div>

    <nav class="topbar-nav">
        @auth
            {{-- MENÚ DEL SISTEMA PARA USUARIOS AUTENTICADOS --}}
            <a href="{{ route('clientes.index') }}"
               class="topbar-link {{ request()->is('clientes*') ? 'is-active' : '' }}">
                Clientes
            </a>

            <a href="{{ route('cuentas.index') }}"
               class="topbar-link {{ request()->is('cuentas*') ? 'is-active' : '' }}">
                Cuentas por Cobrar
            </a>

            <a href="{{ route('politicas.index') }}"
               class="topbar-link {{ request()->is('politicas*') ? 'is-active' : '' }}">
                Políticas de Crédito
            </a>

            <a href="{{ route('reportes.cartera') }}"
               class="topbar-link {{ request()->is('reportes*') ? 'is-active' : '' }}">
                Reportes
            </a>
             <a href="{{ route('bitacora.index') }}"
               class="topbar-link {{ request()->is('bitacora*') ? 'is-active' : '' }}">
                Bitácora
            </a>

            <a href="{{ route('usuarios.index') }}"
               class="topbar-link {{ request()->routeIs('usuarios.*') ? 'is-active' : '' }}">
                Usuarios
            </a>

        @endauth
    </nav>

    <div class="topbar-right">
        {{-- SI NO ESTÁ LOGUEADO: SOLO "Iniciar sesión" --}}
        @guest
            <a href="{{ route('login') }}" class="topbar-link">
                Iniciar sesión
            </a>
        @endguest

        {{-- SI ESTÁ LOGUEADO: MOSTRAR USUARIO + BOTÓN SALIR --}}
        @auth
            <span class="topbar-username">
                {{ auth()->user()->name }}
            </span>

            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-light btn-xs">
                    Salir
                </button>
            </form>
        @endauth
    </div>
</header>


<main class="page">
    @includeIf('partials.alerts')

    @yield('content')
</main>

<footer class="footer">
    <span>Proyecto Financiero · Módulo Cuentas por Cobrar · Universidad de El Salvador 2025</span>
</footer>

</body>
</html>
