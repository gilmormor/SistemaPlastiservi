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

class ReportInvStockVendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-stock-inventario-vendedores');
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
        return view('reportinvstockvend.index', compact('tablashtml','selecmultprod'));

    }
    public function reportinvstockvendpage(Request $request){
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
        /* $datas = InvMov::stock($request);
        $datas = $datas->get(); */
        $datas = InvMov::stocksql($request);

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
                return view('reportinvstockvend.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteStockInv.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }
    
}
