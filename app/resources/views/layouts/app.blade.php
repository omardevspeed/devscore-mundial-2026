<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DevScore Mundial 2026 — Predicciones en vivo</title>
    <meta name="description" content="Partidos del Mundial 2026 en tiempo real con predicciones automáticas de marcador exacto. Por Omar Curvelo — @OmarDevSpeed.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        negro:   '#0A0A0A',
                        azul:    '#00D4FF',
                        speed:   '#FF6B35',
                        card:    '#111111',
                        borde:   '#1F1F1F',
                        acierto: '#22c55e',
                        fallo:   '#E24B4A',
                    },
                    fontFamily: {
                        display: ['"Space Grotesk"', 'sans-serif'],
                        body:    ['Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-display { font-family: 'Space Grotesk', sans-serif; }
        ::-webkit-scrollbar { height: 8px; width: 8px; }
        ::-webkit-scrollbar-track { background: #0A0A0A; }
        ::-webkit-scrollbar-thumb { background: #1F1F1F; border-radius: 9999px; }
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 0 1px #FF6B35, 0 0 12px rgba(255,107,53,.25); }
            50%      { box-shadow: 0 0 0 1px #FF6B35, 0 0 22px rgba(255,107,53,.55); }
        }
        .live-border { animation: glow 1.8s ease-in-out infinite; }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-negro text-white min-h-screen antialiased"
      x-data="dashboard()" x-init="init()">

    {{-- HEADER --}}
    @php($navItems = array_filter([
        ['href' => '#estadisticas', 'label' => 'Estadísticas', 'show' => true],
        ['href' => '#en-vivo',      'label' => 'En vivo',      'show' => $enVivo->isNotEmpty()],
        ['href' => '#proximos',     'label' => 'Próximos',     'show' => true],
        ['href' => '#terminados',   'label' => 'Terminados',   'show' => $terminados->isNotEmpty()],
        ['href' => '#grupos',       'label' => 'Tabla por grupos', 'show' => !empty($grupos)],
    ], fn ($i) => $i['show']))
    <header class="bg-negro border-b border-borde sticky top-0 z-50 backdrop-blur">
        <div class="max-w-6xl mx-auto px-4 py-3 md:py-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-omardevspeed.png') }}"
                     alt="OmarDevSpeed"
                     class="h-10 w-10 md:h-11 md:w-11 rounded-xl object-cover shrink-0 ring-1 ring-borde">

                <div class="leading-tight">
                    <h1 class="font-display font-bold text-base text-white sm:text-lg md:text-xl">
                        DevScore <span class="text-azul">Mundial 2026</span>
                    </h1>
                    <p class="text-xs text-gray-400">
                        por <span class="text-speed font-medium">@OmarDevSpeed</span> 🇻🇪🇨🇱
                    </p>
                </div>
            </div>

            {{-- Navegación desktop --}}
            <nav class="hidden lg:flex items-center gap-1 text-sm">
                @foreach($navItems as $item)
                    <a href="{{ $item['href'] }}"
                       class="px-3 py-1.5 rounded-lg text-gray-300 font-medium hover:text-white hover:bg-card transition">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2 md:gap-3">
                <span x-show="hayEnVivo"
                      class="animate-pulse bg-speed text-white text-[10px] sm:text-xs font-bold px-2 py-1 rounded uppercase tracking-wide"
                      x-cloak>
                    ● <span x-text="cantidadEnVivo"></span> en vivo
                </span>
                <span class="hidden sm:inline text-xs text-gray-400">
                    Actualizado hace <span class="text-azul font-medium" x-text="haceSegundos"></span>s
                </span>

                {{-- Botón hamburguesa (mobile / tablet) --}}
                <button type="button"
                        class="lg:hidden inline-flex items-center justify-center h-9 w-9 rounded-lg border border-borde text-white hover:bg-card transition"
                        :aria-expanded="menuOpen"
                        aria-label="Abrir menú"
                        @click="menuOpen = !menuOpen">
                    <svg x-show="!menuOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="menuOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Menú desplegable (mobile / tablet) --}}
        <nav x-show="menuOpen" x-cloak x-transition.opacity
             @click.outside="menuOpen = false"
             class="lg:hidden border-t border-borde bg-negro">
            <div class="max-w-6xl mx-auto px-4 py-2 flex flex-col">
                @foreach($navItems as $item)
                    <a href="{{ $item['href'] }}"
                       @click="menuOpen = false"
                       class="px-3 py-3 rounded-lg text-gray-200 font-medium hover:text-white hover:bg-card transition border-b border-borde/50 last:border-0">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </nav>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-6 md:py-8 space-y-10 md:space-y-14">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="border-t border-borde mt-10">
        <div class="max-w-6xl mx-auto px-4 py-8 text-center space-y-3">
            <p class="font-display font-bold text-white text-lg">
                Devscore <span class="text-azul">Mundial 2026</span>
            </p>
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-sm text-gray-300">
                <span class="text-speed font-medium">@OmarDevSpeed</span>
                <a href="https://youtube.com/@OmarDevSpeed" class="hover:text-azul transition" target="_blank" rel="noopener">YouTube</a>
                <a href="https://tiktok.com/@OmarDevSpeed" class="hover:text-azul transition" target="_blank" rel="noopener">TikTok</a>
                <a href="https://instagram.com/OmarDevSpeed" class="hover:text-azul transition" target="_blank" rel="noopener">Instagram</a>
                <a href="https://github.com/omardevspeed/devscore-mundial-2026" class="hover:text-azul transition" target="_blank" rel="noopener">GitHub</a>
            </div>
            <p class="text-xs text-gray-400">Construido en vivo en YouTube — Repo público en GitHub</p>
            <p class="text-xs text-gray-500">Predicciones basadas en Qatar 2022 + Rusia 2018 · 100% gratuito</p>
        </div>
    </footer>

    <script>
        function dashboard() {
            return {
                menuOpen: false,
                hayEnVivo: {{ $enVivo->isNotEmpty() ? 'true' : 'false' }},
                cantidadEnVivo: {{ $enVivo->count() }},
                haceSegundos: 0,
                _timer: null,
                init() {
                    setInterval(() => this.haceSegundos++, 1000);
                    this._timer = setInterval(() => this.refrescar(), 60000);
                },
                async refrescar() {
                    try {
                        const r = await fetch('{{ route('refresh') }}', { headers: { 'Accept': 'application/json' } });
                        if (!r.ok) return;
                        const data = await r.json();
                        const previo = this.hayEnVivo;
                        this.hayEnVivo = data.hay_en_vivo;
                        this.cantidadEnVivo = data.cantidad_en_vivo;
                        this.haceSegundos = 0;
                        // Si cambió el estado de partidos en vivo, recargar contenido completo.
                        if (previo !== data.hay_en_vivo) {
                            window.location.reload();
                        }
                    } catch (e) { /* silencioso */ }
                },
            };
        }
    </script>
    <style>[x-cloak]{display:none!important;}</style>
</body>
</html>
