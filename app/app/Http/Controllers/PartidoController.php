<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Prediccion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartidoController extends Controller
{
    private array $estadosLive = ['live', 'in_play', 'inprogress', 'playing'];

    public function index(): View
    {
        return view('partidos.index', $this->datos());
    }

    public function refresh(Request $request): JsonResponse
    {
        $datos = $this->datos();

        return response()->json([
            'stats'              => $datos['stats'],
            'hay_en_vivo'        => $datos['enVivo']->isNotEmpty(),
            'cantidad_en_vivo'   => $datos['enVivo']->count(),
            'actualizado'        => now()->setTimezone('America/Santiago')->format('H:i:s'),
        ]);
    }

    private function datos(): array
    {
        $enVivo = Partido::with('prediccion')
            ->whereIn('estado', $this->estadosLive)
            ->orderBy('fecha_partido')
            ->get();

        $proximos = Partido::with('prediccion')
            ->whereNotIn('estado', array_merge($this->estadosLive, ['finished']))
            ->orderBy('fecha_partido')
            ->get()
            ->groupBy(fn ($p) => $p->fecha_partido?->setTimezone('America/Santiago')->format('Y-m-d'));

        $terminados = Partido::with('prediccion')
            ->where('estado', 'finished')
            ->orderByDesc('fecha_partido')
            ->get();

        return [
            'stats'      => $this->statsGlobales(),
            'enVivo'     => $enVivo,
            'proximos'   => $proximos,
            'terminados' => $terminados,
            'grupos'     => $this->tablaPorGrupos(),
        ];
    }

    private function statsGlobales(): array
    {
        $jugados = Partido::where('estado', 'finished')->count();

        $validadas = Prediccion::where('resultado', '!=', 'pendiente')->count();
        $exactas   = Prediccion::where('resultado', 'exacto')->count();
        $correctas = Prediccion::where('resultado', 'correcto')->count();

        $aciertos = $exactas + $correctas;
        $porcentaje = $validadas > 0 ? round(($aciertos / $validadas) * 100, 1) : 0.0;

        return [
            'jugados'     => $jugados,
            'total_fixt'  => 104,
            'exactas'     => $exactas,
            'correctas'   => $correctas,
            'validadas'   => $validadas,
            'porcentaje'  => $porcentaje,
            'puntos'      => Prediccion::sum('puntos_obtenidos'),
        ];
    }

    private function tablaPorGrupos(): array
    {
        $partidos = Partido::where('estado', 'finished')
            ->whereNotNull('grupo')
            ->get();

        $tabla = [];

        foreach ($partidos as $p) {
            $grupo = $p->grupo;
            foreach ([
                [$p->equipo_local, $p->goles_local, $p->goles_visitante],
                [$p->equipo_visitante, $p->goles_visitante, $p->goles_local],
            ] as [$equipo, $gf, $gc]) {
                $tabla[$grupo][$equipo] ??= [
                    'equipo' => $equipo, 'pj' => 0, 'g' => 0, 'e' => 0,
                    'p' => 0, 'gf' => 0, 'gc' => 0, 'pts' => 0,
                ];

                $row = &$tabla[$grupo][$equipo];
                $row['pj']++;
                $row['gf'] += $gf;
                $row['gc'] += $gc;

                if ($gf > $gc) {
                    $row['g']++;
                    $row['pts'] += 3;
                } elseif ($gf === $gc) {
                    $row['e']++;
                    $row['pts'] += 1;
                } else {
                    $row['p']++;
                }
                unset($row);
            }
        }

        foreach ($tabla as $grupo => &$equipos) {
            foreach ($equipos as &$row) {
                $row['dg'] = $row['gf'] - $row['gc'];
            }
            unset($row);
            usort($equipos, fn ($a, $b) => [$b['pts'], $b['dg'], $b['gf']] <=> [$a['pts'], $a['dg'], $a['gf']]);
        }
        unset($equipos);

        ksort($tabla);

        return $tabla;
    }
}
