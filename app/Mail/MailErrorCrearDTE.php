<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailErrorCrearDTE extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $event;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($asunto,$event)
    {
        $this->subject = $asunto;
        $this->event = $event;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.errorcreardte');
    }
}
