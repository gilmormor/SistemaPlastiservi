<?php

namespace App\Listeners;

use App\Events\GuardarGuiaDespacho;
use App\Mail\MailInicioDespacho;
use App\Mail\MailNotaVentaDevuelta;
use App\Models\DespachoOrd;
use App\Models\Dte;
use App\Models\NotaVenta;
use App\Models\Notificaciones;
use App\Models\Producto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotifyMailGuardarGuiaDespacho
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
    public function handle(GuardarGuiaDespacho $event)
    {
        $rutaPantalla = urlPrevio();
        $rutaOrigen = urlActual();
        $despachoord = $event->despachoord;
        $dte = $event->dte;
        $notificaciones = new Notificaciones();
        $notificaciones->usuarioorigen_id = auth()->id();
        $aux_email = $despachoord->notaventa->vendedor->persona->email;
        if($despachoord->notaventa->vendedor->persona->usuario){
            $notificaciones->usuariodestino_id = $despachoord->notaventa->vendedor->persona->usuario->id;
            $aux_email = $despachoord->notaventa->vendedor->persona->usuario->email;
        }
        $notificaciones->vendedor_id = $despachoord->notaventa->vendedor_id;
        $notificaciones->status = 1;                    
        $notificaciones->nombretabla = 'despachoord';
        $notificaciones->mensaje = 'Guia Despacho:' . $despachoord->guiadespacho . ' OD:'.$despachoord->id.' NV:'.$despachoord->notaventa_id;
        $notificaciones->nombrepantalla = $rutaPantalla; 'despachoord.indexguiafact';
        $notificaciones->rutaorigen = $rutaOrigen;
        $notificaciones->rutadestino = 'notaventaconsulta';
        $notificaciones->tabla_id = $despachoord->id;
        $notificaciones->accion = 'Despacho Iniciado.';
        $notificaciones->mensajetitle = 'Nro. Guia despacho: '.$despachoord->guiadespacho;
        $notificaciones->icono = 'fa fa-fw fa-truck text-yellow ';
        $notificaciones->save();
        //$usuario = Usuario::findOrFail(auth()->id());
        $asunto = $notificaciones->mensaje;
        $cuerpo = $notificaciones->mensaje;



        $aux_rut = number_format( substr ( $dte->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $dte->cliente->rut, strlen($dte->cliente->rut) -1 , 1 );
        $aux_telefono = $dte->cliente->telefono;
        $aux_razonSocial = $dte->cliente->razonsocial;
        $aux_direccion = $dte->cliente->direccion;
        $aux_contactonombre = $dte->cliente->contactonombre;
        $aux_comunaNombre = $dte->cliente->comuna->nombre;
        $aux_sucursalNombre = $dte->sucursal->nombre;
        $aux_Guiadespnro = str_pad($dte->nrodocto, 10, "0", STR_PAD_LEFT);
        $datehoy = date('d/m/Y h:i:s A');
        $dateGuiaDesp = date('d/m/Y h:i:s A', strtotime($dte->fechahora));
        $aux_vendedorNombre = $dte->vendedor->persona->nombre . " " . $dte->vendedor->persona->apellido;
        $aux_notaventa_id = str_pad($despachoord->notaventa_id, 10, "0", STR_PAD_LEFT);
        $aux_cotizacion_id = str_pad($despachoord->notaventa->cotizacion_id, 10, "0", STR_PAD_LEFT);
        $aux_despachosol_id = str_pad($despachoord->despachosol_id, 10, "0", STR_PAD_LEFT);
        $aux_despachoord_id = str_pad($despachoord->id, 10, "0", STR_PAD_LEFT);
        

        $aux_oc_id = $dte->dteoc->oc_id;
        $aux_logo = asset("assets/lte/dist/img/LOGO-PLASTISERVI.png");
        $aux_GuiaDespDet = 
        "
        <br>
        <span class='h3'>GUIA DESPACHO</span>
        <br>
        <div id='page_pdf'>
            <table id='factura_head'>
                <tr>
                    <td class='logo_factura'>
                        <div>
                            <img src='$aux_logo' style='max-width:100%;width:auto;height:auto;'>
                        </div>
                    </td>
                    <td class='info_empresa'>
                    </td>
                    <td class='info_factura'>
                        <div>
                            <span class='h3'>Guia Despacho / $aux_sucursalNombre</span>
                            <p><strong>Guia Despacho Nro:</strong>$aux_Guiadespnro</p>
                            <p><strong>Fecha:</strong> $dateGuiaDesp</p>
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
                        <th width='50px' style='text-align: center;' title='Cant. Nota de Venta: $aux_notaventa_id'>Cant NV</th>
                        <th width='50px' style='text-align: center;' title='Cant. despacho Previo'>DespPrev</th>
                        <th width='50px' style='text-align: center;' title='Cant. Guia Desp: $aux_Guiadespnro'>GD</th>
                        <th width='50px' style='text-align: center;' title='Saldo Nota Venta: $aux_notaventa_id'>Saldo NV</th>
                    </tr>
                </thead>
                <tbody id='detalle_productos'>";

                //$dte = Dte::findOrFail(3246);
                $dtedets = [];
                foreach ($dte->dtedets as $dtedet) {
                    $dtedets[$dtedet->dtedet_despachoorddet->notaventadetalle_id] = [
                        "producto_id" => $dtedet->producto_id,
                        "qtyitem" => $dtedet->qtyitem
                    ];
                }
                //dd($dtedets);
                $notaventadetalles = $dte->dteguiadesp->despachoord->notaventa->notaventadetalles;
                $guiaDesp = [];
                foreach ($notaventadetalles as $notaventadetalle) {
                    $notaventadetalle_id = $notaventadetalle->id;
                    $notaventadetalle_cant = $notaventadetalle->cant;
                    /*************************/
                    //SUMA TOTAL DESPACHADO
                    /*************************/
                    $sql = "SELECT cantdesp,group_guiadespacho
                        FROM vista_sumorddespxnvdetid
                        WHERE notaventadetalle_id=$notaventadetalle_id;";
                    $datasumadesp = DB::select($sql);
                    $group_guiadespacho = "";
                    if(empty($datasumadesp)){
                        $sumacantdesp= 0;
                    }else{
                        $sumacantdesp= $datasumadesp[0]->cantdesp;
                        $group_guiadespacho = $datasumadesp[0]->group_guiadespacho;

                        $cadena = $datasumadesp[0]->group_guiadespacho;
                        $numero_a_eliminar = $dte->nrodocto;

                        // Convertir la cadena en un array
                        $array_numeros = explode(',', $cadena);

                        // Eliminar el número específico
                        $array_resultado = array_diff($array_numeros, [$numero_a_eliminar]);

                        // Convertir el array resultante en una cadena
                        $group_guiadespacho = implode(',', $array_resultado);
                    }
                    $aux_qtyitem = (isset($dtedets[$notaventadetalle_id]) ? $dtedets[$notaventadetalle_id]["qtyitem"] : 0);
                    $sumacantdespPrev = $sumacantdesp - $aux_qtyitem;
                    $aux_producto_id = $notaventadetalle->producto_id;
                    $atributoProd = Producto::atributosProducto($aux_producto_id);
                    $aux_producto_nombre = $atributoProd["nombre"];
                    $aux_cantGuiaDesp = number_format($aux_qtyitem, 0, ",", ".");
                    $aux_nvcantsaldo = number_format($notaventadetalle_cant - $sumacantdesp, 0, ",", ".");
                    $aux_unimed = $notaventadetalle->unidadmedida->nombre;
        
                    $guiaDesp[] =[
                        "producto_id" => $aux_producto_id,
                        "producto_nombre" => $aux_producto_nombre,
                        "unidadmedida_nombre" => $aux_unimed,
                        "notaventa_cant" => $notaventadetalle->cant,
                        "sumacantdespPrev" => $sumacantdespPrev,
                        "aux_cantGuiaDesp" => $aux_cantGuiaDesp,
                        "aux_nvcantsaldo" => $aux_nvcantsaldo
                    ];

                    $aux_GuiaDespDet .=
                    "<tr class='headt' style='height:150%;'>
                        <td style='text-align: center;'>$aux_producto_id</td>
                        <td class='textleft'>$aux_producto_nombre</td>
                        <td style='text-align: center;'>$aux_unimed</td>
                        <td style='text-align: center;'>$notaventadetalle_cant</td>
                        <td style='text-align: center;' title='GD: $group_guiadespacho'>$sumacantdespPrev</td>
                        <td style='text-align: center;'>$aux_cantGuiaDesp</td>
                        <td style='text-align: center;'>$aux_nvcantsaldo</td>
                    </tr>";

                }
                //dd($guiaDesp);
/*
                foreach ($dte->dtedets as $dtedet) {
                    $notaventadetalle_id = $dtedet->dtedet_despachoorddet->notaventadetalle_id;
                    $notaventadetalle_cant = $dtedet->dtedet_despachoorddet->notaventadetalle->cant;
                    $sql = "SELECT cantdesp
                        FROM vista_sumorddespxnvdetid
                        WHERE notaventadetalle_id=$notaventadetalle_id";
                    $datasumadesp = DB::select($sql);
                    if(empty($datasumadesp)){
                        $sumacantdesp= 0;
                    }else{
                        $sumacantdesp= $datasumadesp[0]->cantdesp;
                    }
                    $sumacantdespPrev = $sumacantdesp - $dtedet->qtyitem;
                    $aux_producto_id = $dtedet->producto_id;
                    $atributoProd = Producto::atributosProducto($aux_producto_id);
                    $aux_producto_nombre = $atributoProd["nombre"];
                    $aux_cantGuiaDesp = number_format($dtedet->qtyitem, 0, ",", ".");
                    $aux_cantsaldo = number_format($notaventadetalle_cant - $sumacantdesp, 0, ",", ".");
                    $aux_unimed = $dtedet->unidadmedida->nombre;
                    $aux_GuiaDespDet .=
                        "<tr class='headt' style='height:150%;'>
                            <td style='text-align: center;'>$aux_producto_id</td>
                            <td class='textleft'>$aux_producto_nombre</td>
                            <td style='text-align: center;'>$aux_unimed</td>
                            <td style='text-align: center;'>$notaventadetalle_cant</td>
                            <td style='text-align: center;'>$sumacantdespPrev</td>
                            <td style='text-align: center;'>$aux_cantGuiaDesp</td>
                            <td style='text-align: center;'>$aux_cantsaldo</td>
                        </tr>";
                }
                */
                $aux_obs = "";
                if (!is_null($dte->obs)){
                    $aux_obs = $dte->obs;
                }
                $aux_GuiaDespDet .=
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
                    <tr>
                        <td colspan='7' class='textleft' width='40%'><span><strong>Nota de Venta: </strong></span></td>
                        <td class='textleft' width='50%'><span>$aux_notaventa_id</span></td>
                    </tr>
                    <tr>
                        <td colspan='7' class='textleft' width='40%'><span><strong>Solicitud Despacho: </strong></span></td>
                        <td class='textleft' width='50%'><span>$aux_despachosol_id</span></td>
                    </tr>
                    <tr>
                        <td colspan='7' class='textleft' width='40%'><span><strong>Orden Despacho: </strong></span></td>
                        <td class='textleft' width='50%'><span>$aux_despachoord_id</span></td>
                    </tr>
                </table>
            </div>
        </div>";
        $cuerpo = $aux_GuiaDespDet;

        Mail::to($aux_email)->send(new MailInicioDespacho($notificaciones,$asunto,$cuerpo,$despachoord));

    }
}
