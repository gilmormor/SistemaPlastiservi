<?php

namespace App\Listeners;

use App\Http\Controllers\BitacoraController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;

class BitFinSesionUsuario
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //EN COMENTARIO 28/05/2021
        //POR AHORA NO ME INTERESA GUARDAR EL CIERRE DE SESION
        /*
        $request = new Request();
        $request->codmov = 'FS';
        $request->desc = 'Fin de Sesion';
        $bitacora = new BitacoraController;
        $bitacora->guardar($request);
        */
    }
}
