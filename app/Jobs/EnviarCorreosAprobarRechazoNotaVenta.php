<?php

namespace App\Jobs;

use App\Models\Notificaciones;
use App\Mail\MailAprobarRechazoNotaVenta;
use App\Models\EmailxLote;
use App\Models\NotaVenta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarCorreosAprobarRechazoNotaVenta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $notaventaId;
    protected $usuarioOrigenId;
    protected $rutaPantalla;
    protected $rutaOrigen;
    protected $arrayUsuarios;

    public function __construct($notaventaId, $usuarioOrigenId, $rutaPantalla, $rutaOrigen, $arrayUsuarios)
    {
        $this->notaventaId = $notaventaId;
        $this->usuarioOrigenId = $usuarioOrigenId;
        $this->rutaPantalla = $rutaPantalla;
        $this->rutaOrigen = $rutaOrigen;
        $this->arrayUsuarios = $arrayUsuarios;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /* $rutaPantalla = urlPrevio();
        $rutaOrigen = urlActual(); */
        //$notaventa = $this->notaventa;
        $notaventa = NotaVenta::with([
            'vendedor.persona.usuario',
            'vendedor'
        ])->findOrFail($this->notaventaId);

        if ($notaventa->aprobstatus == 3) {
            $aux_mensaje = "Nota de Venta $notaventa->id fue APROBADA.";
            $aux_mensaje2 = "\nSi es el caso inicia el proceso de produccion y posterior despacho.";
            $aux_icono = "fa fa-fw fa-thumbs-o-up text-primary";
        } else {
            $aux_mensaje = "Nota de Venta $notaventa->id fue RECHAZADA.";
            $aux_mensaje2 = "";
            $aux_icono = "fa fa-fw fa-thumbs-o-down text-red";
        }

        /* $arrayUsuarios = [];

        // Vendedor
        $arrayUsuarios[] = [
            "usuario_id" => $notaventa->vendedor->persona->usuario_id,
            "nombre" => $notaventa->vendedor->persona->usuario->nombre,
            "email" => $notaventa->vendedor->persona->usuario->email
        ]; */

        // Email por lote
        $emailxlote = EmailxLote::with([
                        'persona'
                    ])->find(4);
        if ($emailxlote) {
            foreach ($emailxlote->personas as $persona) {
                $arrayUsuarios[] = [
                    "usuario_id" => $persona->usuario_id,
                    "nombre" => $persona->usuario->nombre,
                    "email" => $persona->usuario->email
                ];
            }
        }

        foreach ($arrayUsuarios as $arrayUsuario) {

            $notificaciones = new Notificaciones();
            $notificaciones->usuarioorigen_id = $this->usuarioOrigenId;
            $notificaciones->usuariodestino_id = $arrayUsuario["usuario_id"];
            $notificaciones->vendedor_id = $notaventa->vendedor_id;
            $notificaciones->status = 1;
            $notificaciones->nombretabla = 'notaventa';
            $notificaciones->nombrepantalla = $this->rutaPantalla;
            $notificaciones->rutaorigen = $this->rutaOrigen;
            $notificaciones->rutadestino = "notaventaaprobar";
            $notificaciones->mensaje = $aux_mensaje;
            $notificaciones->tabla_id = $notaventa->id;
            $notificaciones->accion = $aux_mensaje;
            $notificaciones->mensajetitle = $aux_mensaje;
            $notificaciones->icono = $aux_icono;
            $notificaciones->save();

            $asunto = $notificaciones->mensaje;
            $cuerpo = nl2br(
                $aux_mensaje .
                $aux_mensaje2 .
                ($notaventa->aprobobs ? "\n\n<b>Observación:</b> " . $notaventa->aprobobs : "")
            );

            // ValidarEmailAntesDeSendListener valida formato y DNS antes del envío.
            // Si el correo es inválido, el envío se cancelará y se notificará a los administradores.
            Mail::to($arrayUsuario["email"])
                ->send(new MailAprobarRechazoNotaVenta(
                    $notificaciones,
                    $asunto,
                    $cuerpo,
                    $notaventa
                ));
        }
    }
    public function failed(\Throwable $e)
    {
        Log::error('Job EnviarCorreosAprobarRechazoNotaVenta FALLÓ', [
            'mensaje' => $e->getMessage(),
            'archivo' => $e->getFile(),
            'linea' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
