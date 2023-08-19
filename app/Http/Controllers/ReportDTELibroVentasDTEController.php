<?php

namespace App\Http\Controllers;

use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportDTELibroVentasDTEController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-reporte-dte-libro-ventas-dte');
        $fechaAct = date("d/m/Y");
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        return view('reportdtelibroventasdte.index', compact('tablas'));
    }

    public function reportdtelibroventasdtepage(Request $request){
        //can('reporte-guia_despacho');
        //dd('entro');
        //$datas = GuiaDesp::reporteguiadesp($request);
        //$request->foliocontrol_id = "(1,5,6,7)";
        //$request->request->add(['foliocontrol_id' => "(1,5,6,7)"]);
        //$request->request["foliocontrol_id"] = "(1,5,6,7)";
        $request->merge(['foliocontrol_id' => "(1,5,6,7)"]);
        $request->merge(['orderby' => " order by dte.id,dte.sucursal_id desc "]);
        $request->merge(['groupby' => " group by dte.id "]);
        //dd($request->request);
        $datas = Dte::reportestadocli($request);
        return datatables($datas)->toJson();
    }

    public function exportPdf(Request $request)
    {
        $request->merge(['foliocontrol_id' => "(1,5,6,7)"]);
        $request->merge(['orderby' => "  order by dte.fchemisgen "]);
        $request->merge(['groupby' => " group by dte.id "]);
        $datas = Dte::reportestadocli($request);
        //dd($datas[0]);
        //dd('entro');

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $request->merge(['sucursal_nombre' => "Todas"]);
        }else{
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            $aux_sucursalNombre = $sucursal->nombre;
            $request->merge(['sucursal_nombre' => $sucursal->nombre]);
        }
        if($datas){
            
            if(env('APP_DEBUG')){
                return view('reportdtelibroventasdte.listado', compact('datas','empresa','usuario','request'));
            }
            
            //dd($datas);

            $pdf = PDF::loadView('reportdtelibroventasdte.listado', compact('datas','empresa','usuario','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("libroventasdte.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function totalizarindex(Request $request){
        $request->merge(['foliocontrol_id' => "(1,5,6,7)"]);
        $respuesta = array();
        $datas = Dte::totalreportestadocli($request);
        $aux_total = 0;
        foreach ($datas as $data) {
            $aux_total += $data->mnttotal;
        }
        $respuesta['aux_total'] = $aux_total;
        return $respuesta;
    }
    
}
