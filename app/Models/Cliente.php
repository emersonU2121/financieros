<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'tipo','codigo','nombre','giro','direccion','zona','telefono',
        'dui','nit','nrc','estado_civil','lugar_trabajo','ingresos_mensuales',
        'egresos_mensuales','total_activos','total_pasivos','ventas_anuales',
        'utilidad_neta','clasificacion_id','limite_credito','activo'
    ];

    protected $casts = [
        'ingresos_mensuales' => 'decimal:2',
        'egresos_mensuales' => 'decimal:2',
        'total_activos' => 'decimal:2',
        'total_pasivos' => 'decimal:2',
        'ventas_anuales' => 'decimal:2',
        'utilidad_neta' => 'decimal:2',
        'limite_credito' => 'decimal:2',
        'activo' => 'boolean'
    ];

    public function clasificacion()
    {
        return $this->belongsTo(ClasificacionCliente::class, 'clasificacion_id');
    }

    public function cuentas()
    {
        return $this->hasMany(CuentaPorCobrar::class, 'cliente_id');
    }
}
