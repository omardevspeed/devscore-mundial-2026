PROMPT MAESTRO PARA CURSOR — DevScore Mundial 2026
VERSION 4 — FINAL CON FIXES VISUALES INCLUIDOS
100% GRATUITO — Sin APIs de pago

***
ROL

Eres un ingeniero senior de Laravel con experiencia en Docker, APIs externas y algoritmos de predicción deportiva. Estás construyendo DevScore Mundial 2026 — una plataforma pública que muestra partidos del Mundial en tiempo real con predicciones automáticas de marcador exacto generadas por un algoritmo propio basado en datos históricos reales.

Costo total del proyecto: $0

***
IDENTIDAD

Nombre:     DevScore Mundial 2026
Subdominio: mundial.omardevspeed.com
Repo:       github.com/omardevspeed/devscore-mundial-2026
Creador:    Omar Curvelo — @OmarDevSpeed 🇻🇪🇨🇱

***
APIS — 100% GRATUITAS SIN KEY

API 1 — Partidos en vivo:
  URL: https://raw.githubusercontent.com/rezarahiminia/worldcup2026/main/data/matches.json
  USO: Fixtures, resultados en tiempo real, grupos, fases
  KEY: No requiere

API 2 — Datos históricos para predicciones:
  URL: https://raw.githubusercontent.com/openfootball/worldcup.json/master/2022/worldcup.json
  URL: https://raw.githubusercontent.com/openfootball/worldcup.json/master/2018/worldcup.json
  USO: Estadísticas históricas Qatar 2022 + Rusia 2018
  KEY: No requiere

***
STACK

Backend:    Laravel 11 (PHP 8.2+)
Frontend:   Blade + Tailwind CSS + Alpine.js
Database:   MySQL 8
Deploy:     Docker + docker-compose + Nginx
Scheduler:  Laravel Task Scheduler — cada 60 segundos

***
IDENTIDAD VISUAL — OBLIGATORIO EN TODA LA APP

Negro base:         #0A0A0A  ← fondo de toda la app
Azul eléctrico:     #00D4FF  ← títulos, scores, badges activos
Naranja speed:      #FF6B35  ← acentos, partidos en vivo, CTAs
Blanco texto:       #FFFFFF  ← TODO el texto de contenido
Gris subtítulos:    #CCCCCC  ← textos secundarios
Gris cards:         #111111  ← fondo de cards
Gris borde:         #1F1F1F  ← bordes de cards
Verde acierto:      #22c55e  ← badge EXACTO y CORRECTO
Rojo error:         #E24B4A  ← badge INCORRECTO

Tipografía:         Space Grotesk (títulos) + Inter (cuerpo) — Google Fonts
Modo:               Dark mode SIEMPRE — nunca light mode
Diseño:             100% responsivo — mobile first obligatorio

REGLAS DE COLOR ABSOLUTAS — NO NEGOCIABLES:

✅ TODOS los nombres de equipos: text-white font-bold
✅ TODOS los scores: text-[#00D4FF] font-bold text-2xl
✅ TODOS los grupos y fases: text-[#FF6B35] text-sm font-medium
✅ TODOS los textos de predicción: text-white
✅ TODOS los razonamientos: text-gray-300 text-sm
✅ FONDO de toda la página: bg-[#0A0A0A]
✅ FONDO de cards: bg-[#111111] border border-[#1F1F1F]
✅ NUNCA usar text-black, text-gray-900, text-gray-800
✅ NUNCA fondo blanco en ningún elemento

***
BANDERAS — SISTEMA DE EMOJIS (fallback obligatorio)

La API puede no devolver URLs de banderas. Crear un helper con emojis para TODOS los equipos:

// app/Helpers/BanderaHelper.php
class BanderaHelper
{
    public static array $banderas = [
        // Grupo A
        'Mexico'              => '🇲🇽',
        'South Africa'        => '🇿🇦',
        'Ecuador'             => '🇪🇨',
        'New Zealand'         => '🇳🇿',
        // Grupo B
        'Argentina'           => '🇦🇷',
        'Chile'               => '🇨🇱',
        'Peru'                => '🇵🇪',
        'Australia'           => '🇦🇺',
        // Grupo C
        'Netherlands'         => '🇳🇱',
        'Senegal'             => '🇸🇳',
        'Japan'               => '🇯🇵',
        'Canada'              => '🇨🇦',
        // Grupo D
        'Spain'               => '🇪🇸',
        'Morocco'             => '🇲🇦',
        'Croatia'             => '🇭🇷',
        'Cameroon'            => '🇨🇲',
        // Grupo E
        'Portugal'            => '🇵🇹',
        'Uruguay'             => '🇺🇾',
        'Saudi Arabia'        => '🇸🇦',
        'IR Iran'             => '🇮🇷',
        // Grupo F
        'Brazil'              => '🇧🇷',
        'Switzerland'         => '🇨🇭',
        'Ivory Coast'         => '🇨🇮',
        'Serbia'              => '🇷🇸',
        // Grupo G
        'France'              => '🇫🇷',
        'USA'                 => '🇺🇸',
        'England'             => '🏴󠁧󠁢󠁥󠁮󠁧󠁿',
        'Paraguay'            => '🇵🇾',
        // Grupo H
        'Germany'             => '🇩🇪',
        'Colombia'            => '🇨🇴',
        'Ghana'               => '🇬🇭',
        'Algeria'             => '🇩🇿',
        // Grupo I
        'Belgium'             => '🇧🇪',
        'Korea Republic'      => '🇰🇷',
        'Venezuela'           => '🇻🇪',
        'Egypt'               => '🇪🇬',
        // Grupo J
        'Netherlands'         => '🇳🇱',
        'Nigeria'             => '🇳🇬',
        'Türkiye'             => '🇹🇷',
        'Poland'              => '🇵🇱',
        // Grupo K
        'Italy'               => '🇮🇹',
        'Qatar'               => '🇶🇦',
        'Costa Rica'          => '🇨🇷',
        'Panama'              => '🇵🇦',
        // Grupo L
        'Denmark'             => '🇩🇰',
        'Slovenia'            => '🇸🇮',
        'Iraq'                => '🇮🇶',
        'Cuba'                => '🇨🇺',
    ];

    public static function get(string $equipo): string
    {
        return self::$banderas[$equipo]
            ?? self::$banderas[trim($equipo)]
            ?? '🏳️';
    }
}

En todas las vistas usar así:
<span class="text-3xl md:text-4xl">
    {{ BanderaHelper::get($partido->equipo_local) }}
</span>
<span class="text-white font-bold text-lg md:text-xl">
    {{ $partido->equipo_local }}
</span>

***
BASE DE DATOS

// tabla: partidos
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

// tabla: estadisticas_historicas
Schema::create('estadisticas_historicas', function (Blueprint $table) {
    $table->id();
    $table->string('equipo');
    $table->integer('torneo');
    $table->integer('partidos');
    $table->integer('victorias');
    $table->integer('empates');
    $table->integer('derrotas');
    $table->integer('goles_favor');
    $table->integer('goles_contra');
    $table->string('fase_alcanzada');
    $table->decimal('indice_fuerza', 8, 2)->default(0);
    $table->timestamps();
    $table->unique(['equipo', 'torneo']);
});

// tabla: predicciones
Schema::create('predicciones', function (Blueprint $table) {
    $table->id();
    $table->foreignId('partido_id')->constrained()->onDelete('cascade');
    $table->integer('pred_goles_local');
    $table->integer('pred_goles_visitante');
    $table->string('pred_ganador');
    $table->decimal('confianza', 5, 2);
    $table->text('razonamiento');
    $table->enum('resultado', ['pendiente','exacto','correcto','incorrecto'])
          ->default('pendiente');
    $table->integer('puntos_obtenidos')->default(0);
    $table->timestamps();
    $table->unique('partido_id');
});

***
ALGORITMO DE PREDICCIÓN

Fuentes de datos:
60% peso: Histórico Qatar 2022 + Rusia 2018
40% peso: Resultados reales del Mundial 2026

Índice de fuerza:
indice_fuerza =
    (goles_favor × 2)
    - (goles_contra × 1.5)
    + (victorias × 3)
    + (empates × 1)
    + bonus_fase_alcanzada

// bonus_fase_alcanzada:
GROUPS → 0 | R32 → 2 | R16 → 4
QF → 7 | SF → 10 | FINAL → 13 | WINNER → 18

Goles predichos:
promedio_ataque_local     = goles_favor_local / partidos_local
promedio_ataque_visitante = goles_favor_visitante / partidos_visitante
factor_defensa_visitante  = max(0.1, 1 - (goles_contra_visitante / (partidos_visitante × 2)))
factor_defensa_local      = max(0.1, 1 - (goles_contra_local / (partidos_local × 2)))

goles_pred_local     = max(0, round(promedio_ataque_local × factor_defensa_visitante × factor_fase))
goles_pred_visitante = max(0, round(promedio_ataque_visitante × factor_defensa_local × factor_fase))

// factor_fase:
GROUP_STAGE → 1.0 | ROUND_OF_32 → 0.95 | ROUND_OF_16 → 0.90
QUARTER_FINAL → 0.85 | SEMI_FINAL → 0.80 | FINAL → 0.80

// Ajuste dinámico con 2026:
promedio_final = (promedio_historico × 0.6) + (promedio_2026 × 0.4)

Confianza:
diferencia = abs(indice_local - indice_visitante)
confianza  = min(88, 50 + (diferencia × 1.5))
// Mín 50% — Máx 88%

***
SERVICES

WorldCupApiService
class WorldCupApiService
{
    public function syncPartidos(): int
    {
        $response = Http::timeout(15)
            ->get('https://raw.githubusercontent.com/rezarahiminia/worldcup2026/main/data/matches.json');

        if (!$response->successful()) return 0;

        $matches = $response->json();
        $synced  = 0;

        foreach ($matches as $match) {
            Partido::updateOrCreate(
                ['api_id' => (string)($match['id'] ?? $match['matchId'] ?? uniqid())],
                [
                    'equipo_local'      => $match['homeTeam']['name'] ?? $match['home'] ?? 'TBD',
                    'equipo_visitante'  => $match['awayTeam']['name'] ?? $match['away'] ?? 'TBD',
                    'bandera_local'     => $match['homeTeam']['flag'] ?? null,
                    'bandera_visitante' => $match['awayTeam']['flag'] ?? null,
                    'fecha_partido'     => $match['date'] ?? $match['utcDate'] ?? now(),
                    'estado'            => strtolower($match['status'] ?? 'scheduled'),
                    'goles_local'       => $match['score']['home'] ?? $match['homeScore'] ?? null,
                    'goles_visitante'   => $match['score']['away'] ?? $match['awayScore'] ?? null,
                    'grupo'             => $match['group'] ?? null,
                    'fase'              => $match['stage'] ?? $match['round'] ?? 'GROUP_STAGE',
                ]
            );
            $synced++;
        }
        return $synced;
    }
}

HistoricoService
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
            $response = Http::timeout(15)->get($url);
            if (!$response->successful()) continue;

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

            if (!$local || !$visitante || $scoreLocal === null) continue;

            foreach ([$local, $visitante] as $eq) {
                if (!isset($stats[$eq])) {
                    $stats[$eq] = ['p'=>0,'v'=>0,'e'=>0,'d'=>0,'gf'=>0,'gc'=>0,'fase'=>'GROUPS'];
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
            $this->actualizarFase($stats, $local, $visitante, $ronda, $scoreLocal, $scoreVisitante);
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
                    $stats[$local]['fase']    = $fase === 'FINAL' ? 'WINNER' : $fase;
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

PrediccionService
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

        $ganador = match(true) {
            $golesLocal > $golesVisitante => 'local',
            $golesVisitante > $golesLocal => 'visitante',
            default                        => 'empate',
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
        if ($sL['partidos_2026'] > 0) $textoL .= " ({$sL['gf_2026']} goles en el Mundial 2026)";
        $res = match(true) {
            $gL > $gV  => "El análisis favorece a {$local}",
            $gV > $gL  => "El análisis favorece a {$visitante}",
            default     => "El análisis sugiere un partido equilibrado",
        };
        return "{$textoL}. {$textoV}. {$res}. Predicción: {$local} {$gL} - {$gV} {$visitante} con {$conf}% de confianza. Basado en datos históricos de Qatar 2022 y Rusia 2018.";
    }

    public function validarPrediccion(Prediccion $prediccion): void
    {
        $partido = $prediccion->partido;
        if ($partido->estado !== 'finished') return;

        $aciertoMarcador = $prediccion->pred_goles_local === $partido->goles_local
            && $prediccion->pred_goles_visitante === $partido->goles_visitante;

        $ganadorReal = match(true) {
            $partido->goles_local > $partido->goles_visitante  => 'local',
            $partido->goles_visitante > $partido->goles_local  => 'visitante',
            default                                             => 'empate',
        };

        $resultado = match(true) {
            $aciertoMarcador                         => 'exacto',
            $prediccion->pred_ganador === $ganadorReal => 'correcto',
            default                                   => 'incorrecto',
        };

        $prediccion->update([
            'resultado'        => $resultado,
            'puntos_obtenidos' => match($resultado) { 'exacto' => 5, 'correcto' => 3, default => 0 },
        ]);
    }
}

***
COMMANDS

// SyncPartidos — corre cada minuto
class SyncPartidos extends Command
{
    protected $signature   = 'mundial:sync';
    protected $description = 'Sincroniza partidos y genera/valida predicciones';

    public function handle(WorldCupApiService $api, PrediccionService $prediccion): void
    {
        $synced = $api->syncPartidos();
        $this->info("Sincronizados: {$synced}");

        Partido::whereDoesntHave('prediccion')
            ->where('estado', '!=', 'finished')
            ->get()->each(fn($p) => $prediccion->generarPrediccion($p));

        Prediccion::where('resultado', 'pendiente')
            ->whereHas('partido', fn($q) => $q->where('estado', 'finished'))
            ->get()->each(fn($pred) => $prediccion->validarPrediccion($pred));

        $this->info('Sync: ' . now()->format('H:i:s'));
    }
}

// CargarHistorico — solo una vez al instalar
class CargarHistorico extends Command
{
    protected $signature   = 'mundial:historico';
    protected $description = 'Carga datos históricos de Qatar 2022 y Rusia 2018';

    public function handle(HistoricoService $historico): void
    {
        $historico->cargarHistorico();
        $this->info('Datos históricos cargados.');
    }
}

// Kernel.php
$schedule->command('mundial:sync')->everyMinute();

***
LANDING PAGE — SECCIONES Y DISEÑO EXACTO

REGLAS VISUALES DE LAS VISTAS:

TODOS los nombres de equipos:
  <span class="text-white font-bold text-lg md:text-xl">{{ $partido->equipo_local }}</span>

TODAS las banderas (emoji como principal, URL como imagen si existe):
  <span class="text-3xl md:text-4xl">{{ BanderaHelper::get($partido->equipo_local) }}</span>

TODOS los scores:
  <span class="text-[#00D4FF] font-bold text-2xl md:text-3xl">{{ $partido->goles_local }}</span>

TODOS los grupos:
  <span class="text-[#FF6B35] text-sm font-medium uppercase">{{ $partido->grupo }}</span>

TODOS los razonamientos:
  <p class="text-gray-300 text-sm leading-relaxed">{{ $prediccion->razonamiento }}</p>

BADGE EXACTO:
  <span class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded">✅ EXACTO · 5 pts</span>

BADGE CORRECTO:
  <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded">✅ CORRECTO · 3 pts</span>

BADGE INCORRECTO:
  <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">❌ INCORRECTO · 0 pts</span>

BADGE EN VIVO (parpadeante):
  <span class="animate-pulse bg-[#FF6B35] text-white text-xs font-bold px-2 py-1 rounded">● EN VIVO</span>

SECCIONES OBLIGATORIAS:

1. HEADER
   - Logo OmarDevSpeed + "DevScore Mundial 2026"
   - "Actualizado hace X segundos" en gris
   - Badge naranja parpadeante si hay partidos en vivo
   - bg-[#0A0A0A] border-b border-[#1F1F1F]

2. STATS GLOBALES — 4 cards en grid
   - Partidos jugados / 104
   - Predicciones EXACTAS / total partidos jugados
   - Predicciones CORRECTAS / total partidos jugados
   - % de aciertos total del algoritmo (color verde si >60%, rojo si <40%)

3. EN VIVO AHORA — solo si hay partidos live
   - Borde naranja animado
   - Score en azul grande
   - Emoji bandera grande (text-4xl) + nombre en blanco
   - Predicción del algoritmo visible

4. PRÓXIMOS PARTIDOS — TODOS los partidos pendientes
   - Mostrar TODOS (no solo los del día)
   - Agrupados por fecha
   - Ordenados fecha_partido ASC
   - Hora en formato Chile (America/Santiago)
   - Predicción visible antes del partido

5. PARTIDOS TERMINADOS
   - Resultado real en azul grande
   - Predicción del algoritmo debajo
   - Badge de resultado (exacto/correcto/incorrecto)
   - Nombres de equipos en BLANCO

6. TABLA POR GRUPOS
   - overflow-x-auto en mobile
   - Encabezado en azul

7. FOOTER
   - @OmarDevSpeed · YouTube · TikTok · Instagram · GitHub
   - "Construido en vivo en YouTube — Repo público en GitHub"
   - "Predicciones basadas en Qatar 2022 + Rusia 2018"

AUTO-REFRESH:
   - Alpine.js fetch() cada 60 segundos
   - Sin recargar la página completa
   - Silencioso — no interrumpe el scroll
   - Indicador "Actualizado hace X seg" se actualiza solo

***
RESPONSIVO — OBLIGATORIO

Mobile  < 640px:   1 columna, texto sm, banderas text-2xl
Tablet  640-1024:  2 columnas, texto base
Desktop > 1024px:  3 columnas, texto lg, banderas text-4xl

Clases clave:
grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4
text-sm md:text-base lg:text-lg
p-3 md:p-5
overflow-x-auto
flex items-center gap-2 md:gap-3

Testing: 375px / 768px / 1280px

***
ORDEN DE CONSTRUCCIÓN

docker-compose.yml + Dockerfile + nginx/default.conf
Laravel 11 install en /app
BanderaHelper con todos los emojis
Migraciones: partidos + estadisticas_historicas + predicciones
Models con relaciones (Partido hasOne Prediccion, etc)
Services: WorldCupApiService + HistoricoService + PrediccionService
Commands: SyncPartidos + CargarHistorico + Kernel
Controller: PartidoController con stats globales
layouts/app.blade.php con header y footer
partidos/index.blade.php — landing completa con todas las secciones
Auto-refresh Alpine.js
Verificar colores en TODAS las secciones antes de terminar
Verificar responsivo en 375px, 768px y 1280px
