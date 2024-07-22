<?php

namespace App\Listeners;

use App\Events\EnviarEmailFactxVencer;
use App\Mail\MailFactxVencer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyMailFactxVencer
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
    public function handle(EnviarEmailFactxVencer $event)
    {
        $facturas = $event->facturas;
        $sucursal = $event->sucursal;
        if(is_null($sucursal)){
            $sucursal_nombre = "Sin Sucursal";
        }else{
            $sucursal_nombre = $sucursal->nombre;
        }
        $persona = $event->persona;
        $aux_email = $persona->email;
        $aux_email = "gmoreno@plastiservi.cl";
        $asunto = "Facturas por Vencer en los proximos 10 Días - Sucursal: $sucursal_nombre";
        if(count($facturas) <= 0){
            $aux_cuerpo = 
            "
            Estimado/a $persona->nombre $persona->apellido,\n\nNos complace informarle que actualmente no tiene ninguna factura pendiente de vencimiento en los próximos 10 días.\n\nGracias por su atención.
            <br>
            <span class='h3'>NO HAY INFORMACION PARA MOSTRAR - Sucursal: $sucursal_nombre</span>
            <br>";
        }else{
            $aux_cuerpo = 
            "Estimado/a $persona->nombre $persona->apellido
            <br>
            <span class='h3'>$asunto</span>
            <br>
            <div id='page_pdf'>
                <table id='factura_detalle'>
                    <thead>
                        <tr>
                            <th width='20px' style='text-align: center;'>RUT</th>
                            <th width='150px' style='text-align: left;'>Razon Social</th>
                            <th width='20px' style='text-align: center;'>N° Fact</th>
                            <th width='20px' style='text-align: center;'>FechFac</th>
                            <th width='20px' style='text-align: center;'>FechVenc</th>
                            <th width='20px' style='text-align: right;' title='Monto Factura'>Monto</th>
                            <th width='20px' style='text-align: right;' title='Deuda'>Deuda</th>
                        </tr>
                    </thead>
                    <tbody id='detalle_productos'>";
    
                    foreach ($facturas as $factura) {
                        //$aux_rut = formato_rut($factura->rut);
                        $aux_fechafact = date("d/m/Y", strtotime($factura->fecfact));
                        $aux_fechavenc = date("d/m/Y", strtotime($factura->fecvenc));
                        $aux_mnttot = number_format($factura->mnttot, 0, ',', '.');
                        $aux_deuda = number_format($factura->deuda, 0, ',', '.');
                        $aux_cuerpo .=
                        "<tr class='headt' style='height:150%;'>
                            <td style='text-align: center;'>$factura->rut</td>
                            <td style='text-align: left;'>$factura->razonsocial</td>
                            <td style='text-align: center;'>$factura->nrofav</td>
                            <td style='text-align: center;'>$aux_fechafact</td>
                            <td style='text-align: center;'>$aux_fechavenc</td>
                            <td style='text-align: right;'>$aux_mnttot</td>
                            <td style='text-align: right;'>$aux_deuda</td>
                        </tr>";
                        //<td class='textleft'>$aux_nvcantsaldo</td>
                    }
    
                    $aux_cuerpo .=
                    "</tbody>
                </table>
            </div>";
        }
        $cuerpo = $aux_cuerpo;

        Mail::to($aux_email)->send(new MailFactxVencer($asunto,$cuerpo));

    }
}
