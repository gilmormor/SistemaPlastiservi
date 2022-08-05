<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaGrupoValMes;
use App\Models\Empresa;
use App\Models\InvBodega;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class ReportInvMovController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-movimiento-inventario');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $sucursales = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablashtml['sucursales'] = $sucursales;
        $tablashtml['areaproduccions'] = AreaProduccion::orderBy('id')->get();
        $tablashtml['invbodegas'] = InvBodega::orderBy('id')->get();
        $productos = Producto::productosxUsuario();
        $selecmultprod = 1;
        return view('reportinvmov.index', compact('tablashtml','productos','selecmultprod'));
    }

    public function reporte(Request $request){
    
        if($request->ajax()){
            //dd($request);
            $datas = consultainvmov($request);
            return datatables($datas)->toJson();
        }
    }

    public function totalizarRep(Request $request){
        $respuesta = array();
        $datas = consultainvmov($request);
        $aux_totalcant = 0;
        //$aux_totaldinero = 0;
        foreach ($datas as $data) {
            $aux_totalcant += $data->cant;
            //$aux_totaldinero += $data->subtotal;
        }
        $respuesta['aux_totalcant'] = $aux_totalcant;
        //$respuesta['aux_totaldinero'] = $aux_totaldinero;
        return $respuesta;
    }

    public function exportPdf(Request $request)
    {
        $datas = consultainvmov($request);

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
       
        if($datas){
            
            if(env('APP_DEBUG')){
                return view('reportinvmov.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            $pdf = PDF::loadView('reportinvmov.listado', compact('datas','empresa','usuario','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteMovInv.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }


}

function consultainvmov($request){
    //dd($request->annomes);
    $aux_annomes = CategoriaGrupoValMes::annomes($request->annomes);
    $aux_condannomes = "invmov.annomes='$aux_annomes'";
    $aux_condsucursal_id = " true";
    if(empty($request->sucursal_id)){
        $aux_condsucursal_id = "invmov.sucursal_id='$request->sucursal_id'";
    }
    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "invmov.fechahora>='$fechad' and invmov.fechahora<='$fechah'";
    }
    $aux_condareaproduccion_id = " true";
    if(!empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }
    $aux_condproducto_idPxP = " true";
    if(!empty($request->producto_idPxP)){
        $aux_codprod = explode(",", $request->producto_idPxP);
        $aux_codprod = implode ( ',' , $aux_codprod);
        $aux_condproducto_idPxP = "invmovdet.producto_id in ($aux_codprod)";
    }
    $aux_condinvbodega_id = " true";
    if(!empty($request->invbodega_id)){
        $aux_condinvbodega_id = "invmovdet.invbodega_id='$request->invbodega_id'";
    }
    $sql = "SELECT invmov.id,invmovdet.id as invmovdet_id,invmov.fechahora,invmov.annomes,invmov.desc,invbodegaproducto.producto_id,invmov.idmovmod,
            invmovmodulo.nombre as invmovmodulo_nombre,
            invbodega.nombre as invbodega_nombre,sucursal.nombre as sucursal_nombre,producto.nombre as producto_nombre,categoriaprod.nombre as categoriaprod_nombre,invmovdet.cant
            FROM invmov INNER JOIN invmovdet
            ON invmov.id = invmovdet.invmov_id and isnull(invmov.deleted_at) and isnull(invmov.staanul) and isnull(invmovdet.deleted_at)
            INNER JOIN sucursal
            on invmov.sucursal_id = sucursal.id and isnull(sucursal.deleted_at)
            INNER JOIN invbodegaproducto
            on invmovdet.invbodegaproducto_id = invbodegaproducto.id and isnull(invbodegaproducto.deleted_at)
            INNER JOIN producto
            on invbodegaproducto.producto_id = producto.id and isnull(producto.deleted_at)
            INNER JOIN invbodega
            on invbodegaproducto.invbodega_id = invbodega.id and isnull(invbodega.deleted_at)
            INNER JOIN categoriaprod
            on categoriaprod.id=producto.categoriaprod_id and isnull(categoriaprod.deleted_at)
            INNER JOIN invmovmodulo
            on invmovmodulo.id=invmov.invmovmodulo_id and isnull(invmovmodulo.deleted_at)
            WHERE $aux_condannomes
            and $aux_condsucursal_id
            and $aux_condFecha
            and $aux_condareaproduccion_id
            and $aux_condproducto_idPxP
            and $aux_condinvbodega_id
            ORDER BY invbodega.orden,invmov.fechahora;";
    //dd($sql);
    $datas = DB::select($sql);
    return $datas;
}