<?php

namespace App\Http\Controllers;

use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportDTELibroVentasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-reporte-dte-libro-ventas');
        $fechaAct = date("d/m/Y");
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablas['fecha1erDiaMes'] = date("01/m/Y");
        return view('reportdtelibroventas.index', compact('tablas'));
    }

    public function reportdtelibroventaspage(Request $request){
        //can('reporte-guia_despacho');
        //dd('entro');
        //$datas = GuiaDesp::reporteguiadesp($request);
        //$request->foliocontrol_id = "(1,5,6,7)";
        //$request->request->add(['foliocontrol_id' => "(1,5,6,7)"]);
        //$request->request["foliocontrol_id"] = "(1,5,6,7)";
        $request->merge(['foliocontrol_id' => "(1,5,6,7)"]);
        $request->merge(['orderby' => " order by dte.id desc "]);
        $request->merge(['groupby' => " group by dte.id "]);
        //dd($request->request);
        $datas = Dte::reportestadocli($request);
        if($request->genexcel == 1){
            $respuesta = [];
            $respuesta["datos"] = $datas;
            $respuesta["fechaact"] = date("d/m/Y");    
        }else{
            $respuesta = datatables($datas)->toJson();
        }
        return $respuesta;
    }

    public function exportPdf(Request $request)
    {
        $request->merge(['foliocontrol_id' => "(1,5,6,7)"]);
        $request->merge(['orderby' => "  order by dte.fchemisgen "]);
        $request->merge(['groupby' => " group by dte.id "]);
        $datas = Dte::reportestadocli($request);
        //dd($datas[0]);

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
                return view('reportdtelibroventas.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportdtelibroventas.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportdtelibroventas.listado', compact('datas','empresa','usuario','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("libroventas.pdf");
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
