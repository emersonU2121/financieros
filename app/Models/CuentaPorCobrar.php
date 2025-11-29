<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;   // 👈 (opcional pero recomendado)

class CuentaPorCobrar extends Model
{
    use HasFactory;  // 👈

        protected $table = 'cuentas_por_cobrar';

    protected $fillable = [
        'cliente_id','politica_credito_id','fiador_id','usuario_responsable_id',
        'numero_factura','fecha_factura','tipo_documento',
        'monto_capital_inicial','monto_capital_actual',
        'intereses_acumulados','comisiones_acumuladas',
        'fecha_inicio','fecha_vencimiento','estado'
    ];

    protected $casts = [
        'monto_capital_inicial' => 'decimal:2',
        'monto_capital_actual' => 'decimal:2',
        'intereses_acumulados' => 'decimal:2',
        'comisiones_acumuladas' => 'decimal:2',
        'fecha_factura' => 'date',
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function politica()
    {
        return $this->belongsTo(PoliticaCredito::class, 'politica_credito_id');
    }

    public function fiador()
    {
        return $this->belongsTo(Fiador::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'usuario_responsable_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'cuenta_id');
    }

    public function embargo()
    {
        return $this->hasOne(Embargo::class, 'cuenta_id');
    }
}
