<?php $__env->startSection('title', 'Detalle de cuenta'); ?>

<?php $__env->startSection('content'); ?>

<div class="page">

    
    <div class="page-header">
        <div>
            <h1 class="page-title">Cuenta #<?php echo e($cuenta->id); ?></h1>
            <p class="page-subtitle">
                Factura <?php echo e($cuenta->numero_factura); ?> • Cliente: <?php echo e($cuenta->cliente->nombre); ?>

            </p>
        </div>

        <span class="status-pill status-<?php echo e(strtolower($cuenta->estado)); ?>">
            <?php echo e(strtoupper($cuenta->estado)); ?>

        </span>
    </div>

    
    <div class="card">
        <h2 class="card-title">Información general</h2>

        <div class="form-grid" style="margin-top: 1rem;">
            
            <div>
                <p class="text-muted">Cliente</p>
                <p><?php echo e($cuenta->cliente->codigo); ?> · <?php echo e($cuenta->cliente->nombre); ?></p>
            </div>

            <div>
                <p class="text-muted">Política aplicada</p>
                <p><?php echo e($cuenta->politica->nombre); ?> (<?php echo e($cuenta->politica->plazo_dias); ?> días)</p>
            </div>

            <div>
                <p class="text-muted">Fechas</p>
                <p>
                    Inicio: <?php echo e($cuenta->fecha_inicio->format('d/m/Y')); ?> <br>
                    Vence: <?php echo e($cuenta->fecha_vencimiento->format('d/m/Y')); ?>

                </p>
            </div>

            <div>
                <p class="text-muted">Montos</p>
                <p>
                    Capital inicial: $<?php echo e(number_format($cuenta->monto_capital_inicial, 2)); ?> <br>
                    Capital actual: $<?php echo e(number_format($cuenta->monto_capital_actual, 2)); ?>

                </p>
            </div>

            <div>
                <p class="text-muted">Intereses / Comisiones</p>
                <p>
                    Intereses acumulados: $<?php echo e(number_format($cuenta->intereses_acumulados, 2)); ?> <br>
                    Comisiones acumuladas: $<?php echo e(number_format($cuenta->comisiones_acumuladas, 2)); ?>

                </p>
            </div>

        </div>
    </div>

    
    <div class="card">
        <div class="card-header-row">
            <h2 class="card-title">Pagos registrados</h2>

            <?php if(!in_array($cuenta->estado, ['CANCELADO', 'INCOBRABLE', 'REFINANCIADO'])): ?>
                <a href="#form-pago" class="btn btn-primary btn-xs">
                    + Registrar pago
                </a>
            <?php endif; ?>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                <tr>
                    <th>Recibo</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Interés</th>
                    <th>Comisión</th>
                    <th>Capital</th>
                    <th>Forma pago</th>
                </tr>
                </thead>

                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $cuenta->pagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pago): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($pago->numero_recibo); ?></td>
                        <td><?php echo e($pago->fecha_pago->format('d/m/Y')); ?></td>
                        <td>$<?php echo e(number_format($pago->monto_total, 2)); ?></td>
                        <td>$<?php echo e(number_format($pago->monto_interes, 2)); ?></td>
                        <td>$<?php echo e(number_format($pago->monto_comision, 2)); ?></td>
                        <td>$<?php echo e(number_format($pago->monto_capital, 2)); ?></td>
                        <td><?php echo e($pago->forma_pago ?? '-'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-muted empty-state text-center">
                            No hay pagos registrados aún.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php if(!in_array($cuenta->estado, ['CANCELADO','INCOBRABLE','REFINANCIADO'])): ?>
    <div class="card" id="form-pago">
        <h2 class="card-title">Registrar pago</h2>
        <p class="page-subtitle">El sistema aplica primero intereses, luego comisiones y por último capital.</p>

        <form method="POST" action="<?php echo e(route('pagos.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="cuenta_id" value="<?php echo e($cuenta->id); ?>">

            <div class="form-grid">

                <div class="form-group">
                    <label for="fecha_pago">Fecha</label>
                    <input type="date" id="fecha_pago" name="fecha_pago"
                           class="input"
                           value="<?php echo e(old('fecha_pago', now()->format('Y-m-d'))); ?>" required>
                </div>

                <div class="form-group">
                    <label for="monto_total">Monto total pagado</label>
                    <input type="number" step="0.01" id="monto_total" name="monto_total"
                           class="input" value="<?php echo e(old('monto_total')); ?>" required>
                </div>

                <div class="form-group">
                    <label for="forma_pago">Forma de pago</label>
                    <input type="text" id="forma_pago" name="forma_pago"
                           class="input"
                           value="<?php echo e(old('forma_pago', 'EFECTIVO')); ?>">
                </div>

                <div class="form-group" style="grid-column:1 / -1;">
                    <label for="observaciones">Observaciones</label>
                    <textarea id="observaciones" name="observaciones" rows="2"
                              class="input"><?php echo e(old('observaciones')); ?></textarea>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrar pago</button>
            </div>

        </form>

    </div>
    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\proyectos\financieros\resources\views/cuentas/show.blade.php ENDPATH**/ ?>