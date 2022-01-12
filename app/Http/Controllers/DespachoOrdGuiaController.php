<?php

namespace App\Http\Controllers;

use App\Models\DespachoOrd;
use App\Models\DespachoOrdAnul;
use App\Models\NotaVentaCerrada;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespachoOrdGuiaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-guia-despacho');
        $aux_vista = 'G';
        return view('despachoordguia.index', compact('aux_vista'));

    }

    public function despachoordguiapage(){
        $datas = consultaindex();
        return datatables($datas)->toJson();
    }

    public function totalizarindex(){
        $respuesta = array();
        $datas = consultaindex();
        $aux_totalkg = 0;
        $aux_subtotal = 0;
        //$aux_totaldinero = 0;
        foreach ($datas as $data) {
            $aux_totalkg += $data->aux_totalkg;
            $aux_subtotal += $data->subtotal;
        }
        $respuesta['aux_totalkg'] = $aux_totalkg;
        $respuesta['aux_subtotal'] = $aux_subtotal;
        return $respuesta;
    }


}

function consultaindex(){

    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,despachoord.fechaestdesp,
    cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
    '' as notaventaxk,comuna.nombre as comuna_nombre,
    tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
    SUM(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
    sum(round((despachoorddet.cantdesp * notaventadetalle.preciounit) * ((notaventa.piva+100)/100))) as subtotal
    FROM despachoord INNER JOIN notaventa
    ON despachoord.notaventa_id = notaventa.id AND ISNULL(despachoord.deleted_at) and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
    INNER JOIN despachoorddet
    ON despachoorddet.despachoord_id = despachoord.id AND ISNULL(despachoorddet.deleted_at)
    INNER JOIN notaventadetalle
    ON notaventadetalle.id = despachoorddet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
    INNER JOIN tipoentrega
    ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
    LEFT JOIN clientebloqueado
    ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    WHERE despachoord.aprguiadesp='1' and isnull(despachoord.guiadespacho)
    AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
    AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
    AND notaventa.sucursal_id in ($sucurcadena)
    GROUP BY despachoorddet.despachoord_id;";

    return DB::select($sql);

}