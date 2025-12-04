<?php $__env->startSection('title', 'Nueva cuenta por cobrar'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">

    
    <div class="page-header">
        <div>
            <h1 class="page-title">Nueva cuenta por cobrar</h1>
            <p class="page-subtitle">Registra una nueva cuenta generada por una factura a crédito.</p>
        </div>
        <a href="<?php echo e(route('cuentas.index')); ?>" class="btn btn-light">← Volver</a>
    </div>

    
    <div class="card">

        <div class="card-header-row">
            <h2 class="card-title">Formulario de registro</h2>
        </div>

        <form method="POST" action="<?php echo e(route('cuentas.store')); ?>">
            <?php echo csrf_field(); ?>

            
            <div class="filters-grid" style="margin-top: 1rem;">

                
                <div class="form-group">
                    <label for="cliente_id">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="input" required>
                        <option value="">Seleccione un cliente</option>
                        <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cliente->id); ?>" <?php echo e(old('cliente_id')==$cliente->id?'selected':''); ?>>
                                <?php echo e($cliente->codigo); ?> · <?php echo e($cliente->nombre); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="form-group">
                    <label for="politica_credito_id">Política de crédito</label>
                    <select name="politica_credito_id" id="politica_credito_id" class="input" required>
                        <option value="">Seleccione una política</option>
                        <?php $__currentLoopData = $politicas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>" <?php echo e(old('politica_credito_id')==$p->id?'selected':''); ?>>
                                <?php echo e($p->nombre); ?> —
                                <?php echo e($p->plazo_dias); ?> días · <?php echo e($p->tasa_interes_anual); ?>% interés
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="form-group">
                    <label for="usuario_responsable_id">Analista / Responsable</label>
                    <select name="usuario_responsable_id" id="usuario_responsable_id" class="input" required>
                        <option value="">Seleccione usuario</option>
                        <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>" <?php echo e(old('usuario_responsable_id')==$u->id?'selected':''); ?>>
                                <?php echo e($u->name); ?> (<?php echo e($u->email); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="form-group">
                    <label for="numero_factura">Número de factura</label>
                    <input type="text" class="input"
                           name="numero_factura"
                           value="<?php echo e(old('numero_factura')); ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="fecha_factura">Fecha de factura</label>
                    <input type="date" class="input"
                           name="fecha_factura"
                           value="<?php echo e(old('fecha_factura')); ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="tipo_documento">Tipo de documento</label>
                    <input type="text" class="input"
                           name="tipo_documento"
                           value="<?php echo e(old('tipo_documento','FACTURA_CREDITO')); ?>">
                </div>

                
                <div class="form-group">
                    <label for="monto_capital_inicial">Monto financiado (capital)</label>
                    <input type="number" step="0.01" class="input"
                           name="monto_capital_inicial"
                           value="<?php echo e(old('monto_capital_inicial')); ?>"
                           required>
                </div>

                
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de inicio del crédito</label>
                    <input type="date" class="input"
                           name="fecha_inicio"
                           value="<?php echo e(old('fecha_inicio')); ?>"
                           required>
                </div>

                
                <div class="form-group">
                    <label for="fiador_nombre">Fiador — Nombre</label>
                    <input type="text" class="input"
                           name="fiador_nombre"
                           value="<?php echo e(old('fiador_nombre')); ?>">
                </div>

                <div class="form-group">
                    <label for="fiador_dui">Fiador — DUI</label>
                    <input type="text" class="input"
                           name="fiador_dui"
                           value="<?php echo e(old('fiador_dui')); ?>">
                </div>

                <div class="form-group">
                    <label for="fiador_direccion">Fiador — Dirección</label>
                    <input type="text" class="input"
                           name="fiador_direccion"
                           value="<?php echo e(old('fiador_direccion')); ?>">
                </div>

                <div class="form-group">
                    <label for="fiador_telefono">Fiador — Teléfono</label>
                    <input type="text" class="input"
                           name="fiador_telefono"
                           value="<?php echo e(old('fiador_telefono')); ?>">
                </div>
            </div>

            
            <div class="filters-actions" style="margin-top: 1.8rem;">
                <a href="<?php echo e(route('cuentas.index')); ?>" class="btn btn-light">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar cuenta</button>
            </div>

        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/josue-avalos/SistemaANF/financieros/resources/views/cuentas/create.blade.php ENDPATH**/ ?>