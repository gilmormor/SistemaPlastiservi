<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailNotificacion extends Mailable
{
    use Queueable, SerializesModels;

    public $notificacion;
    public $detalle;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($notificacion,$detalle)
    {
        $this->subject = $notificacion->mensaje;
        $this->notificacion = $notificacion;
        $this->detalle = $detalle;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.notificaciones');
    }
}
