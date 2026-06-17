<?php

namespace App\Services;

use App\Models\EstadisticaHistorica;
use App\Models\Partido;
use App\Models\Prediccion;

class PrediccionService
{
    private array $factorFase = [
        'GROUP_STAGE'   => 1.00,
        'ROUND_OF_32'   => 0.95,
        'ROUND_OF_16'   => 0.90,
        'QUARTER_FINAL' => 0.85,
        'SEMI_FINAL'    => 0.80,
        'FINAL'         => 0.80,
    ];

    public function generarPrediccion(Partido $partido): Prediccion
    {
        $statsLocal     = $this->getStats($partido->equipo_local);
        $statsVisitante = $this->getStats($partido->equipo_visitante);
        $factorFase     = $this->factorFase[$partido->fase] ?? 1.0;

        $defensaVisitante = max(0.1, 1 - ($statsVisitante['promedio_gc'] / 2));
        $defensaLocal     = max(0.1, 1 - ($statsLocal['promedio_gc'] / 2));

        $golesLocal     = max(0, (int) round($statsLocal['promedio_gf'] * $defensaVisitante * $factorFase));
        $golesVisitante = max(0, (int) round($statsVisitante['promedio_gf'] * $defensaLocal * $factorFase));

        $ganador = match (true) {
            $golesLocal > $golesVisitante => 'local',
            $golesVisitante > $golesLocal => 'visitante',
            default                       => 'empate',
        };

        $diferencia = abs($statsLocal['indice'] - $statsVisitante['indice']);
        $confianza  = min(88, 50 + ($diferencia * 1.5));

        $razonamiento = $this->generarRazonamiento(
            $partido, $statsLocal, $statsVisitante,
            $golesLocal, $golesVisitante, $confianza
        );

        return Prediccion::updateOrCreate(
            ['partido_id' => $partido->id],
            [
                'pred_goles_local'     => $golesLocal,
                'pred_goles_visitante' => $golesVisitante,
                'pred_ganador'         => $ganador,
                'confianza'            => round($confianza, 1),
                'razonamiento'         => $razonamiento,
                'resultado'            => 'pendiente',
                'puntos_obtenidos'     => 0,
            ]
        );
    }

    private function getStats(string $equipo): array
    {
        $historico     = EstadisticaHistorica::where('equipo', $equipo)->get();
        $totalPartidos = $historico->sum('partidos');
        $totalGF       = $historico->sum('goles_favor');
        $totalGC       = $historico->sum('goles_contra');
        $indiceMax     = $historico->max('indice_fuerza') ?? 20;

        $partidos2026 = Partido::where(function ($q) use ($equipo) {
            $q->where('equipo_local', $equipo)->orWhere('equipo_visitante', $equipo);
        })->where('estado', 'finished')->get();

        $gf2026 = 0;
        $gc2026 = 0;
        foreach ($partidos2026 as $p) {
            if ($p->equipo_local === $equipo) {
                $gf2026 += $p->goles_local ?? 0;
                $gc2026 += $p->goles_visitante ?? 0;
            } else {
                $gf2026 += $p->goles_visitante ?? 0;
                $gc2026 += $p->goles_local ?? 0;
            }
        }

        $promedioGF_h = $totalPartidos > 0 ? $totalGF / $totalPartidos : 1.0;
        $promedioGC_h = $totalPartidos > 0 ? $totalGC / $totalPartidos : 1.0;

        if ($partidos2026->count() > 0) {
            $promedioGF = ($promedioGF_h * 0.6) + (($gf2026 / $partidos2026->count()) * 0.4);
            $promedioGC = ($promedioGC_h * 0.6) + (($gc2026 / $partidos2026->count()) * 0.4);
        } else {
            $promedioGF = $promedioGF_h;
            $promedioGC = $promedioGC_h;
        }

        return [
            'promedio_gf'   => round($promedioGF, 2),
            'promedio_gc'   => round($promedioGC, 2),
            'indice'        => $indiceMax,
            'partidos_2026' => $partidos2026->count(),
            'gf_2026'       => $gf2026,
        ];
    }

    private function generarRazonamiento(Partido $partido, array $sL, array $sV, int $gL, int $gV, float $conf): string
    {
        $local     = $partido->equipo_local;
        $visitante = $partido->equipo_visitante;
        $textoL    = "{$local} promedia {$sL['promedio_gf']} goles a favor y {$sL['promedio_gc']} en contra";
        $textoV    = "{$visitante} promedia {$sV['promedio_gf']} goles a favor y {$sV['promedio_gc']} en contra";

        if ($sL['partidos_2026'] > 0) {
            $textoL .= " ({$sL['gf_2026']} goles en el Mundial 2026)";
        }

        $res = match (true) {
            $gL > $gV => "El análisis favorece a {$local}",
            $gV > $gL => "El análisis favorece a {$visitante}",
            default   => "El análisis sugiere un partido equilibrado",
        };

        return "{$textoL}. {$textoV}. {$res}. Predicción: {$local} {$gL} - {$gV} {$visitante} con {$conf}% de confianza. Basado en datos históricos de Qatar 2022 y Rusia 2018.";
    }

    public function validarPrediccion(Prediccion $prediccion): void
    {
        $partido = $prediccion->partido;

        if (!$partido || $partido->estado !== 'finished') {
            return;
        }

        $aciertoMarcador = $prediccion->pred_goles_local === $partido->goles_local
            && $prediccion->pred_goles_visitante === $partido->goles_visitante;

        $ganadorReal = match (true) {
            $partido->goles_local > $partido->goles_visitante => 'local',
            $partido->goles_visitante > $partido->goles_local => 'visitante',
            default                                            => 'empate',
        };

        $resultado = match (true) {
            $aciertoMarcador                           => 'exacto',
            $prediccion->pred_ganador === $ganadorReal => 'correcto',
            default                                    => 'incorrecto',
        };

        $prediccion->update([
            'resultado'        => $resultado,
            'puntos_obtenidos' => match ($resultado) {
                'exacto'   => 5,
                'correcto' => 3,
                default    => 0,
            },
        ]);
    }
}
