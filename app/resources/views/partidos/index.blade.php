@extends('layouts.app')

@section('content')

    {{-- 2. STATS GLOBALES --}}
    <section id="estadisticas" class="scroll-mt-24">
        <h2 class="font-display font-bold text-white text-xl md:text-2xl mb-4">
            📊 Estadísticas del <span class="text-azul">algoritmo</span>
        </h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div class="bg-card border border-borde rounded-xl p-3 md:p-5">
                <p class="text-gray-400 text-xs md:text-sm">Partidos jugados</p>
                <p class="text-white font-display font-bold text-2xl md:text-3xl mt-1">
                    {{ $stats['jugados'] }}<span class="text-gray-500 text-lg">/{{ $stats['total_fixt'] }}</span>
                </p>
            </div>
            <div class="bg-card border border-borde rounded-xl p-3 md:p-5">
                <p class="text-gray-400 text-xs md:text-sm">Marcadores EXACTOS</p>
                <p class="text-azul font-display font-bold text-2xl md:text-3xl mt-1">
                    {{ $stats['exactas'] }}<span class="text-gray-500 text-lg">/{{ $stats['validadas'] }}</span>
                </p>
            </div>
            <div class="bg-card border border-borde rounded-xl p-3 md:p-5">
                <p class="text-gray-400 text-xs md:text-sm">Ganadores CORRECTOS</p>
                <p class="text-azul font-display font-bold text-2xl md:text-3xl mt-1">
                    {{ $stats['correctas'] }}<span class="text-gray-500 text-lg">/{{ $stats['validadas'] }}</span>
                </p>
            </div>
            <div class="bg-card border border-borde rounded-xl p-3 md:p-5">
                <p class="text-gray-400 text-xs md:text-sm">% de aciertos</p>
                <p class="font-display font-bold text-2xl md:text-3xl mt-1 {{ $stats['porcentaje'] > 60 ? 'text-acierto' : ($stats['porcentaje'] < 40 ? 'text-fallo' : 'text-white') }}">
                    {{ rtrim(rtrim(number_format($stats['porcentaje'], 1), '0'), '.') }}%
                </p>
            </div>
        </div>
    </section>

    {{-- 3. EN VIVO AHORA --}}
    @if($enVivo->isNotEmpty())
        <section id="en-vivo" class="scroll-mt-24">
            <h2 class="font-display font-bold text-white text-xl md:text-2xl mb-4 flex items-center gap-2">
                <span class="animate-pulse bg-speed text-white text-xs font-bold px-2 py-1 rounded uppercase">● En vivo</span>
                Ahora mismo
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach($enVivo as $partido)
                    <article class="bg-card border border-speed rounded-xl p-4 md:p-5 live-border">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[#FF6B35] text-sm font-medium uppercase">{{ $partido->grupo ?? $partido->fase }}</span>
                            <span class="animate-pulse bg-speed text-white text-xs font-bold px-2 py-1 rounded">● EN VIVO</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 md:gap-3 flex-1 min-w-0">
                                <span class="text-3xl md:text-4xl">{{ \App\Helpers\BanderaHelper::get($partido->equipo_local) }}</span>
                                <span class="text-white font-bold text-base md:text-xl truncate">{{ $partido->equipo_local }}</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-azul font-bold text-2xl md:text-3xl tabular-nums">{{ $partido->goles_local ?? 0 }}</span>
                                <span class="text-gray-500 font-bold">-</span>
                                <span class="text-azul font-bold text-2xl md:text-3xl tabular-nums">{{ $partido->goles_visitante ?? 0 }}</span>
                            </div>
                            <div class="flex items-center gap-2 md:gap-3 flex-1 min-w-0 justify-end">
                                <span class="text-white font-bold text-base md:text-xl truncate text-right">{{ $partido->equipo_visitante }}</span>
                                <span class="text-3xl md:text-4xl">{{ \App\Helpers\BanderaHelper::get($partido->equipo_visitante) }}</span>
                            </div>
                        </div>
                        @include('partidos.partials.prediccion', ['partido' => $partido])
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 4. PRÓXIMOS PARTIDOS --}}
    <section id="proximos" class="scroll-mt-24">
        <h2 class="font-display font-bold text-white text-xl md:text-2xl mb-4">
            ⏱ Próximos <span class="text-azul">partidos</span>
        </h2>
        @forelse($proximos as $fecha => $partidosDia)
            <div class="mb-6">
                <h3 class="text-[#FF6B35] text-sm font-medium uppercase tracking-wide mb-3">
                    {{ \Carbon\Carbon::parse($fecha)->locale('es')->translatedFormat('l d \d\e F') }}
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($partidosDia as $partido)
                        <article class="bg-card border border-borde rounded-xl p-3 md:p-5">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[#FF6B35] text-sm font-medium uppercase">{{ $partido->grupo ?? $partido->fase }}</span>
                                <span class="text-gray-400 text-xs">
                                    {{ $partido->fecha_partido?->setTimezone(config('app.display_timezone'))->format('H:i') }} hrs
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex flex-col items-center gap-1 flex-1 min-w-0">
                                    <span class="text-3xl md:text-4xl">{{ \App\Helpers\BanderaHelper::get($partido->equipo_local) }}</span>
                                    <span class="text-white font-bold text-sm md:text-base text-center w-full truncate">{{ $partido->equipo_local }}</span>
                                </div>
                                <span class="text-[#FF6B35] font-display font-bold text-sm md:text-base shrink-0">VS</span>
                                <div class="flex flex-col items-center gap-1 flex-1 min-w-0">
                                    <span class="text-3xl md:text-4xl">{{ \App\Helpers\BanderaHelper::get($partido->equipo_visitante) }}</span>
                                    <span class="text-white font-bold text-sm md:text-base text-center w-full truncate">{{ $partido->equipo_visitante }}</span>
                                </div>
                            </div>
                            @include('partidos.partials.prediccion', ['partido' => $partido])
                        </article>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-card border border-borde rounded-xl p-6 text-center text-gray-400">
                No hay partidos próximos cargados todavía. El algoritmo se actualiza cada 60 segundos.
            </div>
        @endforelse
    </section>

    {{-- 5. PARTIDOS TERMINADOS --}}
    @if($terminados->isNotEmpty())
        <section id="terminados" class="scroll-mt-24">
            <h2 class="font-display font-bold text-white text-xl md:text-2xl mb-4">
                🏁 Partidos <span class="text-azul">terminados</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($terminados as $partido)
                    <article class="bg-card border border-borde rounded-xl p-3 md:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[#FF6B35] text-sm font-medium uppercase">{{ $partido->grupo ?? $partido->fase }}</span>
                            <span class="text-gray-500 text-xs uppercase">Final</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <span class="text-2xl md:text-3xl">{{ \App\Helpers\BanderaHelper::get($partido->equipo_local) }}</span>
                                <span class="text-white font-bold text-sm md:text-base truncate">{{ $partido->equipo_local }}</span>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <span class="text-azul font-bold text-2xl md:text-3xl tabular-nums">{{ $partido->goles_local }}</span>
                                <span class="text-gray-500 font-bold">-</span>
                                <span class="text-azul font-bold text-2xl md:text-3xl tabular-nums">{{ $partido->goles_visitante }}</span>
                            </div>
                            <div class="flex items-center gap-2 flex-1 min-w-0 justify-end">
                                <span class="text-white font-bold text-sm md:text-base truncate text-right">{{ $partido->equipo_visitante }}</span>
                                <span class="text-2xl md:text-3xl">{{ \App\Helpers\BanderaHelper::get($partido->equipo_visitante) }}</span>
                            </div>
                        </div>
                        @include('partidos.partials.prediccion', ['partido' => $partido])
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 6. TABLA POR GRUPOS --}}
    @if(!empty($grupos))
        <section id="grupos" class="scroll-mt-24">
            <h2 class="font-display font-bold text-white text-xl md:text-2xl mb-4">
                📋 Tabla por <span class="text-azul">grupos</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($grupos as $grupo => $equipos)
                    <div class="bg-card border border-borde rounded-xl p-3 md:p-4 overflow-x-auto min-w-0">
                        <h3 class="text-[#FF6B35] text-sm font-medium uppercase mb-2">{{ $grupo }}</h3>
                        <table class="w-full text-sm min-w-[420px]">
                            <thead>
                                <tr class="text-azul text-xs uppercase border-b border-borde">
                                    <th class="text-left py-2 font-semibold">Equipo</th>
                                    <th class="py-2 font-semibold">PJ</th>
                                    <th class="py-2 font-semibold">G</th>
                                    <th class="py-2 font-semibold">E</th>
                                    <th class="py-2 font-semibold">P</th>
                                    <th class="py-2 font-semibold">DG</th>
                                    <th class="py-2 font-semibold">Pts</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($equipos as $row)
                                    <tr class="border-b border-borde/60 last:border-0">
                                        <td class="py-2 text-left">
                                            <span class="mr-1">{{ \App\Helpers\BanderaHelper::get($row['equipo']) }}</span>
                                            <span class="text-white font-bold">{{ $row['equipo'] }}</span>
                                        </td>
                                        <td class="text-center text-gray-300">{{ $row['pj'] }}</td>
                                        <td class="text-center text-gray-300">{{ $row['g'] }}</td>
                                        <td class="text-center text-gray-300">{{ $row['e'] }}</td>
                                        <td class="text-center text-gray-300">{{ $row['p'] }}</td>
                                        <td class="text-center text-gray-300">{{ $row['dg'] > 0 ? '+' : '' }}{{ $row['dg'] }}</td>
                                        <td class="text-center text-azul font-bold">{{ $row['pts'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

@endsection
