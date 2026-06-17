<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediccion extends Model
{
    protected $table = 'predicciones';

    protected $fillable = [
        'partido_id',
        'pred_goles_local',
        'pred_goles_visitante',
        'pred_ganador',
        'confianza',
        'razonamiento',
        'resultado',
        'puntos_obtenidos',
    ];

    protected $casts = [
        'pred_goles_local'     => 'integer',
        'pred_goles_visitante' => 'integer',
        'confianza'            => 'float',
        'puntos_obtenidos'     => 'integer',
    ];

    public function partido(): BelongsTo
    {
        return $this->belongsTo(Partido::class);
    }
}
