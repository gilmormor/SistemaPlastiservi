<?php

namespace App\Listeners;

use App\Mail\MailAcuTecAprobarRechazar;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Notificaciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class NotifyMailAcuTecAprobarRechazar
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
        switch ($cotizacion->aprobstatus) {
            case 2:
                $aux_mensaje = "Acuerdo Ténico APROBADO.";
                $aux_icono = "fa fa-fw fa-thumbs-o-up text-primary";
                $aux_rutadest = "cotizaciontrans";
                $cuerpo = $aux_mensaje . " A la espera de Validación Financiera" ;
                break;
            case 3:
                $aux_mensaje = "Cotización APROBADA (Info Financiera).";
                $aux_icono = "fa fa-fw fa-thumbs-o-up text-primary";
                $aux_rutadest = "cotizaciontrans";
                $cuerpo = $aux_mensaje . ". Cotizacion eta habilitada para realizar Nota de Venta" ;
                break;
            case 4:
                $aux_mensaje = "Cotización RECHAZADA (Info Financiera).";
                $aux_icono = "fa fa-fw fa-thumbs-o-down text-red";
                $aux_rutadest = "cotizaciontrans";
                $cuerpo = $aux_mensaje;
                break;
            case 7:
                $aux_mensaje = "Acuerdo Ténico RECHAZADO.";
                $aux_icono = "fa fa-fw fa-thumbs-o-down text-red";
                $aux_rutadest = "cotizaciontrans";
                $cuerpo = $aux_mensaje;
                break;
        }
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
        //$cuerpo = $notificaciones->mensaje;
        Mail::to($aux_email)->send(new MailAcuTecAprobarRechazar($notificaciones,$asunto,$cuerpo,$cotizacion));
        if($cotizacion->aprobstatus == 2){
            $notificaciones->mensaje .= " Esperando por aprobación financiera";
            $asunto = "Tienes una nueva Cotizacion en tu bandeja para ser validada.";
            $cuerpo = $asunto;
            $aux_email = "lmartinez@plastiservi.cl";
            Mail::to($aux_email)->send(new MailAcuTecAprobarRechazar($notificaciones,$asunto,$cuerpo,$cotizacion));    
        }
    }
}
