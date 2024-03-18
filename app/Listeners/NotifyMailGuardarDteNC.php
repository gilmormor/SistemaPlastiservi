<?php

namespace App\Listeners;

use App\Events\GuardarDteNC;
use App\Mail\MailDteNC;
use App\Models\Notificaciones;
use App\Models\Persona;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;


class NotifyMailGuardarDteNC
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
    public function handle(GuardarDteNC $event)
    {
        $rutaPantalla = urlPrevio();
        $rutaOrigen = urlActual();
        $dte = $event->dte;
        $notificaciones = new Notificaciones();
        $notificaciones->usuarioorigen_id = auth()->id();
        //POR AHORA VOY A ENVIAR EL CORREO SOLO A ANGEL ID:66, LUEGO DEBO RECORREOS TODOS LOS USUARIOS QUE DEBO ENVIAR EL CORREO
        $persona = Persona::findOrFail(66);
        $aux_email = $persona->email;
        $notificaciones->usuariodestino_id = $persona->usuario->id;
        $notificaciones->vendedor_id = $dte->vendedor_id;
        $notificaciones->status = 1;                    
        $notificaciones->nombretabla = 'dte';
        $notificaciones->mensaje = 'Nota Credito: '.$dte->nrodocto;
        $notificaciones->nombrepantalla = $rutaPantalla; //'despachoord.indexguiafact';
        $notificaciones->rutaorigen = $rutaOrigen; //'despachoord/indexfactura';
        $notificaciones->rutadestino = 'reportdtenc';
        $notificaciones->tabla_id = $dte->id;
        $notificaciones->accion = 'NC ' . $dte->nrodocto . ' emitida.';
        $notificaciones->mensajetitle = 'Nro. NC: '.$dte->nrodocto;
        $notificaciones->icono = 'fa fa-fw fa-minus-square-o text-red';
        $notificaciones->save();
        //$usuario = Usuario::findOrFail(auth()->id());
        $asunto = $notificaciones->mensaje;
        $cuerpo = $notificaciones->mensaje;

        Mail::to($aux_email)->send(new MailDteNC($notificaciones,$asunto,$cuerpo,$dte));

    }
}
