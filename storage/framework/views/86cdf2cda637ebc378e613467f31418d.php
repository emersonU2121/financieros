<?php $__env->startSection('title', 'Clientes'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    
    <div class="page-header">
        <div>
            <h1 class="page-title">Clientes</h1>
            <p class="page-subtitle">
                Catálogo de clientes que pueden tener cuentas por cobrar. 
                Administra aquí su información básica, clasificación y límite de crédito.
            </p>
        </div>

        <a href="<?php echo e(route('clientes.create')); ?>" class="btn btn-primary">
            + Nuevo cliente
        </a>
    </div>

    
    <div class="card card-filters">
        <div class="card-header-row">
            <h2 class="card-title">Filtros de búsqueda</h2>
        </div>

        <form method="GET" action="<?php echo e(route('clientes.index')); ?>">
            <div class="filters-grid">
                
                <div class="form-group">
                    <label for="buscar">Buscar</label>
                    <input
                        type="text"
                        id="buscar"
                        name="buscar"
                        class="input"
                        placeholder="Nombre, código, NIT o NRC"
                        value="<?php echo e(request('buscar')); ?>"
                    >
                </div>

                
                <div class="form-group">
                    <label for="tipo">Tipo de cliente</label>
                    <select id="tipo" name="tipo" class="input">
                        <option value="">Todos</option>
                        <option value="NATURAL"  <?php echo e(request('tipo') === 'NATURAL' ? 'selected' : ''); ?>>Persona natural</option>
                        <option value="JURIDICA" <?php echo e(request('tipo') === 'JURIDICA' ? 'selected' : ''); ?>>Persona jurídica</option>
                    </select>
                </div>

                
                <div class="form-group">
                    <label for="solo_activos">Estado</label>
                    <select id="solo_activos" name="solo_activos" class="input">
                        <option value="">Todos</option>
                        <option value="1" <?php echo e(request('solo_activos') === '1' ? 'selected' : ''); ?>>Solo activos</option>
                        <option value="0" <?php echo e(request('solo_activos') === '0' ? 'selected' : ''); ?>>Solo inactivos</option>
                    </select>
                </div>

                
                <div class="filters-actions">
                    <button type="submit" class="btn btn-secondary">
                        Aplicar filtros
                    </button>
                    <a href="<?php echo e(route('clientes.index')); ?>" class="btn btn-light">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    
    <div class="card">
        <div class="card-header-row">
            <h2 class="card-title">Listado de clientes</h2>
            <span class="badge"><?php echo e($clientes->total()); ?> registros</span>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre / Razón social</th>
                        <th>Tipo</th>
                        <th>Clasificación</th>
                        <th>Límite de crédito (US$)</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th style="width: 180px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        
                        <td><?php echo e($cliente->codigo); ?></td>

                        
                        <td>
                            <strong><?php echo e($cliente->nombre); ?></strong><br>
                            <span class="text-sm" style="font-size: 0.78rem; color: var(--text-muted);">
                                DUI: <?php echo e($cliente->dui ?? '—'); ?> · NRC: <?php echo e($cliente->nrc ?? '—'); ?>

                            </span>
                        </td>

                        
                        <td>
                            <?php echo e($cliente->tipo === 'JURIDICA' ? 'Jurídica' : 'Natural'); ?>

                        </td>

                        
                        <td>
                            <?php echo e(optional($cliente->clasificacion)->nombre ?? 'Sin clasificar'); ?>

                        </td>

                        
                        <td>
                            US$ <?php echo e(number_format($cliente->limite_credito, 2)); ?>

                        </td>

                        
                        <td><?php echo e($cliente->telefono ?? '—'); ?></td>

                        
                        <td>
                            <?php if($cliente->activo): ?>
                                <span class="status-pill status-vigente">Activo</span>
                            <?php else: ?>
                                <span class="status-pill status-incobrable">Inactivo</span>
                            <?php endif; ?>
                        </td>

                        
                        <td>
                            <div class="table-actions">
                                <a href="<?php echo e(route('clientes.show', $cliente)); ?>" class="btn btn-xs btn-light">
                                    Ver
                                </a>
                                <a href="<?php echo e(route('clientes.edit', $cliente)); ?>" class="btn btn-xs btn-secondary">
                                    Editar
                                </a>
                                <form action="<?php echo e(route('clientes.destroy', $cliente)); ?>" method="POST" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button
                                        type="submit"
                                        class="btn btn-xs btn-light"
                                        onclick="return confirm('¿Deseas desactivar este cliente?')"
                                    >
                                        Desactivar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8">
                            <p class="empty-state">
                                No se encontraron clientes. Registra uno nuevo con el botón
                                <strong>“+ Nuevo cliente”</strong>.
                            </p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="mt-20">
            <?php echo e($clientes->withQueryString()->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/josue-avalos/SistemaANF/financieros/resources/views/clientes/index.blade.php ENDPATH**/ ?>