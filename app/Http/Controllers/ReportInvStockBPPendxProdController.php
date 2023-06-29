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

class ReportInvStockBPPendxProdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-stock-+-pendiente');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablashtml['invbodegas'] = InvBodega::orderBy('id')
                                    ->where("tipo","=",2)
                                    ->get();
        $tablashtml['areaproduccions'] = AreaProduccion::orderBy('id')->get();
        $tablashtml['categoriaprod'] = CategoriaProd::categoriasxUsuario();
        $selecmultprod = 1;
        return view('reportinvstockbppendxprod.index', compact('tablashtml','selecmultprod'));

    }
    public function reportinvstockbppendxprodpage(Request $request){
        //dd($request);
        can('reporte-stock-+-pendiente');
        $request->request->add(['groupby' => " group by notaventadetalle.producto_id "]);
        $request->request->add(['orderby' => " order by notaventadetalle.producto_id "]);
        $pendientexprods = Producto::pendientexProducto($request,2,1);
        //dd($pendientexprods);
        $arrego_producto_id = [];
        $arrego_pendientexprod = [];
        foreach ($pendientexprods as $pendientexprod) {
            $arrego_producto_id[] = $pendientexprod->producto_id;
            $arrego_pendientexprod[$pendientexprod->producto_id] = $pendientexprod;
        }
        //dd($arrego_pendientexprod);
        $producto_id = implode(",", $arrego_producto_id);
        //$request->request->add(['producto_id' => $producto_id]);
        //dd($producto_id);
        //dd($arrego_pendientexprod);

        $datas = InvMov::stocksql($request,"producto.id");
        foreach ($datas as &$data) {
            if(isset($arrego_pendientexprod[$data->producto_id])){ //SIE EL ELEMENTO EXISTE EL ARREGLO ENTRA.
                $data->cantpend = $arrego_pendientexprod[$data->producto_id]->cant - $arrego_pendientexprod[$data->producto_id]->cantdesp;
                $data->difcantpend = $data->stock - $data->cantpend; //DIFERENCIA ENTRE STOCK Y CANTPEND=CANTIDAD PENDIENTE    
            }else{
                $data->difcantpend = $data->stock;
            }
        }
        //dd($datas);
        return datatables($datas)->toJson();
/*
        return datatables()
        ->eloquent(InvMov::stock($request,"producto.id"))
        ->toJson();*/
    }

    public function exportPdf(Request $request)
    {
        can('reporte-stock-+-pendiente');
        $datas = InvMov::stock($request,"producto.id");
        $request->request->add(['groupby' => " group by notaventadetalle.producto_id "]);
        $request->request->add(['orderby' => " order by notaventadetalle.producto_id "]);
        $pendientexprods = Producto::pendientexProducto($request,2,1);
        //dd($pendientexprods);
        $arrego_producto_id = [];
        $arrego_pendientexprod = [];
        foreach ($pendientexprods as $pendientexprod) {
            $arrego_producto_id[] = $pendientexprod->producto_id;
            $arrego_pendientexprod[$pendientexprod->producto_id] = $pendientexprod;
        }
        //dd($arrego_pendientexprod);
        $producto_id = implode(",", $arrego_producto_id);
        //$request->request->add(['producto_id' => $producto_id]);
        //dd($producto_id);

        $datas = InvMov::stocksql($request,"producto.id");
        foreach ($datas as &$data) {
            if(isset($arrego_pendientexprod[$data->producto_id])){ //SIE EL ELEMENTO EXISTE EL ARREGLO ENTRA.
                $data->cantpend = $arrego_pendientexprod[$data->producto_id]->cant - $arrego_pendientexprod[$data->producto_id]->cantdesp;
                $data->difcantpend = $data->stock - $data->cantpend; //DIFERENCIA ENTRE STOCK Y CANTPEND=CANTIDAD PENDIENTE                
            }else{
                $data->difcantpend = $data->stock;
            }
        }
        //dd($datas);

        //$datas = $datas->get();

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());

        $nombreAreaproduccion = "Todos";
        if($request->areaproduccion_id){
            $areaProduccion = AreaProduccion::findOrFail($request->areaproduccion_id);
            $nombreAreaproduccion=$areaProduccion->nombre;
        }

        if($datas){
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            $request->request->add(['sucursal_nombre' => $sucursal->nombre]);
            if(env('APP_DEBUG')){
                return view('reportinvstockbppendxprod.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportinvstockbppendxprod.listado', compact('datas','empresa','usuario','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteStockBodegaPicking.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function totalizarindex(Request $request){
        $respuesta = array();
        $datas = InvMov::stock($request,"producto.id")->get();
        $aux_totalkg = 0;
        foreach ($datas as $data) {
            //$aux_totalkg += $data->stockkg;
            $aux_totalkg += $data->stock * $data->peso;
        }
        $respuesta['aux_totalkg'] = $aux_totalkg;
        return $respuesta;
    }
     
}
