<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaGrupoValMes;
use App\Models\CategoriaProd;
use App\Models\DespachoSol_InvMov;
use App\Models\Empresa;
use App\Models\InvBodega;
use App\Models\InvBodegaProducto;
use App\Models\InvControl;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class InvBodPesajeaBodProdTermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-traslado-bod-pesaje-a-bod-prodterm');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablashtml['invbodegas'] = InvBodega::orderBy('id')->where("tipo",6)->get();
        $tablashtml['areaproduccions'] = AreaProduccion::orderBy('id')->get();
        $tablashtml['categoriaprod'] = CategoriaProd::categoriasxUsuario();
        $selecmultprod = 1;
        return view('invbodpesajeabodprodterm.index', compact('tablashtml','selecmultprod'));
    }

    public function invbodpesajeabodprodtermpage(Request $request){
        $datas = InvMov::stocksql($request);
        return datatables($datas)->toJson();
/*
        return datatables()
        ->eloquent(InvMov::stock($request))
        ->toJson();
*/
    }

    public function exportPdf(Request $request)
    {
        $datas = InvMov::stock($request);
        $datas = $datas->get();

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());

        $nombreAreaproduccion = "Todos";
        if($request->areaproduccion_id){
            $areaProduccion = AreaProduccion::findOrFail($request->areaproduccion_id);
            $nombreAreaproduccion=$areaProduccion->nombre;
        }
        
        if($datas){
            
            if(env('APP_DEBUG')){
                return view('invbodpesajeabodprodterm.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('invbodpesajeabodprodterm.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('invbodpesajeabodprodterm.listado', compact('datas','empresa','usuario','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteStockInv.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function totalizarindex(Request $request){
        $respuesta = array();
        $datas = InvMov::stock($request)->get();
        $aux_totalkg = 0;
        foreach ($datas as $data) {
            //$aux_totalkg += $data->stockkg;
            $aux_totalkg += $data->stock * $data->peso;
        }
        $respuesta['aux_totalkg'] = $aux_totalkg;
        return $respuesta;
    }

    public function guardar(Request $request)
    {
        //can('guardar-pesaje-carro');
        //dd($request);
        $annomes = CategoriaGrupoValMes::annomes($request->annomes);
        $invcontrolMes = InvControl::where('annomes','=',$annomes)
                ->where('sucursal_id','=',$request->sucursal_id)
                ->where('status','=',1);
        if($invcontrolMes->count() > 0){
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            $anno = substr($annomes, 0, 4);  // Extraer los primeros cuatro caracteres (el año)
            $mes = substr($annomes, 4);     // Extraer los últimos dos caracteres (el mes)
            $mesanno = "$mes/$anno";
            return redirect('invbodpesajeabodprodterm')->with([
                'mensaje'=> "Mes y año $mesanno cerrado no permite movimientos. Sucursal: " . $sucursal->nombre,
                'tipo_alert' => 'alert-error'
            ]);
        }

        $request["mesanno"] = $request->annomes;
        $request["producto_id"] = null;
        $request["tipobodega"] = 6;
        //dd($request);
        $aux_contador = 0;
        $datas = InvMov::stocksql($request);
        foreach ($datas as $data) {
            $requestProd = new Request();
            $requestProd["annomespredef"] = true;
            $requestProd["annomes"] = CategoriaGrupoValMes::annomes($request->annomes);
            $requestProd["data_id"] = $data->invbodegaproducto_id;
            $requestProd["producto_id"] = $data->producto_id;
            $requestProd["invbodega_id"] = $data->invbodega_id;
            $arrayExistencia = InvBodegaProducto::existencia($requestProd);
            $aux_existencia = $arrayExistencia["stock"]["cant"];
            if($aux_existencia > 0){
                $aux_contador++;
            }
        }
        if($aux_contador == 0){
            return redirect('invbodpesajeabodprodterm')->with([
                'mensaje'=> "No hay registros para procesar",
                'tipo_alert' => 'alert-error'
            ]);
        }
        $annomes = CategoriaGrupoValMes::annomes($request->annomes);
        $invmov_array = array();
        $invmov_array["fechahora"] = date("Y-m-d H:i:s");
        $invmov_array["annomes"] = $annomes;
        $invmov_array["desc"] = "Salida de Pesaje entrada Bodega Producto Terminado";
        $invmov_array["obs"] = "Salida de Pesaje entrada Bodega Producto Terminado";
        $invmov_array["invmovmodulo_id"] = 7; //Modulo PESAJE
        $invmov_array["idmovmod"] = null;
        $invmov_array["invmovtipo_id"] = 1;
        $invmov_array["sucursal_id"] = $request->sucursal_id;
        $invmov_array["usuario_id"] = auth()->id();
        $invmov = InvMov::create($invmov_array);       
        foreach ($datas as $data) {
            $requestProd = new Request();
            $requestProd["annomespredef"] = true;
            $requestProd["annomes"] = $annomes;
            $requestProd["data_id"] = $data->invbodegaproducto_id;
            $requestProd["producto_id"] = $data->producto_id;
            $requestProd["invbodega_id"] = $data->invbodega_id;
            $arrayExistencia = InvBodegaProducto::existencia($requestProd);
            $aux_existencia = $arrayExistencia["stock"]["cant"];
            if($aux_existencia > 0){
                $aux_existencia *= -1;
                $producto = Producto::findOrFail($data->producto_id);
                $array_invmovdet = [
                    "invbodegaproducto_id" => $data->invbodegaproducto_id,
                    "producto_id" => $data->producto_id,
                    "invbodega_id" => $data->invbodega_id,
                    "sucursal_id" => $request->sucursal_id,
                    "unidadmedida_id" => $producto->categoriaprod->unidadmedida_id,
                    "invmovtipo_id" => 2,
                    "cant" => $aux_existencia,
                    "cantgrupo" => $aux_existencia,
                    "cantxgrupo" => 1,
                    "peso" => $producto->peso,
                    "cantkg" => $producto->peso * $aux_existencia,
                    "invmov_id" => $invmov->id,
                ];
                $invmovdet = InvMovDet::create($array_invmovdet);
                //BUSCO LA BODEGA DE PRODUCTO TERMINADO DEL PRODUCTO EN CURSO
                $invbodegas=$producto->categoriaprod->invbodegas;
                $bodega_idProdTerm = "";
                foreach ($invbodegas as $invbodega) {
                    if($invbodega->sucursal_id == $request->sucursal_id and $invbodega->tipo == 2){
                        $bodega_idProdTerm =$invbodega->id;
                    }
                }
                if($bodega_idProdTerm != ""){
                    //SI NO EXISTE EN LA TABLA InvBodegaProducto SE CREA
                    $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                        ['producto_id' => $data->producto_id,'invbodega_id' => $bodega_idProdTerm],
                        [
                            'producto_id' => $data->producto_id,
                            'invbodega_id' => $bodega_idProdTerm
                        ]
                    );   
                    $aux_existencia *= -1;
                    $array_invmovdet = [
                        "invbodegaproducto_id" => $invbodegaproducto->id,
                        "producto_id" => $invbodegaproducto->producto_id,
                        "invbodega_id" => $invbodegaproducto->invbodega_id,
                        "sucursal_id" => $request->sucursal_id,
                        "unidadmedida_id" => $producto->categoriaprod->unidadmedida_id,
                        "invmovtipo_id" => 1,
                        "cant" => $aux_existencia,
                        "cantgrupo" => $aux_existencia,
                        "cantxgrupo" => 1,
                        "peso" => $producto->peso,
                        "cantkg" => $producto->peso * $aux_existencia,
                        "invmov_id" => $invmov->id,
                    ];
                    $invmovdet = InvMovDet::create($array_invmovdet);
                }
            }
        }
        return redirect('invbodpesajeabodprodterm')->with([
            'mensaje'=> "Se ejecuto el Traslado a Bodega producto Terminado con exito!",
        ]);
    }

}