<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailAvisoRevisionAcuTec extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $msg;
    public $cuerpo;
    public $tabla;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($msg,$asunto,$cuerpo,$tabla)
    {
        $this->subject = $asunto;
        $this->cuerpo = $cuerpo;
        $this->msg = $msg;
        $this->tabla = $tabla;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.avisorevisionacutec');
    }
}
