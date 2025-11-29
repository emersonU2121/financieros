<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraAccion extends Model
{
    protected $table = 'bitacora_acciones';

    protected $fillable = [
        'user_id',
        'accion',
        'entidad',
        'entidad_id',
        'detalle',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
