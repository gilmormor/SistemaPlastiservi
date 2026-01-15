<?php

namespace App\Listeners;

use App\Jobs\EnviarCorreosAprobarRechazoNotaVenta;
use App\Mail\MailAprobarRechazoNotaVenta;
use App\Models\EmailxLote;
use App\Models\Notificaciones;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Models\Producto;

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
                foreach ($persona->usuario->sucursales as $sucursal) {
                    if($notaventa->sucursal_id == $sucursal->id){
                        $arrayUsuarios[] = [
                            "usuario_id" => $persona->usuario_id,
                            "nombre" => $persona->usuario->nombre,
                            "email" => $persona->usuario->email
                        ];
                        break;
                    }
                }
            }
        }
        //dd($arrayUsuarios);
        $emails = [];
        foreach ($arrayUsuarios as $arrayUsuario) {
            $emails[] = $arrayUsuario['email'];
        }
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

        $aux_logo = asset("assets/lte/dist/img/LOGO-PLASTISERVI.png");
        $aux_sucursalNombre = $notaventa->sucursal->nombre;
        $aux_vendedorNombre = $notaventa->vendedor->persona->nombre . " " . $notaventa->vendedor->persona->apellido;
        $aux_rut = number_format( substr ( $notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $notaventa->cliente->rut, strlen($notaventa->cliente->rut) -1 , 1 );
        $aux_telefono = $notaventa->cliente->telefono;
        $aux_razonSocial = $notaventa->cliente->razonsocial;
        $aux_direccion = $notaventa->cliente->direccion;
        $aux_contactonombre = $notaventa->cliente->contactonombre;
        $aux_comunaNombre = $notaventa->cliente->comuna->nombre;
        $aux_oc_id = $notaventa->oc_id;
        $aux_cotizacion_id = str_pad($notaventa->cotizacion_id, 10, "0", STR_PAD_LEFT);
        $aux_fechahora = date("d-m-Y h:i:s A", strtotime($notaventa->fechahora));
        $aux_NotaVentaDet = 
        "
        <div id='page_pdf'>
            <table id='factura_head'>
                <tr>
                    <td class='logo_factura'>
                        <div>
                            
                        </div>
                    </td>
                    <td class='info_empresa'>
                    </td>
                    <td class='info_factura'>
                        <div>
                            <span class='h3'>NotaVenta / $aux_sucursalNombre</span>
                            <p><strong>NotaVenta Nro:</strong>$notaventa->id</p>
                            <p><strong>Fecha:</strong>$aux_fechahora</p>
                            <p><strong>Vendedor:</strong> $aux_vendedorNombre</p>
                        </div>
                    </td>
                </tr>
            </table>
            <div style='width:100% !important;'>
                <span class='h3'>Cliente</span>
                <table class='info_cliente'>
                    <tr>
                        <td><label><strong>Rut:</strong></label></td><td><p id='rutform' name='rutform'><p>$aux_rut</p></td>
                        <td><label><strong>Teléfono:</strong></label></td><td><p>$aux_telefono</p></td>
                    </tr>
                    <tr>
                        <td><label><strong>Nombre:</strong></label></td><td><p>$aux_razonSocial</p></td>
                        <td><label><strong>Dirección:</strong></label></td><td><p>$aux_direccion</p></td>
                    </tr>
                    <tr>
                        <td><label><strong>Contacto:</strong></label></td><td><p>$aux_contactonombre</p></td>
                        <td><label><strong>Comuna:</strong></label></td><td>$aux_comunaNombre<p></p></td>
                    </tr>
                </table>
            </div>
            <table id='factura_detalle'>
                <thead>
                    <tr>
                        <th width='50px' style='text-align: center;'>Cod</th>
                        <th width='300px' style='text-align: left;'>Descripción</th>
                        <th width='50px' style='text-align: center;'>UN</th>
                        <th width='50px' style='text-align: center;'>Cant</th>
                    </tr>
                </thead>
                <tbody id='detalle_productos'>";

                $notaventadetalles = $notaventa->notaventadetalles;
                $guiaDesp = [];
                foreach ($notaventadetalles as $notaventadetalle) {
                    $notaventadetalle_id = $notaventadetalle->id;
                    $notaventadetalle_cant = $notaventadetalle->cant;

                    $atributoProd = Producto::atributosProducto($notaventadetalle->producto_id);
                    $aux_producto_nombre = $atributoProd["nombre"];
                    $aux_unimed = $notaventadetalle->unidadmedida->nombre;

                    $aux_NotaVentaDet .=
                    "<tr class='headt' style='height:150%;'>
                        <td style='text-align: center;'>$notaventadetalle->producto_id</td>
                        <td class='textleft'>$aux_producto_nombre</td>
                        <td style='text-align: center;'>$aux_unimed</td>
                        <td style='text-align: center;'>$notaventadetalle->cant</td>
                    </tr>";

                }

                $aux_obs = "";
                if (!is_null($notaventa->observacion)){
                    $aux_obs = $notaventa->observacion;
                }
                $aux_NotaVentaDet .=
                "</tbody>
            </table>
            <div class='round2'>
                <p class='nota'><strong> <H3>Observaciones: $aux_obs</H3></strong></p>
            </div>
            <br>
            <div style='width:40% !important;'>
                <span class='h3'>Informacion</span>
                <table id='info_factura'>
                    <tr>
                        <td colspan='7' class='textleft' width='40%'><span><strong>Orden de Compra: </strong></span></td>
                        <td class='textleft' width='50%'><span>$aux_oc_id</span></td>
                    </tr>
                    <tr>
                        <td colspan='7' class='textleft' width='40%'><span><strong>Cotizacion: </strong></span></td>
                        <td class='textleft' width='50%'><span>$aux_cotizacion_id</span></td>
                    </tr>
                </table>
            </div>
        </div>";
        $cuerpo = $aux_NotaVentaDet . "<br><br>" . nl2br($aux_mensaje . $aux_mensaje2 . ($notaventa->aprobobs ? "\n\n<b>Observación:</b> " . $notaventa->aprobobs : "")) ;

        //$cuerpo = nl2br($aux_mensaje . $aux_mensaje2 . ($notaventa->aprobobs ? "\n\n<b>Observación:</b> " . $notaventa->aprobobs : "")) ;
        //dd($cuerpo);
        Mail::to("no-reply@plastiservi.cl")->bcc($emails)->send(new MailAprobarRechazoNotaVenta($notificaciones,$asunto,$cuerpo,$notaventa));
    }
}