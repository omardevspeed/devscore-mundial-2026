# DevScore Mundial 2026 ⚽🔮

Plataforma pública que muestra los partidos del **Mundial 2026** con **predicciones automáticas de marcador exacto** generadas por un algoritmo propio basado en datos históricos reales (Qatar 2022 + Rusia 2018) ajustados con los resultados del propio Mundial 2026.

> Creado por **Omar Curvelo** — [@OmarDevSpeed](https://youtube.com/@OmarDevSpeed) 🇻🇪🇨🇱
> Costo total del proyecto: **$0** (sin APIs de pago, sin keys).

---

## Stack

| Capa        | Tecnología                                  |
|-------------|---------------------------------------------|
| Backend     | Laravel 11 (PHP 8.4)                         |
| Frontend    | Blade + Tailwind CSS (CDN) + Alpine.js (CDN) |
| Base datos  | MySQL 8                                      |
| Deploy      | Docker + docker-compose + Nginx             |
| Scheduler   | Laravel Scheduler (`mundial:sync` cada minuto) |

## Fuentes de datos (gratuitas, sin key)

- **Fixture + resultados 2026:** `openfootball/worldcup.json` → `2026/worldcup.json`
- **Históricos:** `openfootball/worldcup.json` → `2022/worldcup.json` y `2018/worldcup.json`

> Nota: la API original `rezarahiminia/worldcup2026/.../matches.json` del prompt ya no existe (404). Se reemplazó por el dataset de **openfootball 2026**, que es gratuito, estable y comparte el mismo formato que los históricos.

---

## Puesta en marcha

Requisitos: **Docker** y **Docker Compose**.

```bash
# 1. Construir las imágenes (builder clásico por compatibilidad de rutas con acentos)
DOCKER_BUILDKIT=0 COMPOSE_DOCKER_CLI_BUILD=0 docker compose build

# 2. Levantar todo
docker compose up -d
```

El contenedor `app` automáticamente:
1. Espera a MySQL.
2. Genera `APP_KEY` si falta.
3. Corre migraciones.
4. Carga históricos (`mundial:historico`).
5. Hace el primer sync (`mundial:sync`).
6. Cachea config, rutas y vistas.

Abre 👉 **http://localhost:8080**

El servicio `scheduler` ejecuta `mundial:sync` cada minuto (sincroniza partidos, genera predicciones para los que faltan y valida las de partidos finalizados).

### Comandos útiles

```bash
docker compose exec app php artisan mundial:sync       # Sincroniza + predice + valida
docker compose exec app php artisan mundial:historico  # Recarga históricos
docker compose logs -f app                             # Logs de la app
docker compose down                                    # Detener (conserva datos)
docker compose down -v                                 # Detener y borrar la BD
```

---

## Algoritmo de predicción

- **Índice de fuerza:** `(GF×2) − (GC×1.5) + (V×3) + E + bonus_fase`
- **Goles predichos:** `promedio_ataque × factor_defensa_rival × factor_fase`
- **Ajuste dinámico:** `60%` histórico + `40%` resultados reales 2026.
- **Confianza:** `min(88, 50 + diferencia_indices × 1.5)` (rango 50%–88%).

**Puntuación de aciertos:** marcador exacto = `5 pts`, ganador correcto = `3 pts`, fallo = `0 pts`.

---

## Estructura

```
.
├── app/                         # Aplicación Laravel 11
│   ├── app/Helpers/BanderaHelper.php
│   ├── app/Models/{Partido,Prediccion,EstadisticaHistorica}.php
│   ├── app/Services/{WorldCupApiService,HistoricoService,PrediccionService}.php
│   ├── app/Console/Commands/{SyncPartidos,CargarHistorico}.php
│   ├── app/Http/Controllers/PartidoController.php
│   ├── routes/{web,console}.php
│   └── resources/views/{layouts/app,partidos/index,partidos/partials/prediccion}.blade.php
├── docker/entrypoint.sh
├── nginx/default.conf
├── Dockerfile
└── docker-compose.yml
```

---

## Identidad visual

Dark mode permanente · negro base `#0A0A0A` · azul eléctrico `#00D4FF` (scores) · naranja speed `#FF6B35` (acentos/en vivo) · verde acierto `#22c55e` · rojo error `#E24B4A`. Tipografías **Space Grotesk** (títulos) + **Inter** (cuerpo). 100% responsivo, mobile-first.
