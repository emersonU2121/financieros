<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('pagos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cuenta_id')->constrained('cuentas_por_cobrar');
    $table->foreignId('usuario_id')->constrained('users');

    $table->string('numero_recibo')->unique();
    $table->date('fecha_pago');

    $table->decimal('monto_total', 14, 2);
    $table->decimal('monto_interes', 14, 2);
    $table->decimal('monto_comision', 14, 2);
    $table->decimal('monto_capital', 14, 2);

    $table->string('forma_pago')->nullable();
    $table->text('observaciones')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
