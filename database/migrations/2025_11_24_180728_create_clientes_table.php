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
      Schema::create('clientes', function (Blueprint $table) {
    $table->id();
    $table->enum('tipo', ['NATURAL', 'JURIDICA']);
    $table->string('codigo')->unique();
    $table->string('nombre');
    $table->string('giro')->nullable();
    $table->string('direccion')->nullable();
    $table->string('zona')->nullable();
    $table->string('telefono')->nullable();

    // Identificación
    $table->string('dui')->nullable();
    $table->string('nit')->nullable();
    $table->string('nrc')->nullable();

    // Info NATURAL
    $table->string('estado_civil')->nullable();
    $table->string('lugar_trabajo')->nullable();
    $table->decimal('ingresos_mensuales', 12, 2)->nullable();
    $table->decimal('egresos_mensuales', 12, 2)->nullable();

    // Info JURIDICA
    $table->decimal('total_activos', 14, 2)->nullable();
    $table->decimal('total_pasivos', 14, 2)->nullable();
    $table->decimal('ventas_anuales', 14, 2)->nullable();
    $table->decimal('utilidad_neta', 14, 2)->nullable();

    // Gestión de crédito
    $table->foreignId('clasificacion_id')->nullable()
          ->constrained('clasificaciones_clientes');
    $table->decimal('limite_credito', 14, 2)->default(0);

    $table->boolean('activo')->default(true);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
