<?php

namespace App\Services;

use App\Helpers\BanderaHelper;
use App\Models\Partido;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorldCupApiService
{
    private string $openfootballUrl = 'https://raw.githubusercontent.com/openfootball/worldcup.json/master/2026/worldcup.json';

    public function syncPartidos(): int
    {
        $token = config('services.football_data.token');

        [$registros, $seen] = $token
            ? $this->fetchFootballData($token)
            : $this->fetchOpenfootball();

        if (empty($registros)) {
            return 0;
        }

        foreach ($registros as $r) {
            Partido::updateOrCreate(['api_id' => $r['api_id']], $r['attrs']);
        }

        // Reconciliacion: elimina partidos que ya no aparecen en la fuente activa.
        // Tambien limpia automaticamente datos de una fuente anterior al cambiar
        // de proveedor (p. ej. al pasar de openfootball a football-data.org).
        Partido::whereNotIn('api_id', $seen)->delete();

        return count($registros);
    }

    /**
     * Fuente principal (si hay token): football-data.org.
     * Resultados casi en tiempo real e incluye estado IN_PLAY.
     *
     * @return array{0: array<int, array{api_id: string, attrs: array<string, mixed>}>, 1: array<int, string>}
     */
    private function fetchFootballData(string $token): array
    {
        $competition = config('services.football_data.competition', 'WC');

        try {
            $response = Http::withHeaders(['X-Auth-Token' => $token])
                ->timeout(20)
                ->get("https://api.football-data.org/v4/competitions/{$competition}/matches");
        } catch (\Throwable $e) {
            Log::warning('football-data.org: error de red: ' . $e->getMessage());
            return [[], []];
        }

        // Rate limit: respetar el throttling del proveedor.
        if ($response->status() === 429) {
            Log::warning('football-data.org: rate limit alcanzado (429). Se reintentara en el proximo ciclo.');
            return [[], []];
        }

        if (!$response->successful()) {
            Log::warning('football-data.org: respuesta no exitosa (' . $response->status() . '). Se omite el ciclo.');
            return [[], []];
        }

        $matches = $response->json('matches') ?? [];
        $registros = [];
        $seen = [];

        foreach ($matches as $m) {
            $local     = $m['homeTeam']['name'] ?? $m['homeTeam']['shortName'] ?? 'TBD';
            $visitante = $m['awayTeam']['name'] ?? $m['awayTeam']['shortName'] ?? 'TBD';

            if (($local === 'TBD' && $visitante === 'TBD') || empty($m['id'])) {
                continue;
            }

            $apiId  = 'fd-' . $m['id'];
            $estado = $this->mapEstadoFootballData($m['status'] ?? 'SCHEDULED');

            $registros[] = [
                'api_id' => $apiId,
                'attrs'  => [
                    'equipo_local'      => $local,
                    'equipo_visitante'  => $visitante,
                    'bandera_local'     => BanderaHelper::get($local),
                    'bandera_visitante' => BanderaHelper::get($visitante),
                    'fecha_partido'     => $this->parseUtc($m['utcDate'] ?? null),
                    'estado'            => $estado,
                    'goles_local'       => $m['score']['fullTime']['home'] ?? null,
                    'goles_visitante'   => $m['score']['fullTime']['away'] ?? null,
                    'grupo'             => $this->formatGrupo($m['group'] ?? null),
                    'fase'              => $this->mapFaseFootballData($m['stage'] ?? 'GROUP_STAGE'),
                ],
            ];
            $seen[] = $apiId;
        }

        return [$registros, $seen];
    }

    /**
     * Fuente de respaldo (sin token): openfootball. Gratuita pero con retraso
     * y sin estado "en vivo".
     *
     * @return array{0: array<int, array{api_id: string, attrs: array<string, mixed>}>, 1: array<int, string>}
     */
    private function fetchOpenfootball(): array
    {
        try {
            $response = Http::timeout(20)->get($this->openfootballUrl);
        } catch (\Throwable $e) {
            Log::warning('openfootball: error de red: ' . $e->getMessage());
            return [[], []];
        }

        if (!$response->successful()) {
            return [[], []];
        }

        $matches = $response->json('matches') ?? [];
        $registros = [];
        $seen = [];

        foreach ($matches as $m) {
            $local     = $m['team1'] ?? 'TBD';
            $visitante = $m['team2'] ?? 'TBD';

            if ($local === 'TBD' && $visitante === 'TBD') {
                continue;
            }

            $golesLocal     = $m['score']['ft'][0] ?? null;
            $golesVisitante = $m['score']['ft'][1] ?? null;
            $finalizado     = $golesLocal !== null && $golesVisitante !== null;

            $apiId = 'of-' . md5(($m['round'] ?? '') . '|' . $local . '|' . $visitante . '|' . ($m['date'] ?? ''));

            $registros[] = [
                'api_id' => $apiId,
                'attrs'  => [
                    'equipo_local'      => $local,
                    'equipo_visitante'  => $visitante,
                    'bandera_local'     => BanderaHelper::get($local),
                    'bandera_visitante' => BanderaHelper::get($visitante),
                    'fecha_partido'     => $this->parseOpenfootballFecha($m['date'] ?? null, $m['time'] ?? null),
                    'estado'            => $finalizado ? 'finished' : 'scheduled',
                    'goles_local'       => $golesLocal,
                    'goles_visitante'   => $golesVisitante,
                    'grupo'             => $m['group'] ?? null,
                    'fase'              => $this->mapFaseOpenfootball($m['round'] ?? 'GROUP_STAGE'),
                ],
            ];
            $seen[] = $apiId;
        }

        return [$registros, $seen];
    }

    private function mapEstadoFootballData(string $status): string
    {
        return match (strtoupper($status)) {
            'IN_PLAY', 'PAUSED'   => 'live',
            'FINISHED', 'AWARDED' => 'finished',
            default               => 'scheduled',
        };
    }

    private function mapFaseFootballData(string $stage): string
    {
        return match (strtoupper($stage)) {
            'LAST_32'                      => 'ROUND_OF_32',
            'LAST_16'                      => 'ROUND_OF_16',
            'QUARTER_FINALS', 'QUARTER_FINAL' => 'QUARTER_FINAL',
            'SEMI_FINALS', 'SEMI_FINAL'    => 'SEMI_FINAL',
            'THIRD_PLACE'                  => 'THIRD_PLACE',
            'FINAL'                        => 'FINAL',
            default                        => 'GROUP_STAGE',
        };
    }

    private function mapFaseOpenfootball(string $round): string
    {
        $r = strtolower($round);

        return match (true) {
            str_contains($r, 'round of 32')                      => 'ROUND_OF_32',
            str_contains($r, 'round of 16')                      => 'ROUND_OF_16',
            str_contains($r, 'quarter')                          => 'QUARTER_FINAL',
            str_contains($r, 'semi')                             => 'SEMI_FINAL',
            str_contains($r, 'third') || str_contains($r, '3rd') => 'THIRD_PLACE',
            str_contains($r, 'final')                            => 'FINAL',
            default                                              => 'GROUP_STAGE',
        };
    }

    private function formatGrupo(?string $grupo): ?string
    {
        if (empty($grupo)) {
            return null;
        }

        // "GROUP_J" -> "Group J"
        return ucwords(strtolower(str_replace('_', ' ', $grupo)));
    }

    private function parseUtc(?string $valor): Carbon
    {
        if (empty($valor)) {
            return now();
        }

        try {
            return Carbon::parse($valor)->setTimezone('UTC');
        } catch (\Throwable $e) {
            return now();
        }
    }

    private function parseOpenfootballFecha(?string $fecha, ?string $hora): Carbon
    {
        if (empty($fecha)) {
            return now();
        }

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
