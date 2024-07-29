<?php

namespace App\Console\Commands;

use App\Events\EnviarEmailFactxVencer;
use App\Models\DataCobranza;
use App\Models\EmailxLote;
use App\Models\Sucursal;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class RunEnviarEmailFactxVencer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datacobranza:enviaremailfxv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar por email las facturas por vencer';

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
        /* $request = new Request();
        $respuesta = DataCobranza::EnviarEmailFactxVencer($request);
        Event(new EnviarEmailFactxVencer($respuesta)); */

        $emailxlote = EmailxLote::findOrFail(1);
        foreach ($emailxlote->personas as $persona) {
            $aux_sucursales = sucFisXUsu($persona);
            foreach ($aux_sucursales as $sucursal_id) {
                //dd($sucursal_id);
                $sucursal = Sucursal::findOrFail($sucursal_id);
                $facturas = DataCobranza::EnviarEmailFactxVencer($persona,$sucursal_id);
                Event(new EnviarEmailFactxVencer($facturas,$sucursal,$persona));
            }
            $facturas = DataCobranza::EnviarEmailFactxVencer($persona,null);
            if(count($facturas) > 0){
                Event(new EnviarEmailFactxVencer($facturas,null,$persona));    
            }            
        }
    }
}
