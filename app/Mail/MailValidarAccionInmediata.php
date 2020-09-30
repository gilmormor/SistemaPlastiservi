<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailValidarAccionInmediata extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $msg;
    public $cuerpo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($msg,$asunto,$cuerpo)
    {
        $this->subject = " ID: " . $msg->id ." ". $asunto;
        $this->cuerpo = $cuerpo;
        $this->msg = $msg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.validaraccioninmediata');
    }
}
