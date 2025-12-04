<?php $__env->startSection('title', 'Reporte de Cartera'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <div>
            <h1 class="page-title">Reporte de Cartera General</h1>
            <p class="page-subtitle">
                Resumen de todas las cuentas por cobrar: vigentes, en mora, refinanciadas,
                canceladas e incobrables.
            </p>
        </div>

        <a href="<?php echo e(route('cuentas.index')); ?>" class="btn btn-light">
            ← Volver a Cuentas por Cobrar
        </a>
    </div>

    <?php
        $totalCuentas = isset($cuentas) ? $cuentas->count() : 0;

        // Usamos $totales si viene del controlador, si no lo calculamos aquí para no romper nada.
        $saldoVigente = $totales['vigente'] ?? (isset($cuentas)
                ? $cuentas->where('estado', 'VIGENTE')->sum('monto_capital_actual')
                : 0);

        $saldoTotal = $totales['total'] ?? (isset($cuentas)
                ? $cuentas->sum('monto_capital_actual')
                : 0);
    ?>

    
    <section class="card card-filters">
        <h2 class="card-title">Resumen de la cartera</h2>

        <div class="filters-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
            <div class="form-group">
                <label>Total de cuentas</label>
                <div class="input" style="border-style:dashed; font-weight:600;">
                    <?php echo e($totalCuentas); ?>

                </div>
            </div>

            <div class="form-group">
                <label>Saldo vigente</label>
                <div class="input" style="border-style:dashed; color:#22c55e; font-weight:600;">
                    $<?php echo e(number_format($saldoVigente, 2)); ?>

                </div>
            </div>

            <div class="form-group">
                <label>Saldo total de cartera</label>
                <div class="input" style="border-style:dashed; color:#0ea5e9; font-weight:600;">
                    $<?php echo e(number_format($saldoTotal, 2)); ?>

                </div>
            </div>
        </div>
    </section>

    
    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Reportes disponibles</h2>
        </div>

        <div class="filters-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr)); gap:1.5rem;">
            
            <div class="card card-inner">
                <h3 class="card-title" style="margin-bottom:.25rem;">Cartera general</h3>
                <p class="text-muted" style="margin-bottom:.75rem;">
                    Todas las cuentas por cobrar con su saldo actual y estado.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?php echo e(route('reportes.cartera.export', ['formato' => 'pdf'])); ?>"
                       class="btn btn-light btn-sm">
                        PDF
                    </a>
                    <a href="<?php echo e(route('reportes.cartera.export', ['formato' => 'csv'])); ?>"
                       class="btn btn-outline-light btn-sm">
                        Excel (CSV)
                    </a>
                    <a href="<?php echo e(route('reportes.cartera.export', ['formato' => 'excel'])); ?>"
                       class="btn btn-outline-light btn-sm">
                        Excel (.xls)
                    </a>
                </div>
            </div>

            
            <div class="card card-inner">
                <h3 class="card-title" style="margin-bottom:.25rem;">Cuentas en mora</h3>
                <p class="text-muted" style="margin-bottom:.75rem;">
                    Cuentas cuyo vencimiento ya pasó y siguen pendientes de pago.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?php echo e(route('reportes.mora.export', ['formato' => 'pdf'])); ?>"
                       class="btn btn-light btn-sm">
                        PDF
                    </a>
                    <a href="<?php echo e(route('reportes.mora.export', ['formato' => 'csv'])); ?>"
                       class="btn btn-outline-light btn-sm">
                        Excel (CSV)
                    </a>
                    <a href="<?php echo e(route('reportes.mora.export', ['formato' => 'excel'])); ?>"
                       class="btn btn-outline-light btn-sm">
                        Excel (.xls)
                    </a>
                </div>
            </div>

            
            <div class="card card-inner">
                <h3 class="card-title" style="margin-bottom:.25rem;">Cuentas incobrables</h3>
                <p class="text-muted" style="margin-bottom:.75rem;">
                    Cuentas marcadas como incobrables por decisión de la institución.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?php echo e(route('reportes.incobrables.export', ['formato' => 'pdf'])); ?>"
                       class="btn btn-light btn-sm">
                        PDF
                    </a>
                    <a href="<?php echo e(route('reportes.incobrables.export', ['formato' => 'csv'])); ?>"
                       class="btn btn-outline-light btn-sm">
                        Excel (CSV)
                    </a>
                    <a href="<?php echo e(route('reportes.incobrables.export', ['formato' => 'excel'])); ?>"
                       class="btn btn-outline-light btn-sm">
                        Excel (.xls)
                    </a>
                </div>
            </div>

            
            <div class="card card-inner">
                <h3 class="card-title" style="margin-bottom:.25rem;">Cartera por zona geográfica</h3>
                <p class="text-muted" style="margin-bottom:.75rem;">
                    Total de cuentas y saldo agrupado por zona del cliente.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?php echo e(route('reportes.por_zona.export', ['formato' => 'pdf'])); ?>"
                       class="btn btn-light btn-sm">
                        PDF
                    </a>
                    <a href="<?php echo e(route('reportes.por_zona.export', ['formato' => 'csv'])); ?>"
                       class="btn btn-outline-light btn-sm">
                        Excel (CSV)
                    </a>
                    <a href="<?php echo e(route('reportes.por_zona.export', ['formato' => 'excel'])); ?>"
                       class="btn btn-outline-light btn-sm">
                        Excel (.xls)
                    </a>
                </div>
            </div>

            
            <div class="card card-inner">
                <h3 class="card-title" style="margin-bottom:.25rem;">Cartera por tipo de cliente</h3>
                <p class="text-muted" style="margin-bottom:.75rem;">
                    Saldo total por tipo de cliente (natural/jurídica) y clasificación (A, B, C, D).
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?php echo e(route('reportes.por_tipo_cliente.export', ['formato' => 'pdf'])); ?>"
                       class="btn btn-light btn-sm">
                        PDF
                    </a>
                    <a href="<?php echo e(route('reportes.por_tipo_cliente.export', ['formato' => 'csv'])); ?>"
                       class="btn btn-outline-light btn-sm">
                        Excel (CSV)
                    </a>
                    <a href="<?php echo e(route('reportes.por_tipo_cliente.export', ['formato' => 'excel'])); ?>"
                       class="btn btn-outline-light btn-sm">
                        Excel (.xls)
                    </a>
                </div>
            </div>
        </div>
    </section>

    
    <section class="card mt-20">
        <div class="card-header-row">
            <h2 class="card-title">Detalle de cuentas por cobrar</h2>
            <span class="badge">
                <?php echo e($totalCuentas); ?> <?php echo e(\Illuminate\Support\Str::plural('registro', $totalCuentas)); ?>

            </span>
        </div>

        <?php if(!isset($cuentas) || $cuentas->isEmpty()): ?>
            <p class="empty-state">
                No hay cuentas registradas para mostrar en el reporte de cartera.
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
                            <th>Capital inicial</th>
                            <th>Capital actual</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $cuentas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cuenta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($cuenta->numero_factura); ?></td>
                                <td><?php echo e($cuenta->cliente->nombre_completo ?? $cuenta->cliente->nombre); ?></td>
                                <td><?php echo e(optional($cuenta->fecha_inicio)->format('d/m/Y')); ?></td>
                                <td><?php echo e(optional($cuenta->fecha_vencimiento)->format('d/m/Y')); ?></td>
                                <td>$<?php echo e(number_format($cuenta->monto_capital_inicial, 2)); ?></td>
                                <td>$<?php echo e(number_format($cuenta->monto_capital_actual, 2)); ?></td>
                                <td>
                                    <span class="status-pill status-<?php echo e(strtolower($cuenta->estado)); ?>">
                                        <?php echo e(ucfirst(strtolower($cuenta->estado))); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\proyectos\financieros\resources\views/reportes/cartera_general.blade.php ENDPATH**/ ?>