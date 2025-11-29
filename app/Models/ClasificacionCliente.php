<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClasificacionCliente extends Model
{
        protected $table = 'clasificaciones_clientes';

    protected $fillable = ['codigo', 'descripcion'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'clasificacion_id');
    }
}
