<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailFactxVencer extends Mailable
{
    use Queueable, SerializesModels;
    
    public $subject;
    public $cuerpo;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($asunto,$cuerpo)
    {
        $this->subject = $asunto;
        $this->cuerpo = $cuerpo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.factxvencer');
    }
}
