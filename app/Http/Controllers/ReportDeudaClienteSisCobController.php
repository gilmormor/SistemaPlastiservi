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
        can('listar-reporte-deuda-cliente-sistema-cobranza');

        $tablas['giros'] = Giro::orderBy('id')->get();
        $tablas['fechaAct'] = date("d/m/Y");
        $tablas['comunas'] = Comuna::selectcomunas();
        $tablas['vendedores'] = Vendedor::selectvendedores();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        return view('reportdeudaclientesiscob.index', compact('tablas'));
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
        //dd($request);
        $clientes = Cliente::clientesxUsuarioMejorado($request);
        $aux_clientes = [];
        //dd($clientes);
        foreach ($clientes as &$cliente) {
            $data = Cliente::findOrFail($cliente->id);
            //dd($data);
            $aux_consultadeuda = 1;
            $datacobranza = clienteBloqueado($cliente->id,$aux_consultadeuda);
            //dd($datacobranza);
            $data->datacobranza = $datacobranza["datacobranza"];
            if(count($data->datacobranza) > 0){
                //dd($data->datacobranza["datosFacDeuda"][0]->nombrepdf);
                $foliocontrol = Foliocontrol::findOrFail(1); //1 en el registro que contiene al folio Facturacion
                $data->nombrepdf = $foliocontrol->nombrepdf;
            }
            //dd($data);
            //$datas = Cliente::clientesxUsuarioSQL();
            $cliente->datos = $data;
            $cliente->fechaact = date("d/m/Y");
            $cliente->fechaacthora = date("d/m/Y h:i:s A");
            if($request->statusDeuda == "0"){
                $aux_clientes[] = $cliente;
            }
            //dd($cliente);
            if($request->statusDeuda == "1" and $data->datacobranza["TDeudaFec"] > 0){
                $aux_clientes[] = $cliente;
            }
            if($request->statusDeuda == "2" and $data->datacobranza["TDeudaFec"] <= 0){
                $aux_clientes[] = $cliente;
            }
        }
        //dd($cliente);
        if($request->GenExcel == 0){
            $respuesta = $aux_clientes;
        }else{
            $respuesta = [
                "fechaact" => date("d/m/Y"),
                "fechaacthora" => date("d/m/Y h:i:s A"),
                "clientes" => $aux_clientes
            ];
    
        }
        dd($respuesta);
        return $respuesta;
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
