<?php

namespace App\Listeners;

use App\Mail\MailAvisoRevisionNotaVenta;
use App\Models\Admin\Menu;
use App\Models\Notificaciones;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyMailAvisoRevisionNotaVenta
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
        $rutaPantalla = urlPrevio();
        $rutaOrigen = urlActual();
        $notaventa = $event->notaventa;
        //en comentario porque a Sonia le molestaron los mensajes
        //$arrayUsuarios = usuariosConAccesoMenuURL($notaventa,"notaventaaprobar");
        //en comentario porque a Sonia le molestaron los mensajes
        $arrayUsuarios = []; //dejo en blanco los usuarios
        /* ESTO LO MANDE A LA FUNCION usuariosConAccesoMenuURL
        $menu = Menu::where("url","=","notaventaaprobar")->get(); //BUSCO EN LA TABLA MENU EL REGISTRO QUE CONTIENE EL URL notaventaaprobar
        $menu = Menu::findOrFail($menu[0]->id); //LUEGO BUSCO EL ID EN MENU PARA TENER EL OBJETO COMPLETO CON SUS TABLAS HIJAS
        $arrayUsuarios = []; //ARRAY PARA ALMACENAR LOS USUARIOS
        //LUEGO RECORRO Y BUSCO TODOS LOS USUARIO QUE TIENEN ACCESO A ESTE URL PARA ENVIARLES EL CORREO
        //PERO SOLO LOS USUARIOS TIENEN ACCESO A LA MISMA SUCURSAL DE LA NOTA DE VENTA, PUEDEN SER VARIOS USUARIOS UE TENGAN ACCESO A APROBAR NOTAS DE VENTA
        foreach ($menu->menuroles as $menurol) {
            if($menurol->rol_id != 1){
                foreach ($menurol->usuarioroles as $usuariorol){
                    foreach ($usuariorol->usuario->sucursales as $sucursal) {
                        if($event->notaventa->sucursal_id == $sucursal->id){
                            $arrayUsuarios[] = [
                                "usuario_id" =>$usuariorol->usuario->id,
                                "nombre" => $usuariorol->usuario->nombre,
                                "email" => $usuariorol->usuario->email
                            ];
                        }
                    }
                }
            }
        }
        */
        foreach ($arrayUsuarios as $arrayUsuario) {
            //dd($arrayUsuario);
            $notificaciones = new Notificaciones();
            $notificaciones->usuarioorigen_id = auth()->id();
            $aux_email = $arrayUsuario["email"];
            $notificaciones->usuariodestino_id = $arrayUsuario["usuario_id"];
            $notificaciones->vendedor_id = $notaventa->vendedor_id;
            $notificaciones->status = 1;
            $notificaciones->nombretabla = 'notaventa';
            $aux_mensaje = "Tienes una nueva Nota de Venta $notaventa->id para ser validada en tu bandeja.";
            $aux_icono = "fa fa-fw fa-warning text-primary";
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
            $cuerpo = $notificaciones->mensaje . " esperando ser validado.";

            $cuerpo = nl2br($aux_mensaje . "\n\nPara validar Nota de Venta puedes ingresar al siguiente enlace: (Previamente debes ingresar al sistema con usuario y clave)".
            "\n<a href='" . urlRaiz() ."/notaventaaprobar/$notaventa->id/editar/'>" .
                "Clic aqui." .
            "</a>") ;
            Mail::to($aux_email)->send(new MailAvisoRevisionNotaVenta($notificaciones,$asunto,$cuerpo,$notaventa));
        }

    }
}
