<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoliticaCredito extends Model
{

        protected $table = 'politicas_credito';

    protected $fillable = [
        'nombre','plazo_dias','tasa_interes_anual','tasa_mora_anual',
        'comision_inicial','dias_gracia','dias_para_mora',
        'dias_para_incobrable','requiere_fiador'
    ];

    protected $casts = [
        'tasa_interes_anual' => 'decimal:2',
        'tasa_mora_anual' => 'decimal:2',
        'comision_inicial' => 'decimal:2',
        'requiere_fiador' => 'boolean'
    ];

    public function cuentas()
    {
        return $this->hasMany(CuentaPorCobrar::class);
    }
}
