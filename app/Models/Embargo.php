<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Embargo extends Model
{
    protected $fillable = [
        'cuenta_id',
        'fecha_inicio',
        'estado_proceso',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentaPorCobrar::class, 'cuenta_id');
    }
}
