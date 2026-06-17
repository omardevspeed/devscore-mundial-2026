<?php

namespace App\Services;

use App\Models\EstadisticaHistorica;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HistoricoService
{
    private array $urls = [
        2022 => 'https://raw.githubusercontent.com/openfootball/worldcup.json/master/2022/worldcup.json',
        2018 => 'https://raw.githubusercontent.com/openfootball/worldcup.json/master/2018/worldcup.json',
    ];

    private array $faseBonus = [
        'GROUPS' => 0, 'R32' => 2, 'R16' => 4,
        'QF' => 7, 'SF' => 10, 'FINAL' => 13, 'WINNER' => 18,
    ];

    public function cargarHistorico(): void
    {
        foreach ($this->urls as $anio => $url) {
            try {
                $response = Http::timeout(15)->get($url);
            } catch (\Throwable $e) {
                Log::warning("Error al cargar histórico {$anio}: " . $e->getMessage());
                continue;
            }

            if (!$response->successful()) {
                continue;
            }

            $data  = $response->json();
            $stats = $this->procesarPartidos($data['matches'] ?? []);

            foreach ($stats as $equipo => $stat) {
                $indice = ($stat['gf'] * 2)
                    - ($stat['gc'] * 1.5)
                    + ($stat['v'] * 3)
                    + $stat['e']
                    + ($this->faseBonus[$stat['fase']] ?? 0);

                EstadisticaHistorica::updateOrCreate(
                    ['equipo' => $equipo, 'torneo' => $anio],
                    [
                        'partidos'       => $stat['p'],
                        'victorias'      => $stat['v'],
                        'empates'        => $stat['e'],
                        'derrotas'       => $stat['d'],
                        'goles_favor'    => $stat['gf'],
                        'goles_contra'   => $stat['gc'],
                        'fase_alcanzada' => $stat['fase'],
                        'indice_fuerza'  => round($indice, 2),
                    ]
                );
            }
        }
    }

    private function procesarPartidos(array $matches): array
    {
        $stats = [];

        foreach ($matches as $match) {
            $local          = $match['team1'] ?? null;
            $visitante      = $match['team2'] ?? null;
            $scoreLocal     = $match['score']['ft'][0] ?? null;
            $scoreVisitante = $match['score']['ft'][1] ?? null;

            if (!$local || !$visitante || $scoreLocal === null || $scoreVisitante === null) {
                continue;
            }

            foreach ([$local, $visitante] as $eq) {
                if (!isset($stats[$eq])) {
                    $stats[$eq] = ['p' => 0, 'v' => 0, 'e' => 0, 'd' => 0, 'gf' => 0, 'gc' => 0, 'fase' => 'GROUPS'];
                }
            }

            $stats[$local]['p']++;
            $stats[$local]['gf'] += $scoreLocal;
            $stats[$local]['gc'] += $scoreVisitante;
            if ($scoreLocal > $scoreVisitante)       $stats[$local]['v']++;
            elseif ($scoreLocal === $scoreVisitante) $stats[$local]['e']++;
            else                                     $stats[$local]['d']++;

            $stats[$visitante]['p']++;
            $stats[$visitante]['gf'] += $scoreVisitante;
            $stats[$visitante]['gc'] += $scoreLocal;
            if ($scoreVisitante > $scoreLocal)       $stats[$visitante]['v']++;
            elseif ($scoreVisitante === $scoreLocal) $stats[$visitante]['e']++;
            else                                     $stats[$visitante]['d']++;

            $ronda = strtoupper($match['round'] ?? '');
            $this->actualizarFase($stats, $local, $visitante, $ronda, (int) $scoreLocal, (int) $scoreVisitante);
        }

        return $stats;
    }

    private function actualizarFase(array &$stats, string $local, string $visitante, string $ronda, int $gl, int $gv): void
    {
        $faseMap = [
            'FINAL' => 'FINAL', 'THIRD' => 'SF', 'SEMI' => 'SF',
            'QUARTER' => 'QF', 'ROUND OF 16' => 'R16', 'ROUND OF 32' => 'R32',
        ];

        foreach ($faseMap as $key => $fase) {
            if (str_contains($ronda, $key)) {
                if ($gl > $gv) {
                    $stats[$local]['fase']     = $fase === 'FINAL' ? 'WINNER' : $fase;
                    $stats[$visitante]['fase'] = $fase;
                } elseif ($gv > $gl) {
                    $stats[$visitante]['fase'] = $fase === 'FINAL' ? 'WINNER' : $fase;
                    $stats[$local]['fase']     = $fase;
                }
                break;
            }
        }
    }
}
