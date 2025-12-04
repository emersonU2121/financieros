<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $__env->yieldContent('title', 'Sistema Financiero - CxC'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <link rel="stylesheet" href="<?php echo e(asset('css/financieros.css')); ?>">
</head>
<body>

<header class="topbar">
    <div class="topbar-left">
        <span class="topbar-logo">SISTEMA FINANCIERO</span>

        
        <?php if(auth()->guard()->check()): ?>
            <span class="topbar-module">· Cuentas por Cobrar</span>
        <?php endif; ?>
    </div>

    <nav class="topbar-nav">
        <?php if(auth()->guard()->check()): ?>
            
            <a href="<?php echo e(route('clientes.index')); ?>"
               class="topbar-link <?php echo e(request()->is('clientes*') ? 'is-active' : ''); ?>">
                Clientes
            </a>

            <a href="<?php echo e(route('cuentas.index')); ?>"
               class="topbar-link <?php echo e(request()->is('cuentas*') ? 'is-active' : ''); ?>">
                Cuentas por Cobrar
            </a>

            <a href="<?php echo e(route('politicas.index')); ?>"
               class="topbar-link <?php echo e(request()->is('politicas*') ? 'is-active' : ''); ?>">
                Políticas de Crédito
            </a>

            <a href="<?php echo e(route('reportes.cartera')); ?>"
               class="topbar-link <?php echo e(request()->is('reportes*') ? 'is-active' : ''); ?>">
                Reportes
            </a>
             <a href="<?php echo e(route('bitacora.index')); ?>"
               class="topbar-link <?php echo e(request()->is('bitacora*') ? 'is-active' : ''); ?>">
                Bitácora
            </a>

            <a href="<?php echo e(route('usuarios.index')); ?>"
               class="topbar-link <?php echo e(request()->routeIs('usuarios.*') ? 'is-active' : ''); ?>">
                Usuarios
            </a>

        <?php endif; ?>
    </nav>

    <div class="topbar-right">
        
        <?php if(auth()->guard()->guest()): ?>
            <a href="<?php echo e(route('login')); ?>" class="topbar-link">
                Iniciar sesión
            </a>
        <?php endif; ?>

        
        <?php if(auth()->guard()->check()): ?>
            <span class="topbar-username">
                <?php echo e(auth()->user()->name); ?>

            </span>

            <form action="<?php echo e(route('logout')); ?>" method="POST" style="display:inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-light btn-xs">
                    Salir
                </button>
            </form>
        <?php endif; ?>
    </div>
</header>


<main class="page">
    <?php if ($__env->exists('partials.alerts')) echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->yieldContent('content'); ?>
</main>

<footer class="footer">
    <span>Proyecto Financiero · Módulo Cuentas por Cobrar · Universidad de El Salvador 2025</span>
</footer>

</body>
</html>
<?php /**PATH C:\proyectos\financieros\resources\views/layouts/app.blade.php ENDPATH**/ ?>