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
 Schema::create('cuentas_por_cobrar', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cliente_id')->constrained('clientes');
    $table->foreignId('politica_credito_id')->constrained('politicas_credito');
    $table->foreignId('fiador_id')->nullable()->constrained('fiadores');
    $table->foreignId('usuario_responsable_id')->constrained('users');

    $table->string('numero_factura');
    $table->date('fecha_factura');
    $table->string('tipo_documento')->nullable();

    $table->decimal('monto_capital_inicial', 14, 2);
    $table->decimal('monto_capital_actual', 14, 2);
    $table->decimal('intereses_acumulados', 14, 2)->default(0);
    $table->decimal('comisiones_acumuladas', 14, 2)->default(0);

    $table->date('fecha_inicio');
    $table->date('fecha_vencimiento');

    $table->enum('estado', [
        'VIGENTE', 'EN_MORA', 'REFINANCIADO',
        'INCOBRABLE', 'CANCELADO', 'EMBARGO'
    ])->default('VIGENTE');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuenta_por_cobrars');
    }
};
