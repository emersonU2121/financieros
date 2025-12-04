<?php $__env->startSection('title', 'Usuarios'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Usuarios del sistema</h1>
            <p class="page-subtitle">
                Gestión de cuentas de acceso y asignación de roles.
            </p>
        </div>
        <a href="<?php echo e(route('usuarios.create')); ?>" class="btn btn-primary">
            + Nuevo usuario
        </a>
    </div>

    <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="card">
        <div class="card-header-row">
            <h2 class="card-title">Listado de usuarios</h2>
            <span class="badge">
                <?php echo e($usuarios->total()); ?> registros
            </span>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($usuario->name); ?></td>
                        <td><?php echo e($usuario->email); ?></td>
                        <td><?php echo e($usuario->role->nombre ?? 'Sin rol'); ?></td>
                        <td>
                            <?php if($usuario->activo): ?>
                                <span class="status-pill status-vigente">Activo</span>
                            <?php else: ?>
                                <span class="status-pill status-incobrable">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a href="<?php echo e(route('usuarios.edit', $usuario)); ?>"
                               class="btn btn-xs btn-secondary">
                                Editar
                            </a>

                            <form action="<?php echo e(route('usuarios.destroy', $usuario)); ?>"
                                  method="POST" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-xs btn-light"
                                        onclick="return confirm('¿Desactivar este usuario?')">
                                    Desactivar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5">
                            <p class="empty-state">No hay usuarios registrados aún.</p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-20">
            <?php echo e($usuarios->links()); ?>

        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/josue-avalos/SistemaANF/financieros/resources/views/usuarios/index.blade.php ENDPATH**/ ?>