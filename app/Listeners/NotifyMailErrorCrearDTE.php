<?php

namespace App\Listeners;

use App\Events\ErrorCrearDTE;
use App\Mail\MailErrorCrearDTE;
use App\Models\Foliocontrol;
use App\Models\Seguridad\Usuario;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyMailErrorCrearDTE
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
    public function handle(ErrorCrearDTE $event)
    {
        $aux_email = "gmoreno@plastiservi.cl";
        $foliocontrol = Foliocontrol::findOrFail($event->dte->foliocontrol_id);
        $asunto = "Error al crear DTE " . $foliocontrol->doc . " Nro: " . $event->aux_folio;

        Mail::to($aux_email)->send(new MailErrorCrearDTE($asunto,$event));

    }
}
