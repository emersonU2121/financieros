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
       Schema::create('embargos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cuenta_id')->constrained('cuentas_por_cobrar');
    $table->date('fecha_inicio');
    $table->string('estado_proceso')->nullable();
    $table->text('observaciones')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('embargos');
    }
};
