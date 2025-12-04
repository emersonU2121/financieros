<?php $__env->startSection('title', 'Bitácora de acciones'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <div>
            <h1 class="page-title">Bitácora de acciones</h1>
            <p class="page-subtitle">
                Registro de operaciones realizadas por los usuarios en el sistema.
            </p>
        </div>
    </div>

    
    <section class="card card-filters">
        <h2 class="card-title">Filtros de búsqueda</h2>

        <form method="GET" action="<?php echo e(route('bitacora.index')); ?>" class="filters-grid">
            <div class="form-group">
                <label for="usuario_id">Usuario</label>
                <select name="usuario_id" id="usuario_id" class="input">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>"
                            <?php echo e(request('usuario_id') == $u->id ? 'selected' : ''); ?>>
                            <?php echo e($u->name); ?> (<?php echo e($u->email); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="accion">Acción</label>
                <input type="text" name="accion" id="accion"
                       class="input"
                       value="<?php echo e(request('accion')); ?>"
                       placeholder="CREAR_CREDITO, REGISTRAR_PAGO, etc.">
            </div>

            <div class="form-group">
                <label for="desde">Desde</label>
                <input type="date" name="desde" id="desde"
                       class="input"
                       value="<?php echo e(request('desde')); ?>">
            </div>

            <div class="form-group">
                <label for="hasta">Hasta</label>
                <input type="date" name="hasta" id="hasta"
                       class="input"
                       value="<?php echo e(request('hasta')); ?>">
            </div>

            <div class="filters-actions">
                <button type="submit" class="btn btn-secondary">Filtrar</button>
                <a href="<?php echo e(route('bitacora.index')); ?>" class="btn btn-light">Limpiar</a>
            </div>
        </form>
    </section>

    
    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Registro de acciones</h2>
            <span class="badge">
                <?php echo e($registros->total()); ?> registros
            </span>
        </div>

        <?php if($registros->isEmpty()): ?>
            <p class="empty-state">
                No hay registros en la bitácora con los filtros actuales.
            </p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Fecha / Hora</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Entidad</th>
                        <th>ID Entidad</th>
                        <th>Detalle</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $registros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($item->created_at->format('d/m/Y H:i')); ?></td>
                            <td>
                                <?php echo e(optional($item->user)->name ?? 'Usuario eliminado'); ?>

                                <br>
                                <span class="text-muted small">
                                    <?php echo e(optional($item->user)->email); ?>

                                </span>
                            </td>
                            <td><span class="badge"><?php echo e($item->accion); ?></span></td>
                            <td><?php echo e($item->entidad ?? '-'); ?></td>
                            <td><?php echo e($item->entidad_id ?? '-'); ?></td>
                            <td><?php echo e($item->detalle ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-20">
                <?php echo e($registros->links()); ?>

            </div>
        <?php endif; ?>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/josue-avalos/SistemaANF/financieros/resources/views/bitacora/index.blade.php ENDPATH**/ ?>