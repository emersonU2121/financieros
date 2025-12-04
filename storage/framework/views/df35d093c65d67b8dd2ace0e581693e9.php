<?php $__env->startSection('title', 'Políticas de Crédito'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">

    <div class="page-header">
        <div>
            <h1 class="page-title">Políticas de Crédito</h1>
            <p class="page-subtitle">
                Configura los parámetros de crédito que se aplicarán a las cuentas por cobrar.
            </p>
        </div>

        <a href="<?php echo e(route('politicas.create')); ?>" class="btn btn-primary">
            + Nueva política
        </a>
    </div>

    <div class="card">

        <div class="card-header-row">
            <h2 class="card-title">Listado de políticas registradas</h2>
            <span class="badge">
                <?php echo e($politicas->count()); ?> registro<?php echo e($politicas->count() === 1 ? '' : 's'); ?>

            </span>
        </div>

        <?php if($politicas->isEmpty()): ?>
            <p class="empty-state">
                No hay políticas de crédito registradas. Crea una nueva con el botón “Nueva política”.
            </p>
        <?php else: ?>
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
                        <?php $__currentLoopData = $politicas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $politica): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($politica->nombre); ?></td>
                                <td><?php echo e($politica->plazo_dias); ?></td>
                                <td><?php echo e(number_format($politica->tasa_interes_anual, 2)); ?>%</td>
                                <td><?php echo e(number_format($politica->tasa_mora_anual, 2)); ?>%</td>
                                <td><?php echo e(number_format($politica->comision_inicial, 2)); ?>%</td>
                                <td><?php echo e($politica->dias_gracia); ?></td>
                                <td><?php echo e($politica->dias_para_mora); ?></td>
                                <td><?php echo e($politica->dias_para_incobrable); ?></td>
                                <td>
                                    <?php if($politica->requiere_fiador): ?>
                                        <span class="status-pill status-refinanciado">Requiere</span>
                                    <?php else: ?>
                                        <span class="status-pill status-cancelado">No requiere</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="<?php echo e(route('politicas.edit', $politica)); ?>" class="btn btn-xs btn-secondary">Editar</a>
                                        <form action="<?php echo e(route('politicas.destroy', $politica)); ?>" method="POST" onsubmit="return confirm('¿Eliminar esta política?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-xs btn-light">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/josue-avalos/SistemaANF/financieros/resources/views/politicas/index.blade.php ENDPATH**/ ?>