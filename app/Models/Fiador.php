<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fiador extends Model

{
    protected $table = 'fiadores';
    
    protected $fillable = ['nombre', 'dui', 'direccion', 'telefono'];

    public function cuentas()
    {
        return $this->hasMany(CuentaPorCobrar::class);
    }
}
