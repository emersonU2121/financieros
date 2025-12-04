<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cartera General</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h1   { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background: #f2f2f2; }
        .resumen { margin-bottom: 15px; }
    </style>
</head>
<body>

<h1>Reporte de Cartera General</h1>

<div class="resumen">
    <p><strong>Total de cuentas:</strong> <?php echo e($cuentas->count()); ?></p>
    <p><strong>Saldo vigente:</strong> $<?php echo e(number_format($totales['vigente'] ?? 0, 2)); ?></p>
    <p><strong>Saldo total cartera:</strong> $<?php echo e(number_format($totales['total'] ?? 0, 2)); ?></p>
</div>

<table>
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
            <td><?php echo e($cuenta->estado); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

</body>
</html>
<?php /**PATH C:\proyectos\financieros\resources\views/reportes/pdf/cartera_general.blade.php ENDPATH**/ ?>