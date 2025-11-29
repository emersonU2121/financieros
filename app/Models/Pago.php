<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'cuenta_id','usuario_id','numero_recibo','fecha_pago',
        'monto_total','monto_interes','monto_comision','monto_capital',
        'forma_pago','observaciones'
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto_total' => 'decimal:2',
        'monto_interes' => 'decimal:2',
        'monto_comision' => 'decimal:2',
        'monto_capital' => 'decimal:2'
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentaPorCobrar::class, 'cuenta_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
