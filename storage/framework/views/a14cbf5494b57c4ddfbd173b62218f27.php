<?php $__env->startSection('title', 'Nuevo Cliente'); ?>

<?php $__env->startSection('content'); ?>

<div class="page">

    
    <div class="page-header">
        <div>
            <h1 class="page-title">Nuevo Cliente</h1>
            <p class="page-subtitle">
                Registra un nuevo cliente para asociarlo a cuentas por cobrar, límites de crédito y clasificaciones de riesgo.
            </p>
        </div>

        <a href="<?php echo e(route('clientes.index')); ?>" class="btn btn-light">← Volver al listado</a>
    </div>

    
    <?php if($errors->any()): ?>
        <div class="card" style="border-left: 4px solid var(--danger); margin-bottom:1.5rem;">
            <h3 style="margin-top:0">Se encontraron errores:</h3>
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>


    <form action="<?php echo e(route('clientes.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        
        
        

        <div class="card">
            <h3 class="card-title">Datos generales del cliente</h3>

            <div class="filters-grid">

                
                <div class="form-group">
                    <label>Tipo de cliente *</label>
                    <select name="tipo" class="input" required>
                        <option value="">Seleccione…</option>
                        <option value="NATURAL">Persona Natural</option>
                        <option value="JURIDICA">Persona Jurídica</option>
                    </select>
                </div>

                
                <div class="form-group">
                    <label>Código interno *</label>
                    <input type="text" name="codigo" class="input" placeholder="Ej.: CLI-001" required>
                </div>

                
                <div class="form-group" style="grid-column: span 2;">
                    <label>Nombre / Razón social *</label>
                    <input type="text" name="nombre" class="input" placeholder="Nombre completo o empresa" required>
                </div>

                
                <div class="form-group" style="grid-column: span 2;">
                    <label>Giro del negocio / profesión</label>
                    <input type="text" name="giro" class="input" placeholder="Actividad económica">
                </div>

                
                <div class="form-group" style="grid-column: span 2;">
                    <label>Dirección</label>
                    <input type="text" name="direccion" class="input" placeholder="Dirección principal">
                </div>

                
                <div class="form-group">
                    <label>Zona</label>
                    <input type="text" name="zona" class="input" placeholder="Urbano, rural, zona 1…">
                </div>

                
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="input" placeholder="2222-2222">
                </div>

                
                <div class="form-group">
                    <label>DUI</label>
                    <input type="text" name="dui" class="input" placeholder="Solo persona natural">
                </div>

                
                <div class="form-group">
                    <label>NIT</label>
                    <input type="text" name="nit" class="input">
                </div>

                
                <div class="form-group">
                    <label>NRC</label>
                    <input type="text" name="nrc" class="input">
                </div>

            </div>
        </div>

        <br>

        
        
        

        <div class="card">
            <h3 class="card-title">Información para persona natural</h3>

            <div class="filters-grid">

                
                <div class="form-group">
                    <label>Estado civil</label>
                    <input type="text" name="estado_civil" class="input" placeholder="Soltero, casado, etc.">
                </div>

                
                <div class="form-group">
                    <label>Lugar de trabajo</label>
                    <input type="text" name="lugar_trabajo" class="input">
                </div>

                
                <div class="form-group">
                    <label>Ingresos mensuales (US$)</label>
                    <input type="number" name="ingresos_mensuales" class="input" min="0" step="0.01">
                </div>

                
                <div class="form-group">
                    <label>Egresos mensuales (US$)</label>
                    <input type="number" name="egresos_mensuales" class="input" min="0" step="0.01">
                </div>

            </div>
        </div>

        <br>

        
        
        

        <div class="card">
            <h3 class="card-title">Información financiera (empresa)</h3>

            <div class="filters-grid">

                
                <div class="form-group">
                    <label>Total activos (US$)</label>
                    <input type="number" name="total_activos" class="input" min="0" step="0.01">
                </div>

                
                <div class="form-group">
                    <label>Total pasivos (US$)</label>
                    <input type="number" name="total_pasivos" class="input" min="0" step="0.01">
                </div>

                
                <div class="form-group">
                    <label>Ventas anuales (US$)</label>
                    <input type="number" name="ventas_anuales" class="input" min="0" step="0.01">
                </div>

                
                <div class="form-group">
                    <label>Utilidad neta (US$)</label>
                    <input type="number" name="utilidad_neta" class="input" min="0" step="0.01">
                </div>

            </div>
        </div>

        <br>

        
        
        

        <div class="card">
            <h3 class="card-title">Clasificación y crédito</h3>

            <div class="filters-grid">

                
                <div class="form-group">
                    <label>Clasificación de riesgo</label>
                    <select name="clasificacion_id" class="input">
                        <option value="">Sin clasificación</option>
                        <?php $__currentLoopData = $clasificaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>"><?php echo e($c->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="form-group">
                    <label>Límite de crédito autorizado (US$) *</label>
                    <input type="number" name="limite_credito" class="input" min="0" step="0.01" required>
                </div>

                
                <div class="form-check">
    <input
        type="checkbox"
        name="activo"
        id="activo"
        value="1"
        <?php echo e(old('activo', 1) ? 'checked' : ''); ?>

    >
    <label for="activo">Cliente activo</label>
</div>


            </div>
        </div>

        <br>

        
        <div class="card" style="background: transparent; box-shadow:none;">
            <button class="btn btn-primary">Guardar cliente</button>
            <a href="<?php echo e(route('clientes.index')); ?>" class="btn btn-light">Cancelar</a>
        </div>

    </form>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/josue-avalos/SistemaANF/financieros/resources/views/clientes/create.blade.php ENDPATH**/ ?>