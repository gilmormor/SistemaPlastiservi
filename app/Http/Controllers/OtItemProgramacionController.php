<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use App\Models\MateriaPrima;
use App\Models\Op;
use App\Models\OpDet;
use App\Models\Ot;
use App\Models\OtDet;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class OtItemProgramacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-programacion-item-ot');
        //$datas = FormaPago::orderBy('id')->get();
        $tablas['materiaprimas'] = MateriaPrima::orderBy('id')->get();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablas['maquinas'] = Maquina::whereIn('sucursal_id', $sucurArray)
            ->orderBy('id')
            ->select('id', 'nombre','maquinagrupo_id')
            ->get()
            ->toJson();
        //dd($tablas['maquinas']);
        $selecmultprod = 1;
        return view('otitemprogramacion.index',compact('tablas','selecmultprod'));
    }

    public function otitemprogramacionpage(Request $request){
        $request->request->set('aux_estado', 3);
        $request->request->set('noanul', 1);
        $request->request->set('modulo_id', 35);
        //$request->merge(['sta_envprog' => 1]);
        $request->request->set('sta_envprog', 1);
        
        //dd($request);
        $datas = Ot::reportotitem($request);
        return datatables($datas)->toJson();
    }

    public function aprobar(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            $otdet = OtDet::findOrFail($request->id);
            $ot = Ot::findOrFail($otdet->ot_id);
            //dd($request->updatednum_at);
            /* dd(strtotime($otdet->updated_at));
            dd($otdet); */
            if(strtotime($ot->updated_at) != $request->otupdatednum_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>"Encabezado de registro modificado por otro usuario. \nFecha: " . date('d-m-Y h:i:s A', strtotime($ot->updated_at)),
                    'tipo_alert' => 'error'
                ]);    
            }

            if(strtotime($otdet->updated_at) != $request->updatednum_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>"Detalle de registro modificado por otro usuario. \nFecha: " . date('d-m-Y h:i:s A', strtotime($otdet->updated_at)),
                    'tipo_alert' => 'error'
                ]);    
            }
            //dd($request);
            $request1 = new Request();
            $request1->merge(['modulo_id' => 35]);
            $request1->request->set('modulo_id', 35);
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

            $op = new Op();
            $op->ot_id = $ot->id;
            $op->cantprod = $otdet->cantprod;
            $op->kgprod = $request->kgprod;
            $op->ordenaten = $request->ordenaten;
            $op->obs = $request->obs;
            $op->usuario_id = auth()->id();
            if($op->save()){
                $opdets[] = new OpDet([
                    'op_id' => $op->id, 
                    'otdet_id' => $request->id,
                    'obs' => $request->obsext,
                    'maquina_id' => $request->extrusora
                ]);
                if($request->impresora != "X"){
                    $opdets[] = new OpDet([
                        'op_id' => $op->id, 
                        'otdet_id' => $request->id,
                        'obs' => $request->obsimp,
                        'maquina_id' => $request->impresora
                    ]);
                }
                if($request->selladora != "X"){
                    $opdets[] = new OpDet([
                        'op_id' => $op->id, 
                        'otdet_id' => $request->id,
                        'obs' => $request->obssell,
                        'maquina_id' => $request->selladora
                    ]);
                }
                $aux_save = $op->opdets()->saveMany($opdets);
                if($aux_save){
                    return response()->json([
                        'mensaje' => 'Registro guardo con exito.',
                        'status' => '0',
                        'id' => $request->id,
                        'nfila' => $request->id,
                        'dte_id' => $request->id,
                    ]);
                }else{
                    return response()->json([
                        'id' => 0,
                        'mensaje' => "Ocurrio un error al intenta guardar.",
                        'tipo_alert' => 'error'
                    ]);
                }
            }else{
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Ocurrio un error al intenta guardar.",
                    'tipo_alert' => 'error'
                ]);
            }
/* cantprod


 */
            $opdet->maquina_id = $request->maquina_id;

            $op->save();
            $otdet->sta_envprog = 1;
            $otdet->espesorprod = $request->espesorprod;
            $otdet->kgprod = $request->kgprod;
            $otdet->cantprod = $request->cantprod;
            $otdet->envprogusu_id = auth()->id();
            $otdet->envprogfecha = date("Y-m-d H:i:s");
            /* $otdet->updated_at = date("Y-m-d H:i:s"); */
            $ot->updated_at = date("Y-m-d H:i:s");
            if($ot->save() and $otdet->save()){
                return response()->json([
                                            'mensaje' => 'Registro guardo con exito.',
                                            'status' => '0',
                                            'id' => $otdet->id,
                                            'nfila' => $otdet->id,
                                            'dte_id' => $otdet->id,
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
