<?php

namespace App\Console\Commands;

use App\Models\Partido;
use App\Models\Prediccion;
use App\Services\PrediccionService;
use App\Services\WorldCupApiService;
use Illuminate\Console\Command;

class SyncPartidos extends Command
{
    protected $signature   = 'mundial:sync';
    protected $description = 'Sincroniza partidos y genera/valida predicciones';

    public function handle(WorldCupApiService $api, PrediccionService $prediccion): void
    {
        $synced = $api->syncPartidos();
        $this->info("Sincronizados: {$synced}");

        // Genera predicción para todo partido que aún no tenga una.
        // Incluye los ya finalizados para poder medir la precisión real del algoritmo.
        Partido::whereDoesntHave('prediccion')
            ->get()
            ->each(fn ($p) => $prediccion->generarPrediccion($p));

        // Valida toda predicción pendiente cuyo partido ya terminó.
        Prediccion::where('resultado', 'pendiente')
            ->whereHas('partido', fn ($q) => $q->where('estado', 'finished'))
            ->get()
            ->each(fn ($pred) => $prediccion->validarPrediccion($pred));

        $this->info('Sync: ' . now()->format('H:i:s'));
    }
}
