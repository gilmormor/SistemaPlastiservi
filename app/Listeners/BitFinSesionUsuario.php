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
        $request = new Request();
        $request->codmov = 'FS';
        $request->desc = 'Fin de Sesion';
        $bitacora = new BitacoraController;
        $bitacora->guardar($request);
    }
}
