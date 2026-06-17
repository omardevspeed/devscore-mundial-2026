<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->string('api_id')->unique();
            $table->string('equipo_local');
            $table->string('equipo_visitante');
            $table->string('bandera_local')->nullable();
            $table->string('bandera_visitante')->nullable();
            $table->datetime('fecha_partido');
            $table->string('estado');
            $table->integer('goles_local')->nullable();
            $table->integer('goles_visitante')->nullable();
            $table->string('grupo')->nullable();
            $table->string('fase');
            $table->timestamps();
            $table->index(['estado', 'fecha_partido']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};
