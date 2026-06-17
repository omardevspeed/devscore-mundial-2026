<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estadisticas_historicas', function (Blueprint $table) {
            $table->id();
            $table->string('equipo');
            $table->integer('torneo');
            $table->integer('partidos');
            $table->integer('victorias');
            $table->integer('empates');
            $table->integer('derrotas');
            $table->integer('goles_favor');
            $table->integer('goles_contra');
            $table->string('fase_alcanzada');
            $table->decimal('indice_fuerza', 8, 2)->default(0);
            $table->timestamps();
            $table->unique(['equipo', 'torneo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estadisticas_historicas');
    }
};
