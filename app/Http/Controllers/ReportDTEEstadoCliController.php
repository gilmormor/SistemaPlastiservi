<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Comuna;
use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\InvMov;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportDTEEstadoCliController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-estado-cliente');
        $fechaAct = date("d/m/Y");
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        return view('reportdteestadocli.index', compact('tablas'));
    }

    public function reportdteestadoclipage(Request $request){
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
        return datatables($datas)->toJson();
    }

    public function exportPdf(Request $request)
    {
        $request->merge(['foliocontrol_id' => "(1,5,6,7)"]);
        $request->merge(['orderby' => " cliente.rut asc "]);
        $request->merge(['groupby' => ""]);
        
        $datas = Dte::reportestadocli($request);
        dd($datas);

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());

        $nombreAreaproduccion = "Todos";
        if($request->areaproduccion_id){
            $areaProduccion = AreaProduccion::findOrFail($request->areaproduccion_id);
            $nombreAreaproduccion=$areaProduccion->nombre;
        }
        
        if($datas){
            
            if(env('APP_DEBUG')){
                return view('reportdteestadocli.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportdteestadocli.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportdteestadocli.listado', compact('datas','empresa','usuario','request'));
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
        $datas = Dte::totalreportestadocli($request);
        $aux_total = 0;
        foreach ($datas as $data) {
            $aux_total += $data->mnttotal;
        }
        $respuesta['aux_total'] = $aux_total;
        return $respuesta;
    }

    
}
