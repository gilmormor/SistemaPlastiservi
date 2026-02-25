<?php

namespace App\Console\Commands;

use App\Models\AcuerdoTecnicoTemp;
use App\Models\Producto;
use Illuminate\Console\Command;

class PoblarNombreCompAcuerdoTecnicoTemp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acuerdotecnicotemp:poblar-glosa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pobla el campo glosa a partir de atributosProducto';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        AcuerdoTecnicoTemp::chunk(100, function ($attemps) {
            foreach ($attemps as $attemp) {
                // Solo si no tiene glosa
                if (!empty($attemp->at_glosa)) {
                    continue;
                }
                if (empty($attemp->at_glosa)) {
                    if(isset($attemp->cotizaciondetalle)){
                        $atributos = Producto::atributosProducto($attemp->cotizaciondetalle->producto_id,$attemp->at_cotizaciondetalle_id);
                        $attemp->at_glosa = $atributos['nombre'];
                        $attemp->save();
                    }
                }
            }
        });
    }
}
