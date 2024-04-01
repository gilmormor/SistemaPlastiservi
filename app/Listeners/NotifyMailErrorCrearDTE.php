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
        //$aux_email = $event->dte->usuario->email;
        $aux_email = "gmoreno@plastiservi.cl";
        $foliocontrol = Foliocontrol::findOrFail($event->dte->foliocontrol_id);
        if($event->origenError == 1){
            $aux_origen = "Subir DTE SII";
        }else{
            $aux_origen = "Subir DTE Cobranza";
        }
        $asunto = "Error $aux_origen " . $foliocontrol->doc . " Nro: " . $event->dte->nrodocto;

        Mail::to($aux_email)->send(new MailErrorCrearDTE($asunto,$event));
    }
}
