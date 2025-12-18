<?php

namespace App\Listeners;

use App\Jobs\EnviarCorreosAprobarRechazoNotaVenta;
use App\Mail\MailAprobarRechazoNotaVenta;
use App\Models\EmailxLote;
use App\Models\Notificaciones;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyMailAprobarRechazoNotaVenta
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
        /* $arrayUsuarios = [];

        // Vendedor
        $arrayUsuarios[] = [
            "usuario_id" => $event->notaventa->vendedor->persona->usuario_id,
            "nombre" => $event->notaventa->vendedor->persona->usuario->nombre,
            "email" => $event->notaventa->vendedor->persona->usuario->email
        ];

        dispatch(
            new EnviarCorreosAprobarRechazoNotaVenta(
                $event->notaventa,
                auth()->id(),
                urlPrevio(),
                urlActual(),
                $arrayUsuarios
            )
        ); */
        $rutaPantalla = urlPrevio();
        $rutaOrigen = urlActual();
        $notaventa = $event->notaventa;
        $aux_mensaje2 = "";
        $arrayUsuarios = [];
        if($notaventa->aprobstatus == 3){
            $aux_mensaje = "Nota de Venta $notaventa->id fue APROBADA.";
            $aux_mensaje2 = "\nSi es el caso inicia el proceso de produccion y posterior despacho.";
            $aux_icono = "fa fa-fw fa-thumbs-o-up text-primary";    
        }else{
            $aux_mensaje = "Nota de Venta $notaventa->id fue RECHAZADA.";
            $aux_icono = "fa fa-fw fa-thumbs-o-down text-red";
        }
        //En comentario para que no envie correo a los usuarios que tengan acceso al menu de aprobar nota venta
        //$arrayUsuarios = usuariosConAccesoMenuURL($notaventa,"despachosol");
        $arrayUsuarios[] = [
            "usuario_id" => $notaventa->vendedor->persona->usuario_id,
            "nombre" => $notaventa->vendedor->persona->usuario->nombre,
            "email" => $notaventa->vendedor->persona->usuario->email
        ];
        $emailxlote = EmailxLote::where("id",4)->get();
        if(count($emailxlote) > 0){
            $emailxlote = EmailxLote::findOrFail(4);
            foreach($emailxlote->personas as $persona){
                $arrayUsuarios[] = [
                    "usuario_id" => $persona->usuario_id,
                    "nombre" => $persona->usuario->nombre,
                    "email" => $persona->usuario->email
                ];
            }
        }
        //dd($arrayUsuarios);
        foreach ($arrayUsuarios as $arrayUsuario) {
            $notificaciones = new Notificaciones();
            $notificaciones->usuarioorigen_id = auth()->id();
            $aux_email = $arrayUsuario["email"];
            $notificaciones->usuariodestino_id = $arrayUsuario["usuario_id"];
            $notificaciones->vendedor_id = $notaventa->vendedor_id;
            $notificaciones->status = 1;
            $notificaciones->nombretabla = 'notaventa';
            $aux_rutadest = "notaventaaprobar";
            $notificaciones->nombrepantalla = $rutaPantalla; //'notaventa.indexguiafact';
            $notificaciones->rutaorigen = $rutaOrigen; //'notaventa/indexfactura';
            $notificaciones->rutadestino = $aux_rutadest;
            $notificaciones->mensaje = $aux_mensaje;
            $notificaciones->tabla_id = $notaventa->id;
            $notificaciones->accion = $aux_mensaje;
            $notificaciones->mensajetitle = $aux_mensaje;
            $notificaciones->icono = $aux_icono;
            $notificaciones->save();
            //$usuario = Usuario::findOrFail(auth()->id());
            $asunto = $notificaciones->mensaje;

            $cuerpo = nl2br($aux_mensaje . $aux_mensaje2 . ($notaventa->aprobobs ? "\n\n<b>Observación:</b> " . $notaventa->aprobobs : "")) ;
            //dd($cuerpo);
            Mail::to($aux_email)->send(new MailAprobarRechazoNotaVenta($notificaciones,$asunto,$cuerpo,$notaventa));
        }
    }
}