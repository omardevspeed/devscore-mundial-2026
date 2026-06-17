<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predicciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->integer('pred_goles_local');
            $table->integer('pred_goles_visitante');
            $table->string('pred_ganador');
            $table->decimal('confianza', 5, 2);
            $table->text('razonamiento');
            $table->enum('resultado', ['pendiente', 'exacto', 'correcto', 'incorrecto'])
                  ->default('pendiente');
            $table->integer('puntos_obtenidos')->default(0);
            $table->timestamps();
            $table->unique('partido_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predicciones');
    }
};
