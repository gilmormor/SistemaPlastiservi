<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ValidarEmailAntesDeSendListener
{
    /**
     * Handle the event.
     *
     * Valida formato y DNS de cada destinatario antes de que Laravel envíe el correo.
     * Si algún correo es inválido, cancela el envío (return false) y notifica a los
     * administradores. El flujo del sistema nunca se interrumpe.
     *
     * @param  \Illuminate\Mail\Events\MessageSending  $event
     * @return bool|null  Retornar false cancela el envío
     */
    public function handle(MessageSending $event)
    {
        // Correos de alerta interna: se excluyen de la validación para evitar bucle infinito
        $alertEmails = ['gmoreno@plastiservi.cl', 'bjuica@plastiservi.cl'];

        $destinatarios = array_keys($event->message->getTo() ?? []);

        foreach ($destinatarios as $email) {
            // Los correos de alerta interna se omiten de la validación
            if (in_array($email, $alertEmails)) {
                continue;
            }

            $emailValido = filter_var($email, FILTER_VALIDATE_EMAIL)
                        && checkdnsrr(substr(strrchr($email, "@"), 1), 'MX');

            if (!$emailValido) {
                $asunto = $event->message->getSubject() ?? '(sin asunto)';
                Log::warning('ValidarEmailAntesDeSendListener: correo inválido "' . $email . '" — envío cancelado. Asunto: ' . $asunto);
                try {
                    Mail::raw(
                        "Correo inválido detectado — envío cancelado.\nCorreo: {$email}\nAsunto original: {$asunto}",
                        function ($m) use ($alertEmails) {
                            $m->to($alertEmails)->subject('Correo inválido detectado');
                        }
                    );
                } catch (\Exception $e) {
                    Log::warning('ValidarEmailAntesDeSendListener: no se pudo enviar alerta — ' . $e->getMessage());
                }
                return false;
            }
        }
    }
}
