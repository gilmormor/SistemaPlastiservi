<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CentroEconomico;
use App\Models\Comuna;
use App\Models\Ot;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\Vendedor;
use Illuminate\Http\Request;

class ReportOtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-reporte-ot');

        $areaproduccions =  AreaProduccion::areaproduccionxusuario();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablashtml['centroeconomicos'] = CentroEconomico::orderBy('id')->get();
        return view('reportot.index', compact('areaproduccions','fechaAct','tablashtml'));
    }

    public function reportotpage(Request $request){
        //dd($request);
        //can('reporte-guia_despacho');
        //dd('entro');
        //$datas = GuiaDesp::reporteguiadesp($request);
        //dd($request);
        $datas = Ot::reportot($request);
        if($request->GenExcel == 0){
            return datatables($datas)->toJson();
        }else{
            $respuesta = [
                "fechaact" => date("d/m/Y"),
                "fechaactaaaammdd" => date("Y/m/d"),
                "fechaactaaaammdd2" => date("Y-m-d"),
                "fechaacthora" => date("d/m/Y h:i:s A"),
                "respuesta" => $datas
            ];
            //dd($respuesta);
            return $respuesta;    
        }
        //return datatables($datas)->toJson();

    }

}
