<?php

namespace App\Listeners;

use App\Mail\MailAvisoRevisionAcuTec;
use App\Models\Notificaciones;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyMailAvisoRevisionAcuTec
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
        $rutaPantalla = urlPrevio();
        $rutaOrigen = urlActual();
        $cotizacion = $event->cotizacion;
        $notificaciones = new Notificaciones();
        $notificaciones->usuarioorigen_id = auth()->id();
        $aux_email = $cotizacion->vendedor->persona->email;
        if($cotizacion->vendedor->persona->usuario){
            $notificaciones->usuariodestino_id = $cotizacion->vendedor->persona->usuario->id;
            $aux_email = $cotizacion->vendedor->persona->usuario->email;
        }
        $notificaciones->vendedor_id = $cotizacion->vendedor_id;
        $notificaciones->status = 1;                    
        $notificaciones->nombretabla = 'cotizacion';
        $aux_mensaje = "";
        $aux_icono = "";
        $aux_rutadest = "";
        $aux_mensaje = "Tienes un nuevo Acuerdo Técnico en tu bandeja";
        $aux_icono = "fa fa-fw fa-warning text-primary";
        $aux_rutadest = "cotizaciontrans";
        $notificaciones->nombrepantalla = $rutaPantalla; //'cotizacion.indexguiafact';
        $notificaciones->rutaorigen = $rutaOrigen; //'cotizacion/indexfactura';
        $notificaciones->rutadestino = $aux_rutadest;
        $notificaciones->mensaje = $aux_mensaje;
        $notificaciones->tabla_id = $cotizacion->id;
        $notificaciones->accion = $aux_mensaje;
        $notificaciones->mensajetitle = $aux_mensaje;
        $notificaciones->icono = $aux_icono;
        $notificaciones->save();
        //$usuario = Usuario::findOrFail(auth()->id());
        $asunto = $notificaciones->mensaje;
        $cuerpo = $notificaciones->mensaje . " esperando ser validado.";
        $aux_email = "nrojas@plastiservi.cl";

        Mail::to($aux_email)->send(new MailAvisoRevisionAcuTec($notificaciones,$asunto,$cuerpo,$cotizacion));
    }
}
