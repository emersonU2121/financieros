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
    Schema::create('politicas_credito', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->integer('plazo_dias');
    $table->decimal('tasa_interes_anual', 5, 2);
    $table->decimal('tasa_mora_anual', 5, 2)->default(0);
    $table->decimal('comision_inicial', 5, 2)->default(0);
    $table->integer('dias_gracia')->default(0);
    $table->integer('dias_para_mora')->default(0);
    $table->integer('dias_para_incobrable')->default(90);
    $table->boolean('requiere_fiador')->default(false);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('politica_creditos');
    }
};
