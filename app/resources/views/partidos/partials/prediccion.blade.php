@php($pred = $partido->prediccion)
@if($pred)
    <div class="mt-3 pt-3 border-t border-borde">
        <div class="flex items-center justify-between gap-2">
            <span class="text-[#FF6B35] text-xs font-medium uppercase tracking-wide">Predicción del algoritmo</span>
            <span class="text-xs text-gray-400">{{ rtrim(rtrim(number_format($pred->confianza, 1), '0'), '.') }}% confianza</span>
        </div>

        <div class="flex items-center justify-center gap-3 mt-2">
            <span class="text-white font-bold text-sm md:text-base text-right flex-1 min-w-0 truncate">{{ $partido->equipo_local }}</span>
            <span class="text-azul font-bold text-xl md:text-2xl tabular-nums shrink-0">{{ $pred->pred_goles_local }}</span>
            <span class="text-gray-500 font-bold shrink-0">-</span>
            <span class="text-azul font-bold text-xl md:text-2xl tabular-nums shrink-0">{{ $pred->pred_goles_visitante }}</span>
            <span class="text-white font-bold text-sm md:text-base text-left flex-1 min-w-0 truncate">{{ $partido->equipo_visitante }}</span>
        </div>

        @if($partido->estado === 'finished')
            <div class="mt-2 flex justify-center">
                @switch($pred->resultado)
                    @case('exacto')
                        <span class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded">✅ EXACTO · 5 pts</span>
                        @break
                    @case('correcto')
                        <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded">✅ CORRECTO · 3 pts</span>
                        @break
                    @case('incorrecto')
                        <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">❌ INCORRECTO · 0 pts</span>
                        @break
                @endswitch
            </div>
        @endif

        <p class="text-gray-300 text-xs md:text-sm leading-relaxed mt-2">{{ $pred->razonamiento }}</p>
    </div>
@endif
