<?php $__env->startSection('title', 'Iniciar sesión'); ?>

<?php $__env->startSection('content'); ?>
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
        <?php if($errors->any()): ?>
            <div class="alert alert-danger" style="margin-bottom: 1rem; font-size: 0.85rem;">
                <ul style="margin:0; padding-left: 1.1rem;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login.post')); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group" style="margin-bottom: 0.9rem;">
                <label for="email">Correo electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="input"
                    value="<?php echo e(old('email')); ?>"
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
                    <?php echo e(old('remember') ? 'checked' : ''); ?>

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/josue-avalos/SistemaANF/financieros/resources/views/auth/login.blade.php ENDPATH**/ ?>