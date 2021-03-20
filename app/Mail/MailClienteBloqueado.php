<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailClienteBloqueado extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $msg;
    public $cuerpo;
    public $nombrevendedor;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($msg,$asunto,$cuerpo,$nombrevendedor)
    {
        $this->subject = " ID: " . $msg->id ." ". $asunto;
        $this->cuerpo = $cuerpo;
        $this->msg = $msg;
        $this->nombrevendedor = $nombrevendedor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.clientebloqueado');
    }
}
