<?php

namespace App\Listeners;

use App\Events\GuardarDteND;
use App\Mail\MailDteND;
use App\Models\Notificaciones;
use App\Models\Persona;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyMailGuardarDteND
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
    public function handle(GuardarDteND $event)
    {
        $dte = $event->dte;
        //POR AHORA VOY A ENVIAR EL CORREO SOLO A ANGEL ID:66, LUEGO DEBO RECORREOS TODOS LOS USUARIOS QUE DEBO ENVIAR EL CORREO
        enviarcorreo($dte,66);
        enviarcorreo($dte,71);
    }
}

function enviarcorreo($dte,$persona_id){
    $persona = Persona::findOrFail($persona_id);
    $rutaPantalla = urlPrevio();
    $rutaOrigen = urlActual();
    $notificaciones = new Notificaciones();
    $notificaciones->usuarioorigen_id = auth()->id();

    $aux_email = $persona->email;
    $notificaciones->usuariodestino_id = $persona->usuario->id;
    $notificaciones->vendedor_id = $dte->vendedor_id;
    $notificaciones->status = 1;                    
    $notificaciones->nombretabla = 'dte';
    $notificaciones->mensaje = 'Nota Debito: '.$dte->nrodocto;
    $notificaciones->nombrepantalla = $rutaPantalla; //'despachoord.indexguiafact';
    $notificaciones->rutaorigen = $rutaOrigen; //'despachoord/indexfactura';
    $notificaciones->rutadestino = 'reportdtend';
    $notificaciones->tabla_id = $dte->id;
    $notificaciones->accion = 'ND ' . $dte->nrodocto . ' emitida.';
    $notificaciones->mensajetitle = 'Nro. ND: '.$dte->nrodocto;
    $notificaciones->icono = 'fa fa-fw fa-minus-square-o text-red';
    $notificaciones->save();
    //$usuario = Usuario::findOrFail(auth()->id());
    $asunto = $notificaciones->mensaje;
    $cuerpo = $notificaciones->mensaje;

    Mail::to($aux_email)->send(new MailDteND($notificaciones,$asunto,$cuerpo,$dte));

}