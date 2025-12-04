<?php $__env->startSection('title', 'Editar cuenta por cobrar'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Editar cuenta por cobrar</h1>
            <p class="page-subtitle">
                Ajusta los datos principales de la cuenta seleccionada.
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-header-row">
            <h2 class="card-title">Datos de la cuenta</h2>
            <span class="badge">ID #<?php echo e($cuenta->id); ?></span>
        </div>

        <form method="POST" action="<?php echo e(route('cuentas.update', $cuenta)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="filters-grid">
                
                <div class="form-group">
                    <label for="cliente_id">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="input" required>
                        <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cliente->id); ?>"
                                <?php echo e(old('cliente_id', $cuenta->cliente_id) == $cliente->id ? 'selected' : ''); ?>>
                                <?php echo e($cliente->codigo); ?> · <?php echo e($cliente->nombre); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="form-group">
                    <label for="politica_credito_id">Política de crédito</label>
                    <select name="politica_credito_id" id="politica_credito_id" class="input" required>
                        <?php $__currentLoopData = $politicas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>"
                                <?php echo e(old('politica_credito_id', $cuenta->politica_credito_id) == $p->id ? 'selected' : ''); ?>>
                                <?php echo e($p->nombre); ?> (<?php echo e($p->plazo_dias); ?> días · <?php echo e($p->tasa_interes_anual); ?>% anual)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="form-group">
                    <label for="usuario_responsable_id">Analista / Responsable</label>
                    <select name="usuario_responsable_id" id="usuario_responsable_id" class="input" required>
                        <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>"
                                <?php echo e(old('usuario_responsable_id', $cuenta->usuario_responsable_id) == $u->id ? 'selected' : ''); ?>>
                                <?php echo e($u->name); ?> (<?php echo e($u->email); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="form-group">
                    <label for="numero_factura">Número de factura</label>
                    <input type="text" name="numero_factura" id="numero_factura" class="input"
                           value="<?php echo e(old('numero_factura', $cuenta->numero_factura)); ?>" required>
                </div>

                
                <div class="form-group">
                    <label for="fecha_factura">Fecha de factura</label>
                    <input type="date" name="fecha_factura" id="fecha_factura" class="input"
                           value="<?php echo e(old('fecha_factura', optional($cuenta->fecha_factura)->format('Y-m-d'))); ?>" required>
                </div>

                
                <div class="form-group">
                    <label for="tipo_documento">Tipo de documento</label>
                    <input type="text" name="tipo_documento" id="tipo_documento" class="input"
                           value="<?php echo e(old('tipo_documento', $cuenta->tipo_documento)); ?>" required>
                </div>

                
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de inicio del crédito</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="input"
                           value="<?php echo e(old('fecha_inicio', optional($cuenta->fecha_inicio)->format('Y-m-d'))); ?>" required>
                </div>

                
                <div class="form-group">
                    <label for="estado">Estado de la cuenta</label>
                    <select name="estado" id="estado" class="input" required>
                        <?php $__currentLoopData = ['VIGENTE','EN_MORA','INCOBRABLE','REFINANCIADO','CANCELADO']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($estado); ?>"
                                <?php echo e(old('estado', $cuenta->estado) == $estado ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst(strtolower($estado))); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="form-group">
                    <label for="fiador_nombre">Fiador - Nombre</label>
                    <input type="text" name="fiador_nombre" id="fiador_nombre" class="input"
                           value="<?php echo e(old('fiador_nombre', optional($cuenta->fiador)->nombre)); ?>">
                </div>

                
                <div class="form-group">
                    <label for="fiador_dui">Fiador - DUI</label>
                    <input type="text" name="fiador_dui" id="fiador_dui" class="input"
                           value="<?php echo e(old('fiador_dui', optional($cuenta->fiador)->dui)); ?>">
                </div>

                
                <div class="form-group">
                    <label for="fiador_direccion">Fiador - Dirección</label>
                    <input type="text" name="fiador_direccion" id="fiador_direccion" class="input"
                           value="<?php echo e(old('fiador_direccion', optional($cuenta->fiador)->direccion)); ?>">
                </div>

                
                <div class="form-group">
                    <label for="fiador_telefono">Fiador - Teléfono</label>
                    <input type="text" name="fiador_telefono" id="fiador_telefono" class="input"
                           value="<?php echo e(old('fiador_telefono', optional($cuenta->fiador)->telefono)); ?>">
                </div>
            </div>

            <div class="form-actions mt-20">
                <a href="<?php echo e(route('cuentas.show', $cuenta)); ?>" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/josue-avalos/SistemaANF/financieros/resources/views/cuentas/edit.blade.php ENDPATH**/ ?>