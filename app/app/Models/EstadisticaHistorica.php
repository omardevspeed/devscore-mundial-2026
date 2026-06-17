<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadisticaHistorica extends Model
{
    protected $table = 'estadisticas_historicas';

    protected $fillable = [
        'equipo',
        'torneo',
        'partidos',
        'victorias',
        'empates',
        'derrotas',
        'goles_favor',
        'goles_contra',
        'fase_alcanzada',
        'indice_fuerza',
    ];

    protected $casts = [
        'torneo'        => 'integer',
        'partidos'      => 'integer',
        'victorias'     => 'integer',
        'empates'       => 'integer',
        'derrotas'      => 'integer',
        'goles_favor'   => 'integer',
        'goles_contra'  => 'integer',
        'indice_fuerza' => 'float',
    ];
}
