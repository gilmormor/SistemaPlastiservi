<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Empresa;
use App\Models\InvBodega;
use App\Models\InvMov;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportInvStockBPPendxProdVendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-stock-+-pendiente-vend');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablashtml['invbodegas'] = InvBodega::orderBy('id')
                                    ->where("tipo","=",2)
                                    ->get();
        $tablashtml['areaproduccions'] =  AreaProduccion::areaproduccionxusuario();
        $tablashtml['categoriaprod'] = CategoriaProd::categoriasxUsuario();
        $selecmultprod = 1;
        return view('reportinvstockbppendxprodvend.index', compact('tablashtml','selecmultprod'));

    }

    public function exportPdf(Request $request)
    {
        can('reporte-stock-+-pendiente-vend');
        //dd($request->aux_orden);
        $request->request->add(['groupby' => " group by notaventadetalle.producto_id "]);
        $request->request->add(['orderby' => " order by notaventadetalle.producto_id "]);
        $request->request->add(['FiltarxVendedor' => false]);
        $pendientexprods = Producto::pendientexProducto($request,2,1);
        //dd($pendientexprods);
        $datas1 = [];
        $datas = InvMov::stocksql($request,"producto.id");
        $InvStocks = [];
        foreach ($datas as $data) {
            $InvStocks[$data->producto_id] = $data;
        }

        $arrego_producto_id = [];
        foreach ($pendientexprods as &$pendientexprod) {
            $arrego_producto_id[] = $pendientexprod->producto_id;
            $pendientexprod->largo = $pendientexprod->long;
            //$producto = Producto::findOrFail($pendientexprod->producto_id);
            //$pendientexprod->producto_nombre = $pendientexprod->nombre;
            //$pendientexprod->categoria_nombre = $producto->categoriaprod->nombre;
            $pendientexprod->stock = 0;
            $pendientexprod->stockBodProdTerm = 0;
            $pendientexprod->stockPiking = 0;
            $pendientexprod->stockkg = 0;
            $pendientexprod->cantpend = $pendientexprod->cant - $pendientexprod->cantdesp;
            $pendientexprod->difcantpend = $pendientexprod->cantpend * -1;
            $pendientexprod->acuerdotecnico_id = null;
            $pendientexprod->at_ancho = null;
            $pendientexprod->at_largo = null;
            $pendientexprod->at_espesor = null;

            if(isset($InvStocks[$pendientexprod->producto_id])){
                $pendientexprod->stock = $InvStocks[$pendientexprod->producto_id]->stock;
                $pendientexprod->stockPiking = $InvStocks[$pendientexprod->producto_id]->stockPiking;
                $pendientexprod->difcantpend = $pendientexprod->stock - $pendientexprod->cantpend;
            }
            $datas1[$pendientexprod->producto_id] = $pendientexprod;

            //$arrego_pendientexprod[$pendientexprod->producto_id] = $pendientexprod;
        }
        foreach ($InvStocks as &$InvStock) {
            if(!isset($datas1[$InvStock->producto_id])){
                $InvStock->difcantpend = $InvStock->stock;
                $datas1[$InvStock->producto_id] = $InvStock;
            }
        }

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());

        $nombreAreaproduccion = "Todos";
        if($request->areaproduccion_id){
            $areaProduccion = AreaProduccion::findOrFail($request->areaproduccion_id);
            $nombreAreaproduccion=$areaProduccion->nombre;
        }
        $datas = $datas1;
        if($datas){
            //usort($datas, 'compararProductoId');
            $arrayOrden = explode(',', $request->aux_orden);    
            usort($datas, ordenarArrayxCampo($arrayOrden[0], $arrayOrden[1]));
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            $request->request->add(['sucursal_nombre' => $sucursal->nombre]);
            if(env('APP_DEBUG')){
                return view('reportinvstockbppendxprodvend.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportinvstockbppendxprodvend.listado', compact('datas','empresa','usuario','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteStockBodegaPicking.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function totalizarindex(Request $request){
        $respuesta = array();
        //$datas = InvMov::stock($request,"producto.id")->get();
        $datas = InvMov::stocksql($request,"producto.id");
        $aux_totalkg = 0;
        foreach ($datas as $data) {
            //$aux_totalkg += $data->stockkg;
            $aux_totalkg += $data->stock * $data->peso;
        }
        $respuesta['aux_totalkg'] = $aux_totalkg;
        return $respuesta;
    }
}