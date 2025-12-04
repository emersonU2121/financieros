<?php $__env->startSection('title', 'Detalle de cliente'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">

    
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <?php echo e($cliente->codigo); ?> · <?php echo e($cliente->nombre); ?>

            </h1>
            <p class="page-subtitle">
                Tipo: <?php echo e($cliente->tipo === 'NATURAL' ? 'Persona natural' : 'Persona jurídica'); ?>

                <?php if($cliente->clasificacion): ?>
                    · Clasificación: <?php echo e($cliente->clasificacion->nombre); ?>

                <?php endif; ?>
            </p>
        </div>

        <div class="flex gap-2">
            <a href="<?php echo e(route('clientes.edit', $cliente)); ?>" class="btn btn-secondary">
                Editar
            </a>
            <a href="<?php echo e(route('clientes.index')); ?>" class="btn btn-light">
                ← Volver al listado
            </a>
        </div>
    </div>

    
    <section class="card">
        <h2 class="card-title">Datos generales</h2>

        <div class="form-grid">
            <div>
                <p class="text-muted">Código interno</p>
                <p><?php echo e($cliente->codigo); ?></p>
            </div>

            <div>
                <p class="text-muted">Giro / actividad</p>
                <p><?php echo e($cliente->giro ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-muted">Teléfono</p>
                <p><?php echo e($cliente->telefono ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-muted">Dirección</p>
                <p><?php echo e($cliente->direccion ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-muted">Zona</p>
                <p><?php echo e($cliente->zona ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-muted">Estado</p>
                <p>
                    <?php if($cliente->activo): ?>
                        <span class="status-pill status-vigente">Activo</span>
                    <?php else: ?>
                        <span class="status-pill status-incobrable">Inactivo</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </section>

    
    <section class="card mt-20">
        <h2 class="card-title">Identificación</h2>

        <div class="form-grid">
            <div>
                <p class="text-muted">DUI</p>
                <p><?php echo e($cliente->dui ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-muted">NIT</p>
                <p><?php echo e($cliente->nit ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-muted">NRC</p>
                <p><?php echo e($cliente->nrc ?? '—'); ?></p>
            </div>
        </div>
    </section>

    
    <section class="card mt-20">
        <h2 class="card-title">Información financiera / laboral</h2>

        <div class="form-grid">
            <div>
                <p class="text-muted">Estado civil</p>
                <p><?php echo e($cliente->estado_civil ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-muted">Lugar de trabajo</p>
                <p><?php echo e($cliente->lugar_trabajo ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-muted">Ingresos mensuales (US$)</p>
                <p>
                    <?php if(!is_null($cliente->ingresos_mensuales)): ?>
                        $<?php echo e(number_format($cliente->ingresos_mensuales, 2)); ?>

                    <?php else: ?>
                        —
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <p class="text-muted">Egresos mensuales (US$)</p>
                <p>
                    <?php if(!is_null($cliente->egresos_mensuales)): ?>
                        $<?php echo e(number_format($cliente->egresos_mensuales, 2)); ?>

                    <?php else: ?>
                        —
                    <?php endif; ?>
                </p>
            </div>

            <div>
                <p class="text-muted">Límite de crédito (US$)</p>
                <p>$<?php echo e(number_format($cliente->limite_credito, 2)); ?></p>
            </div>
            <div>
                <p class="text-muted">Clasificación de riesgo</p>
                <p><?php echo e($cliente->clasificacion->nombre ?? 'Sin clasificar'); ?></p>
            </div>
        </div>
    </section>

    
    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Cuentas por cobrar asociadas</h2>
            <span class="badge">
                <?php echo e($cliente->cuentas->count()); ?> <?php echo e(\Illuminate\Support\Str::plural('cuenta', $cliente->cuentas->count())); ?>

            </span>
        </div>

        <?php if($cliente->cuentas->isEmpty()): ?>
            <p class="empty-state">Este cliente aún no tiene cuentas por cobrar registradas.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                    <tr>
                        <th>N° factura</th>
                        <th>Fecha inicio</th>
                        <th>Vence</th>
                        <th>Capital actual</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $cliente->cuentas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cuenta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($cuenta->numero_factura); ?></td>
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
                                    Ver cuenta
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/josue-avalos/SistemaANF/financieros/resources/views/clientes/show.blade.php ENDPATH**/ ?>