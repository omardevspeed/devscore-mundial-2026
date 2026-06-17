<?php

namespace App\Console\Commands;

use App\Services\HistoricoService;
use Illuminate\Console\Command;

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
