<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Empresa;
use App\Models\NotaVenta;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportGrupoCatPromController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-reporte-precio-promedio-grupo-categoria');
        $fechaAct = date("d/m/Y");
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablas['vendedores'] = Vendedor::selectvendedores();
        $tablas['areaproduccions'] = AreaProduccion::areaproduccionxusuario();
        $arreglo = $tablas['areaproduccions']->map(function ($area) {
            return $area->attributesToArray();
        })->toArray();
        $tablas['sucFisXUsu'] = sucFisXUsu($users->persona);
        return view('reportgrupocatprom.index', compact('tablas'));
    }

    public function reportgrupocatprompage(Request $request){
        $datas = NotaVenta::consultagrupcatprom($request);
        if($request->genexcel == "0"){
            $respuesta = datatables($datas)->toJson();
        }else{
            $respuesta = [];
            $respuesta["datos"] = $datas; //datatables($datas)->toJson();
            $respuesta["fechaact"] = date("d/m/Y");
            $respuesta["fechaacthora"] = date("d/m/Y h:i:s A");
            $respuesta = datatables($respuesta)->toJson();
        }
        return $respuesta;
    }

    public function exportPdf(Request $request)
    {
        $datas = NotaVenta::consultagrupcatprom($request);
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
                return view('reportgrupocatprom.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportdteestadocli.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportgrupocatprom.listado', compact('datas','empresa','usuario','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteStockInv.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    /* public function totalizarindex(Request $request){
        $request->merge(['filtroguiasusadas' => "1"]);
        $datas = Dte::consultadtedet($request);
        $aux_total = 0;
        foreach ($datas as $data) {
            $aux_total += $data->montoitem;
        }
        $respuesta['aux_total'] = $aux_total;
        return $respuesta;
    } */
}
