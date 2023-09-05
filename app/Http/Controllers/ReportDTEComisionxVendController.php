<?php

namespace App\Http\Controllers;

use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportDTEComisionxVendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-comision-x-vendedor');
        $fechaAct = date("d/m/Y");
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablas['vendedores'] = Vendedor::selectvendedores();
        return view('reportdtecomisionxvend.index', compact('tablas'));
    }

    public function reportdtecomisionxvendpage(Request $request){
        $request->merge(['foliocontrol_id' => "(1,5,6,7)"]);
        if(is_null($request->orderby) or $request->orderby == "" or !isset($request->orderby)){
            $request->merge(['orderby' => " order by foliocontrol.doc,dte.id desc "]);
        }
        $request->merge(['groupby' => " group by dtedet.id "]);
        $request->merge(['fechahoy' => date("d/m/Y")]);
        $datas = Dte::reportcomisionxvend($request);
        dd($datas);
        return datatables($datas)->toJson();
    }

    public function exportPdf(Request $request)
    {
        $request->merge(['foliocontrol_id' => "(1,5,6,7)"]);
        $request->merge(['orderby' => " order by dte.vendedor_id asc,foliocontrol.doc,dte.id "]);
        $request->merge(['groupby' => " group by dtedet.id "]);
        $datas = Dte::reportcomisionxvend($request);
        //dd($datas[0]);

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $request->merge(['sucursal_nombre' => "Todos"]);
        }else{
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            $aux_sucursalNombre = $sucursal->nombre;
            $request->merge(['sucursal_nombre' => $sucursal->nombre]);
        }

        if($datas){
            
            if(env('APP_DEBUG')){
                return view('reportdtecomisionxvend.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportdteestadocli.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportdtecomisionxvend.listado', compact('datas','empresa','usuario','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteStockInv.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function totalizarindex(Request $request){
        $request->merge(['foliocontrol_id' => "(1,5,6,7)"]);
        $respuesta = array();
        $request->merge(['orderby' => " order by dte.id desc "]);
        $request->merge(['groupby' => " group by dtedet.id "]);
        $datas = Dte::reportcomisionxvend($request);
        $aux_total = 0;
        $aux_totalcomision = 0;
        foreach ($datas as $data) {
            $aux_total += $data->montoitem;
            $aux_totalcomision += $data->comision;
        }
        $respuesta['aux_total'] = $aux_total;
        $respuesta['aux_totalcomision'] = $aux_totalcomision;
        return $respuesta;
    }
}
