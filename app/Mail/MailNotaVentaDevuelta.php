<?php

namespace App\Mail;

use App\Models\NotaVenta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailNotaVentaDevuelta extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $msg;
    public $cuerpo;
    public $rut;
    public $razonsocial;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($msg,$asunto,$cuerpo,$rut,$razonsocial)
    {
        $this->subject = " ID: " . $msg->id ." ". $asunto;
        $this->cuerpo = $cuerpo;
        $this->msg = $msg;
        $notaventa = NotaVenta::findOrFail($msg->tabla_id);
        $this->rut = $rut;
        $this->razonsocial = $razonsocial;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.notaventadevuelta');
    }
}
