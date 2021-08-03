<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Giro;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportProdPendSolDespController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('producto-pendiente-soldesp');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];
        $clientevendedorArray = $arrayvend['clientevendedorArray'];
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $productos = Producto::productosxUsuario();
        $selecmultprod = 1;
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        return view('reportprodpendsoldesp.index', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','comunas','fechaAct','productos','selecmultprod','tablashtml'));
    
    }
    public function reporte(Request $request){
        $respuesta = array();
        $respuesta['exito'] = false;
        $respuesta['mensaje'] = "Código no Existe";
        $respuesta['tabla'] = "";
    
        if($request->ajax()){
            /*****CONSULTA POR PRODUCTO*****/
            $datas = consulta($request);
            $respuesta['tabla'] .= "<table id='tabla-data-listar' name='tabla-data-listar' class='table display AllDataTables table-hover table-condensed tablascons2' data-page-length='50'>
            <thead>
                <tr>
                    <th class='tooltipsC' title='Código Producto'>CodProd</th>
                    <th>Descripción</th>
                    <th>Diametro</th>
                    <th>Clase</th>
                    <th>Largo</th>
                    <th>Peso</th>
                    <th class='tooltipsC' title='Tipo Union'>TU</th>
                    <th style='text-align:right' class='tooltipsC' title='Cantidad Solicitada'>CantSol</th>
                    <th style='text-align:right' class='tooltipsC' title='Kg Solicitados'>KgSol</th>
                    <th style='text-align:right' class='tooltipsC' title='Cantidad Despachada'>Cant<br>Desp</th>
                    <th style='text-align:right' class='tooltipsC' title='Kg Despachados'>Kg<br>Desp</th>
                    <th style='text-align:right' class='tooltipsC' title='Cantidad Pendiente'>Cant<br>Pendiente</th>
                    <th style='text-align:right' class='tooltipsC' title='Kg Pendientes'>Kg<br>Pendientes</th>
                </tr>
            </thead>
            <tbody>";
            $aux_totalcanpend = 0;
            $aux_totalkgpend = 0;
            foreach ($datas as $data) {
                $respuesta['tabla'] .= "
                <tr>
                    <td>$data->producto_id</td>
                    <td>$data->nombre</td>
                    <td>$data->diametro</td>
                    <td>$data->cla_nombre</td>
                    <td>$data->long</td>
                    <td>$data->peso</td>
                    <td>$data->tipounion</td>
                    <td style='text-align:right'>". number_format($data->cantsoldesp, 0, ",", ".") ."</td>
                    <td style='text-align:right'>". number_format($data->kgsoldesp, 2, ",", ".") ."</td>
                    <td style='text-align:right'>". number_format($data->cantorddesp, 0, ",", ".") ."</td>
                    <td style='text-align:right'>". number_format($data->kgorddesp, 2, ",", ".") ."</td>
                    <td style='text-align:right'>". number_format(($data->cantsoldesp - $data->cantorddesp), 0, ",", ".") ."</td>
                    <td style='text-align:right'>". number_format(($data->kgsoldesp - $data->kgorddesp), 2, ",", ".") ."</td>
                </tr>";
                $aux_totalcanpend += ($data->cantsoldesp - $data->cantorddesp);
                $aux_totalkgpend += ($data->kgsoldesp - $data->kgorddesp);
    
            }
            $respuesta['tabla'] .= "
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan='11' style='text-align:right'>TOTALES</th>
                        <th style='text-align:right'>". number_format($aux_totalcanpend, 0, ",", ".") ."</th>
                        <th style='text-align:right'>". number_format($aux_totalkgpend, 2, ",", ".") ."</th>
                    </tr>
                </tfoot>
    
                </table>";
            //dd($respuesta['tabla']);
            return $respuesta;
        }
    }
    
    
}

function consulta($request){
    //dd($request);
    if(empty($request->vendedor_id)){
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $vendedorcond = "notaventa.vendedor_id=" . $vendedor_id ;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
            $sucurArray = $user->sucursales->pluck('id')->toArray();
        }else{
            $vendedorcond = " true ";
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
    }else{
        if(is_array($request->vendedor_id)){
            $aux_vendedorid = implode ( ',' , $request->vendedor_id);
        }else{
            $aux_vendedorid = $request->vendedor_id;
        }
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";

        //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }

    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "vista_soldespconsudespacho.fechahora>='$fechad' and vista_soldespconsudespacho.fechahora<='$fechah'";
    }
    if(empty($request->rut)){
        $aux_condrut = " true";
    }else{
        $aux_condrut = "cliente.rut='$request->rut'";
    }
    if(empty($request->oc_id)){
        $aux_condoc_id = " true";
    }else{
        $aux_condoc_id = "notaventa.oc_id='$request->oc_id'";
    }
    if(empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = " true";
    }else{
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }
    if(empty($request->tipoentrega_id)){
        $aux_condtipoentrega_id = " true";
    }else{
        $aux_condtipoentrega_id = "notaventa.tipoentrega_id='$request->tipoentrega_id'";
    }
    if(empty($request->notaventa_id)){
        $aux_condnotaventa_id = " true";
    }else{
        $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
    }


    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true ";
    }else{
        if(is_array($request->comuna_id)){
            $aux_comuna = implode ( ',' , $request->comuna_id);
        }else{
            $aux_comuna = $request->comuna_id;
        }
        $aux_condcomuna_id = " notaventa.comunaentrega_id in ($aux_comuna) ";
    }

    $aux_condproducto_id = " true";
    if(!empty($request->producto_id)){
        $aux_codprod = explode(",", $request->producto_id);
        $aux_codprod = implode ( ',' , $aux_codprod);
        $aux_condproducto_id = "vista_soldespconsudespacho.producto_id in ($aux_codprod)";
    }

    $sql = "SELECT producto_id,producto.nombre,
    producto.diametro,
    claseprod.cla_nombre,producto.long,producto.peso,producto.tipounion,
    sum(cantsoldesp) AS cantsoldesp,
    sum(kgsoldesp) AS kgsoldesp,
    sum(cantorddesp) AS cantorddesp,
    sum(kgorddesp) AS kgorddesp
    FROM vista_soldespconsudespacho INNER JOIN notaventa
    ON vista_soldespconsudespacho.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    INNER JOIN producto
    ON vista_soldespconsudespacho.producto_id=producto.id and isnull(producto.deleted_at)
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    INNER JOIN claseprod
    ON producto.claseprod_id=claseprod.id
    WHERE $vendedorcond
    and $aux_condFecha
    and $aux_condrut
    and $aux_condoc_id
    and $aux_condareaproduccion_id
    and $aux_condtipoentrega_id
    and $aux_condnotaventa_id
    and $aux_condcomuna_id
    and $aux_condproducto_id
    AND vista_soldespconsudespacho.kgorddesp < vista_soldespconsudespacho.kgsoldesp
    GROUP BY producto_id
    ORDER BY producto_id;";

    $datas = DB::select($sql);
    return $datas;
}