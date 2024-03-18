<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\Comuna;
use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Foliocontrol;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportDeudaClienteSisCobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-factura-reporte');

        $tablashtml['giros'] = Giro::orderBy('id')->get();
        $tablashtml['fechaAct'] = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        return view('reportdeudaclientesiscob.index', compact('tablashtml'));
    }

    public function reportdeudaclientesiscobpage(Request $request){
        $datas = Cliente::clientesxUsuarioSQL();
        /* foreach ($datas as &$data) {
            $data->datacobranza = clienteBloqueado($data->id);
        } */
        //dd($data);
        return datatables($datas)->toJson();
    }

    public function consulta(Request $request){
        $data = Cliente::findOrFail($request->id);
        //dd($data);
        $aux_consultadeuda = 1;
        $datacobranza = clienteBloqueado($request->id,$aux_consultadeuda);
        //dd($datacobranza);
        $data->datacobranza = $datacobranza["datacobranza"];
        if(count($data->datacobranza) > 0){
            //dd($data->datacobranza["datosFacDeuda"][0]->nombrepdf);
            $foliocontrol = Foliocontrol::findOrFail(1); //1 en el registro que contiene al folio Facturacion
            $data->nombrepdf = $foliocontrol->nombrepdf;
        }
        //dd($data);
        //$datas = Cliente::clientesxUsuarioSQL();
        return $data;
    }

    public function listardtedet(Request $request){
        //$request->merge(['filtroguiasusadas' => "1"]);
        $datas = Dte::consultadtedet($request);
        $respuesta["datos"] = $datas;
        $respuesta["fechaact"] = date("d/m/Y"); 
        return $respuesta;
    }


    public function exportPdf(Request $request)
    {
        $datas = Dte::reportdtefac($request);
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
            
            if(env('APP_DEBUG')){
                return view('reportdtefac.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'));
            //$pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');

            return $pdf->stream("reportdtefac.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }
}
