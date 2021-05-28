<?php

namespace App\Listeners;

use App\Events\Notificacion;
use App\Mail\MailFacturaDespacho;
use App\Mail\MailNotificacion;
use App\Models\Notificaciones;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class CrearNotificacion
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
    public function handle(Notificacion $event)
    {
        $event->notificacion['usuarioorigen_id'] = auth()->id();
        $event->notificacion['nombrepantalla'] = urlPrevio();
        $event->notificacion['rutaorigen'] = urlActual();
        $notificacion = Notificaciones::create($event->notificacion);
        $detalle = $event->notificacion['detalle'];

        Mail::to($notificacion->usuariodestino->email)->send(new MailNotificacion($notificacion,$detalle));

    }
}
