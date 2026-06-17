<?php

namespace App\Services;

use App\Helpers\BanderaHelper;
use App\Models\Partido;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorldCupApiService
{
    /**
     * Fuente gratuita y sin key con el fixture + resultados del Mundial 2026.
     * Mismo formato openfootball usado para los históricos.
     */
    private string $url = 'https://raw.githubusercontent.com/openfootball/worldcup.json/master/2026/worldcup.json';

    public function syncPartidos(): int
    {
        try {
            $response = Http::timeout(20)->get($this->url);
        } catch (\Throwable $e) {
            Log::warning('Error al sincronizar partidos: ' . $e->getMessage());
            return 0;
        }

        if (!$response->successful()) {
            return 0;
        }

        $data    = $response->json();
        $matches = $data['matches'] ?? (is_array($data) ? $data : []);

        if (!is_array($matches)) {
            return 0;
        }

        $synced = 0;

        foreach ($matches as $match) {
            $local     = $match['team1'] ?? $match['home'] ?? 'TBD';
            $visitante = $match['team2'] ?? $match['away'] ?? 'TBD';

            if ($local === 'TBD' && $visitante === 'TBD') {
                continue;
            }

            $golesLocal     = $match['score']['ft'][0] ?? null;
            $golesVisitante = $match['score']['ft'][1] ?? null;
            $finalizado     = $golesLocal !== null && $golesVisitante !== null;

            $apiId = (string) ($match['id']
                ?? md5(($match['round'] ?? '') . '|' . $local . '|' . $visitante . '|' . ($match['date'] ?? '')));

            Partido::updateOrCreate(
                ['api_id' => $apiId],
                [
                    'equipo_local'      => $local,
                    'equipo_visitante'  => $visitante,
                    'bandera_local'     => BanderaHelper::get($local),
                    'bandera_visitante' => BanderaHelper::get($visitante),
                    'fecha_partido'     => $this->parseFecha($match['date'] ?? null, $match['time'] ?? null),
                    'estado'            => $finalizado ? 'finished' : 'scheduled',
                    'goles_local'       => $golesLocal,
                    'goles_visitante'   => $golesVisitante,
                    'grupo'             => $match['group'] ?? null,
                    'fase'              => $this->mapFase($match['round'] ?? 'GROUP_STAGE'),
                ]
            );
            $synced++;
        }

        return $synced;
    }

    private function mapFase(string $round): string
    {
        $r = strtolower($round);

        return match (true) {
            str_contains($r, 'round of 32')          => 'ROUND_OF_32',
            str_contains($r, 'round of 16')          => 'ROUND_OF_16',
            str_contains($r, 'quarter')              => 'QUARTER_FINAL',
            str_contains($r, 'semi')                 => 'SEMI_FINAL',
            str_contains($r, 'third') || str_contains($r, '3rd') => 'THIRD_PLACE',
            str_contains($r, 'final')                => 'FINAL',
            default                                   => 'GROUP_STAGE',
        };
    }

    private function parseFecha(?string $fecha, ?string $hora): Carbon
    {
        if (empty($fecha)) {
            return now();
        }

        // openfootball usa "13:00 UTC-6" -> separamos hora y offset.
        $hhmm   = '12:00';
        $offset = 'UTC';

        if (!empty($hora)) {
            if (preg_match('/(\d{1,2}:\d{2})/', $hora, $m)) {
                $hhmm = $m[1];
            }
            if (preg_match('/UTC([+-]\d{1,2})/', $hora, $m)) {
                $signo  = $m[1][0];
                $horas  = str_pad(ltrim($m[1], '+-'), 2, '0', STR_PAD_LEFT);
                $offset = "{$signo}{$horas}:00";
            }
        }

        try {
            return Carbon::parse("{$fecha} {$hhmm}", $offset === 'UTC' ? 'UTC' : $offset)
                ->setTimezone('UTC');
        } catch (\Throwable $e) {
            try {
                return Carbon::parse($fecha);
            } catch (\Throwable $e2) {
                return now();
            }
        }
    }
}
