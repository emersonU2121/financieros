<?php $__env->startSection('title', 'Cuentas por Cobrar'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">

    
    <div class="page-header">
        <div>
            <h1 class="page-title">Cuentas por Cobrar</h1>
            <p class="page-subtitle">
                Créditos originados en ventas a crédito. Control de intereses, comisiones y estado de la cartera.
            </p>
        </div>

        <a href="<?php echo e(route('cuentas.create')); ?>" class="btn btn-primary">
            + Nueva cuenta
        </a>
    </div>

    
    <section class="card card-filters">
        <div class="card-header-row">
            <h2 class="card-title">Filtros de búsqueda</h2>
            <span class="badge">Refina la cartera por cliente, estado y rango de fechas</span>
        </div>

        <form method="GET" action="<?php echo e(route('cuentas.index')); ?>">
            <div class="filters-grid">
                <div class="form-group">
                    <label for="cliente">Cliente</label>
                    <select name="cliente" id="cliente" class="input">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cliente->id); ?>"
                                <?php echo e((request('cliente') == $cliente->id) ? 'selected' : ''); ?>>
                                <?php echo e($cliente->nombre_completo ?? $cliente->nombre); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="input">
                        <option value="">Todos</option>
                        <option value="VIGENTE"      <?php echo e(request('estado')=='VIGENTE' ? 'selected' : ''); ?>>Vigente</option>
                        <option value="EN_MORA"      <?php echo e(request('estado')=='EN_MORA' ? 'selected' : ''); ?>>En mora</option>
                        <option value="INCOBRABLE"   <?php echo e(request('estado')=='INCOBRABLE' ? 'selected' : ''); ?>>Incobrable</option>
                        <option value="REFINANCIADO" <?php echo e(request('estado')=='REFINANCIADO' ? 'selected' : ''); ?>>Refinanciado</option>
                        <option value="CANCELADO"    <?php echo e(request('estado')=='CANCELADO' ? 'selected' : ''); ?>>Cancelado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="desde">Desde (fecha inicio)</label>
                    <input type="date" name="desde" id="desde"
                           value="<?php echo e(request('desde')); ?>" class="input">
                </div>

                <div class="form-group">
                    <label for="hasta">Hasta (fecha inicio)</label>
                    <input type="date" name="hasta" id="hasta"
                           value="<?php echo e(request('hasta')); ?>" class="input">
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-secondary">Filtrar</button>
                    <a href="<?php echo e(route('cuentas.index')); ?>" class="btn btn-light">Limpiar</a>
                </div>
            </div>
        </form>
    </section>

    
    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Cartera de créditos</h2>
            <span class="badge">
                <?php echo e($cuentas->count()); ?> <?php echo e(\Illuminate\Support\Str::plural('registro', $cuentas->count())); ?>

            </span>
        </div>

        <?php if($cuentas->isEmpty()): ?>
            <p class="empty-state">
                No hay cuentas registradas con los filtros actuales.
            </p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>N° Factura</th>
                            <th>Cliente</th>
                            <th>Fecha inicio</th>
                            <th>Vencimiento</th>
                            <th>Capital actual</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $cuentas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cuenta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($cuenta->numero_factura); ?></td>
                            <td><?php echo e($cuenta->cliente->nombre_completo ?? $cuenta->cliente->nombre); ?></td>
                            <td><?php echo e(optional($cuenta->fecha_inicio)->format('d/m/Y')); ?></td>
                            <td><?php echo e(optional($cuenta->fecha_vencimiento)->format('d/m/Y')); ?></td>
                            <td>$<?php echo e(number_format($cuenta->monto_capital_actual, 2)); ?></td>
                            <td>
                                <span class="status-pill status-<?php echo e(strtolower($cuenta->estado)); ?>">
                                    <?php echo e(ucfirst(strtolower($cuenta->estado))); ?>

                                </span>
                            </td>
                            <td class="table-actions">
                                <a href="<?php echo e(route('cuentas.show', $cuenta)); ?>" class="btn btn-xs btn-outline">
                                    Ver
                                </a>
                                <a href="<?php echo e(route('cuentas.edit', $cuenta)); ?>" class="btn btn-xs btn-secondary">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/josue-avalos/SistemaANF/financieros/resources/views/cuentas/index.blade.php ENDPATH**/ ?>