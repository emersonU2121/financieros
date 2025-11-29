<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refinanciamiento extends Model
{
    protected $fillable = [
        'cuenta_origen_id',
        'cuenta_nueva_id',
        'fecha',
        'motivo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function origen()
    {
        return $this->belongsTo(CuentaPorCobrar::class, 'cuenta_origen_id');
    }

    public function nueva()
    {
        return $this->belongsTo(CuentaPorCobrar::class, 'cuenta_nueva_id');
    }
}
