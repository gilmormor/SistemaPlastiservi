<?php

namespace App\Listeners;

use App\Mail\MailXMLCargaDocManager;
use App\Models\Foliocontrol;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyMailXMLCargaDocManager
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
        $aux_email = "gmoreno@plastiservi.cl";
        $foliocontrol = Foliocontrol::findOrFail($event->dte->foliocontrol_id);
        $asunto = "XML Cargar Documento Manager. " . $foliocontrol->doc . " Nro: " . $event->dte->nrodocto;

        Mail::to($aux_email)->send(new MailXMLCargaDocManager($asunto,$event));

    }
}
