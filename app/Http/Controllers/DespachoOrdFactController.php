<?php

namespace App\Http\Controllers;

use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespachoOrdFactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-factura-despacho');
        $aux_vista = 'F';
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
        ->whereIn('sucursal.id', $sucurArray)
        ->get();
        return view('despachoordfact.index', compact('tablashtml','aux_vista'));

    }

    public function despachoordfactpage(Request $request){
        $datas = consultaindex($request);
        return datatables($datas)->toJson();
    }

    public function totalizarindex(Request $request){
        $respuesta = array();
        $datas = consultaindex($request);
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

function consultaindex($request){
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);
    if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "x")){
        $aux_sucursal_idCond = "false";
    }else{
        $aux_sucursal_idCond = "notaventa.sucursal_id = $request->sucursal_id";
    }


    $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,despachoord.fechaestdesp,
    cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
    '' as notaventaxk,comuna.nombre as comuna_nombre,despachoord.guiadespacho,
    tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
    SUM(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
    sum(round((despachoorddet.cantdesp * notaventadetalle.preciounit) * ((notaventa.piva+100)/100))) as subtotal,
    despachoord.updated_at
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
    WHERE despachoord.aprguiadesp='1' 
    AND $aux_sucursal_idCond
    and NOT isnull(despachoord.guiadespacho) 
    and isnull(despachoord.numfactura)
    AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
    AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
    AND notaventa.sucursal_id in ($sucurcadena)
    GROUP BY despachoorddet.despachoord_id;";

    return DB::select($sql);

}