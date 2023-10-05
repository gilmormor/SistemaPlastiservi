<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Comuna;
use App\Models\Dte;
use App\Models\DteGuiaUsada;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class DteGuiaDespUsadaLiberarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        can('listar-dte-liberar-guia-usada-refacturar');

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
        return view('dteguiadespusadaliberar.index', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }

    public function dteguiadespusadaliberarpage(Request $request){
        //dd($request->dteguiausada);
        //can('reporte-guia_despacho');
        //dd('entro');
        //$datas = GuiaDesp::reporteguiadesp($request);
        $datas = Dte::reportguiadesppage($request);
        return datatables($datas)->toJson();
    }

    public function exportPdf(Request $request)
    {
        $datas = Dte::reportguiadesppage($request);
        //dd($request);
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
                return view('dteguiadespusadaliberar.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            //$pdf = PDF::loadView('dteguiadespusadaliberar.listado', compact('datas','empresa','usuario','request'));
            $pdf = PDF::loadView('dteguiadespusadaliberar.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');

            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("dteguiadespusadaliberar.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function liberarguiadesp(Request $request)
    {
        can('guardar-dte-liberar-guia-usada-refacturar');
        $dte = Dte::findOrFail($request->dte_id);
        if($request->dteupdated_at != $dte->updated_at){
            return response()->json([
                'id' => 0,
                'mensaje'=>'Registro no puede editado, fué modificado por otro usuario.',
                'tipo_alert' => 'error'
            ]);
        }
        if($request->dteguiausada_id != $dte->dteguiausada->id and $request->dteguiausadaupdated_at != $dte->dteguiausada->updated_at){
            return response()->json([
                'id' => 0,
                'mensaje'=>'Registro no puede editado, fué modificado por otro usuario. Guia Usada no corresponde a la seleccionada',
                'tipo_alert' => 'error'
            ]);
        }

        $dte->updated_at = date("Y-m-d H:i:s");

        //dd($dte->dteguiausada);
        $dteguiausada = DteGuiaUsada::find($request->dteguiausada_id);
        //dd(auth()->id());
        if ($dteguiausada) {
            $dteguiausada->usuariodel_id = auth()->id();
            //dd($dteguiausada->usuariodel_id);
            if($dteguiausada->save() and $dteguiausada->delete()){
                $dte->save();
                return response()->json([
                    'id' => 1,
                    'mensaje'=>'Registro procesado con exito.',
                    'tipo_alert' => 'success'
                ]);    
            } // Elimina el registro
        }
    }
}
