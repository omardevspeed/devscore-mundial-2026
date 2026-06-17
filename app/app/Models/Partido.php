<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Partido extends Model
{
    protected $table = 'partidos';

    protected $fillable = [
        'api_id',
        'equipo_local',
        'equipo_visitante',
        'bandera_local',
        'bandera_visitante',
        'fecha_partido',
        'estado',
        'goles_local',
        'goles_visitante',
        'grupo',
        'fase',
    ];

    protected $casts = [
        'fecha_partido'   => 'datetime',
        'goles_local'     => 'integer',
        'goles_visitante' => 'integer',
    ];

    public function prediccion(): HasOne
    {
        return $this->hasOne(Prediccion::class);
    }

    public function estaEnVivo(): bool
    {
        return in_array($this->estado, ['live', 'in_play', 'inprogress', 'playing']);
    }

    public function estaFinalizado(): bool
    {
        return $this->estado === 'finished';
    }
}
