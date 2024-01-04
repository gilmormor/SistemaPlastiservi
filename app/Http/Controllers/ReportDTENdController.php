<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Comuna;
use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportDTENdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-nota-debito-reporte');

        $giros = Giro::orderBy('id')->get();
        $areaproduccions =  AreaProduccion::areaproduccionxusuario();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        return view('reportdtend.index', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }
    
    public function reportdtendpage(Request $request){
        //can('reporte-guia_despacho');
        //dd('entro');
        //$datas = GuiaDesp::reporteguiadesp($request);
        $request->request->add(['foliocontrol_id' => 6]);
        $datas = Dte::reportdtencnd($request);
        return datatables($datas)->toJson();
    }
    public function exportPdf(Request $request)
    {
        $request->request->add(['foliocontrol_id' => 6]);
        $datas = Dte::reportdtencnd($request);
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
        switch ($request->aprobstatus) {
            case 0:
                $aux_status = "Todos";
                break;
            case 1:
                $aux_status = "Activas";
                break;    
            case 2:
                $aux_status = "Anuladas";
                break;
        }
        $request->merge(['status' => $aux_status]);
        if($datas){
            
            if(env('APP_DEBUG')){
                return view('reportdtend.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportdtend.listado', compact('datas','empresa','usuario','request'));
            //$pdf = PDF::loadView('reportdtend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');

            return $pdf->stream("reportdtend.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }
}
