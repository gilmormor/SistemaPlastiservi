<?php

namespace App\Http\Controllers;

use App\Models\Ot;
use Illuminate\Http\Request;

class OtAprobarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-aprobar-ot');
        //$datas = FormaPago::orderBy('id')->get();
        return view('otaprobar.index');
    }

    public function otaprobarpage(Request $request){
        $request->request->set('aux_estado', 2);
        $request->request->set('noanul', 1);
        $request->request->set('modulo_id', 33);
        //dd($request);
        $datas = Ot::reportot($request);
        return datatables($datas)->toJson();
    }
    
    public function aprobar(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            $ot = Ot::findOrFail($request->id);
            if(strtotime($ot->updated_at) != $request->updatednum_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro no pudo ser Actualizado. Registro Editado por otro usuario. Fecha Hora: '.$ot->updated_at,
                    'tipo_alert' => 'error'
                ]);    
            }

            $request1 = new Request();
            $request1->merge(['modulo_id' => 33]);
            $request1->request->set('modulo_id', 33);
            $request1->merge(['deldesbloqueo' => 0]);
            $request1->request->set('deldesbloqueo', 0);
            if(isset($ot->otnotaventa)){
                $request1->merge(['notaventa_id' => $ot->otnotaventa->notaventa_id]);
                $request1->request->set('notaventa_id', $ot->otnotaventa->notaventa_id);
            }
            $clibloq = clienteBloqueado($ot->cliente_id,0,$request1);
            if(!is_null($clibloq["bloqueo"])){
                return redirect('ot')->with([
                    "mensaje" => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                    "tipo_alert" => "alert-error"
                ]);
            }
            $request1->merge(['deldesbloqueo' => 1]);
            $request1->request->set('deldesbloqueo', 1);
            $clibloq = clienteBloqueado($ot->cliente_id,0,$request1);

            //dd($request);

            $ot->aprobstatus = 2;
            $ot->aprobusu_id = auth()->id();
            $ot->aprobfechahora = date("Y-m-d H:i:s");
            $ot->updated_at = date("Y-m-d H:i:s");
            if($ot->save()){
                return response()->json([
                                            'mensaje' => 'Registro guardo con exito.',
                                            'status' => '0',
                                            'id' => $ot->id,
                                            'nfila' => $ot->id,
                                            'dte_id' => $ot->id,
                                        ]);
            } else {
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Ocurrio un error al intenta guardar.",
                    'tipo_alert' => 'error'
                ]);
            }

            
        }
    }

    public function rechazar(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            $ot = Ot::findOrFail($request->id);
            if(strtotime($ot->updated_at) != $request->updatednum_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro no pudo ser Actualizado. Registro Editado por otro usuario. Fecha Hora: '.$ot->updated_at,
                    'tipo_alert' => 'error'
                ]);    
            }
            if(!isset($request->obs) or $request->obs == "" or $request->obs == null){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'El campo observacion es obligatorio.',
                    'tipo_alert' => 'error'
                ]);    
            }

            //dd($request);

            $request1 = new Request();
            $request1->merge(['modulo_id' => 33]);
            $request1->request->set('modulo_id', 33);
            $request1->merge(['deldesbloqueo' => 0]);
            $request1->request->set('deldesbloqueo', 0);
            $clibloq = clienteBloqueado($ot->cliente_id,0,$request1);
            if(!is_null($clibloq["bloqueo"])){
                return redirect('ot')->with([
                    "mensaje" => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                    "tipo_alert" => "alert-error"
                ]);
            }
            $request1->merge(['deldesbloqueo' => 1]);
            $request1->request->set('deldesbloqueo', 1);
            $clibloq = clienteBloqueado($ot->cliente_id,0,$request1);

            //dd($request);

            $ot->aprobstatus = 3;
            $ot->obsrechazo = $request->obs;
            $ot->aprobusu_id = auth()->id();
            $ot->aprobfechahora = date("Y-m-d H:i:s");
            $ot->updated_at = date("Y-m-d H:i:s");
            if($ot->save()){
                return response()->json([
                                            'mensaje' => 'Registro guardo con exito.',
                                            'status' => '0',
                                            'id' => $ot->id,
                                            'nfila' => $ot->id,
                                            'dte_id' => $ot->id,
                                        ]);
            } else {
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Ocurrio un error al intenta guardar.",
                    'tipo_alert' => 'error'
                ]);
            }

            
        }
    }
}
