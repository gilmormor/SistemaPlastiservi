<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Comuna;
use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\GuiaDesp;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportDTEGuiaDespController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-guia-despacho-reporte');

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
                                    ->whereIn('sucursal.id', $sucurArray)
                                    ->get();
        return view('reportdteguiadesp.index', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }

    public function reportdteguiadesppage(Request $request){
        //can('reporte-guia_despacho');
        //dd('entro');
        //$datas = GuiaDesp::reporteguiadesp($request);
        $datas = Dte::reportguiadesppage($request);
        return datatables($datas)->toJson();
    }

    public function listardtedet(Request $request){
        $request->merge(['filtroguiasusadas' => "1"]);
        $datas = Dte::consultadtedet($request);
        $respuesta["datos"] = $datas;
        $respuesta["fechaact"] = date("d/m/Y"); 
        return $respuesta;
    }

    public function exportPdf(Request $request)
    {
        //dd($request);
        $datas = Dte::reportguiadesppage($request);
        //dd($datas);

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
            if(isset($request->mostrarkg) and $request->mostrarkg == "1"){
                if(env('APP_DEBUG')){
                    return view('reportdteguiadesp.listadokg', compact('datas','empresa','usuario','request'));
                }                
                $pdf = PDF::loadView('reportdteguiadesp.listadokg', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
                return $pdf->stream("reportdteguiadesp.pdf");    
            }else{
                if(env('APP_DEBUG')){
                    return view('reportdteguiadesp.listado', compact('datas','empresa','usuario','request'));
                }
                
                //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
                
                //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
                //$pdf = PDF::loadView('reportdteguiadesp.listado', compact('datas','empresa','usuario','request'));
                $pdf = PDF::loadView('reportdteguiadesp.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
    
                //return $pdf->download('cotizacion.pdf');
                //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
                return $pdf->stream("reportdteguiadesp.pdf");    
            }
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }
    
}
