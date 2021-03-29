<?php

namespace App\Listeners;

use App\Models\DespachoSolDev;
use App\Models\Notificaciones;
use App\Models\Seguridad\Usuario;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DevolverSolDespNotificacion
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
        $usuario = Usuario::findOrFail(auth()->id());
        $despachosol = $event->despachosol;
        $notificaciones = new Notificaciones();
        $notificaciones->usuarioorigen_id = auth()->id();
        $notificaciones->usuariodestino_id = $despachosol->usuario_id;
        $notificaciones->vendedor_id = $despachosol->notaventa->vendedor_id;
        $notificaciones->status = 1;                    
        $notificaciones->tabla_id = $despachosol->id;
        $notificaciones->nombretabla = 'despachosol';
        $notificaciones->mensaje = 'Solicitud despacho devuelta';
        $notificaciones->nombrepantalla = 'despachoord.listardespachosol';
        $notificaciones->rutaorigen = 'despachosol/listarsoldesp';
        $notificaciones->rutadestino = 'despachosol/index';
        $notificaciones->accion = 'Devolucion Solicitud Despacho.';
        $notificaciones->mensajetitle = 'Devuelta por: '.$usuario->nombre;
        $notificaciones->icono = 'fa fa-fw fa-reply text-red';
        $notificaciones->save();
        $despachosoldev = new DespachoSolDev();
        $despachosoldev->despachosol_id = $despachosol->id;
        $despachosoldev->usuario_id = auth()->id();
        $despachosoldev->obs = $event->request->obs;
        $despachosoldev->save();
    }
}
