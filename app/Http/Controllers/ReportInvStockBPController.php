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

class ReportInvStockBPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-stock-bodegapicking');
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
        return view('reportinvstockbp.index', compact('tablashtml','selecmultprod'));

    }
    public function reportinvstockbppage(Request $request){
        can('reporte-stock-bodegapicking');
        $datas = InvMov::stocksql($request,"producto.id");
        return datatables($datas)->toJson();
/*
        return datatables()
        ->eloquent(InvMov::stock($request,"producto.id"))
        ->toJson();*/
    }

    public function exportPdf(Request $request)
    {
        can('reporte-stock-bodegapicking');
        $datas = InvMov::stock($request,"producto.id");
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
                return view('reportinvstockbp.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportinvstockbp.listado', compact('datas','empresa','usuario','request'));
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
