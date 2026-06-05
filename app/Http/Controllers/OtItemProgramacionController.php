<?php

namespace App\Http\Controllers;

use App\Http\Requests\OpStoreRequest;
use App\Models\Maquina;
use App\Models\MateriaPrima;
use App\Models\Op;
use App\Models\OpDet;
use App\Models\OpDetCerr;
use App\Models\OpDetMaquina;
use App\Models\Ot;
use App\Models\OtDet;
use App\Models\OtDetCerr;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\OpDetService;

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
        /* $tablas['maquinas'] = Maquina::whereIn('sucursal_id', $sucurArray)
            ->orderBy('id')
            ->select('id', 'nombre','maquinagrupo_id')
            ->get()
            ->toJson(); */
        $tablas['maquinas'] = Maquina::whereIn('maquina.sucursal_id', $sucurArray)
                ->join("maquinaetapaprod","maquina.id","=","maquinaetapaprod.maquina_id")
                ->join("etapaprod","maquinaetapaprod.etapaprod_id","=","etapaprod.id")
                ->orderBy('maquina.id')
                ->select(
                    'maquina.id', 
                    'maquina.nombre',
                    'maquina.maquinagrupo_id',
                    'etapaprod.nombre as etapaprod_nombre',
                    'etapaprod.id as etapaprod_id'
                )
            ->get()
            ->toJson();
        //dd($tablas['maquinas']);
        $selecmultprod = 1;
        $tablas['editarEtapasProd'] = can('editar-etapa-de-produccion',false);
        return view('otitemprogramacion.index',compact('tablas','selecmultprod'));
    }

    public function otitemprogramacionpage(Request $request){
        $request->request->set('aux_estado', 3);
        $request->request->set('noanul', 1);
        $request->request->set('modulo_id', 35);
        //$request->merge(['sta_envprog' => 1]);
        $request->request->set('sta_envprog', '1,2');

        /* $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['maquinas'] = Maquina::whereIn('maquina.sucursal_id', $sucurArray)
                ->join("maquinaetapaprod","maquina.id","=","maquinaetapaprod.maquina_id")
                ->join("etapaprod","maquinaetapaprod.etapaprod_id","=","etapaprod.id")
                ->orderBy('maquina.id')
                ->select(
                    'maquina.id', 
                    'maquina.nombre',
                    'maquina.maquinagrupo_id',
                    'etapaprod.nombre as etapaprod_nombre',
                    'etapaprod.id as etapaprod_id'
                )
            ->get(); */
        
        //dd($tablas['maquinas']);
        $datas = Ot::reportOtItem($request);
        /* return response()->json([
            'data' => $datas,
            'maquinas' => $tablas['maquinas']
        ]); */
        return datatables($datas)->toJson();
    }

    public function aprobar(OpStoreRequest $request, OpDetService $service)
    {
        if ($request->ajax()) {
            //dd($request->EtapasProduccion[0]);
            //dd($request);
            //dd($request->otdet_id);
            $otdet = OtDet::findOrFail($request->otdet_id);
            //$apetapaprods = $otdet->producto->acuerdotecnico->apetapaprods;

            //dd($apetapaprods);
            /* foreach ($apetapaprods as $apetapaprod) {
                dd($apetapaprod->etapaprod);
            } */

            $ot = $otdet->ot; //Ot::findOrFail($otdet->ot_id);
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
            if(isset($ot->otnotaventa)){
                $request1->merge(['notaventa_id' => $ot->otnotaventa->notaventa_id]);
                $request1->request->set('notaventa_id', $ot->otnotaventa->notaventa_id);
            }
            $clibloq = clienteBloqueado($ot->cliente_id,0,$request1);
            if(!is_null($clibloq["bloqueo"])){
                /* return redirect('ot')->with([
                    "mensaje" => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                    "tipo_alert" => "alert-error"
                ]); */
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                    'tipo_alert' => 'error'
                ]);
            }
            $request1->merge(['deldesbloqueo' => 1]);
            $request1->request->set('deldesbloqueo', 1);
            $clibloq = clienteBloqueado($ot->cliente_id,0,$request1);

            //dd($request);

            /* $op = new Op();
            $op->ot_id = $ot->otdet_id;
            $op->cantprod = $otdet->cantprod;
            $op->kgprod = $request->kgprod;
            $op->ordenaten = $request->ordenaten;
            $op->obs = $request->obs;
            $op->usuario_id = auth()->otdet_id();
            if($op->save()){
                $opdets[] = new OpDet([
                    'op_id' => $op->otdet_id, 
                    'otdet_id' => $request->otdet_id,
                    'obs' => $request->obsext,
                    'maquina_id' => $request->extrusora
                ]);
                if($request->impresora != "X"){
                    $opdets[] = new OpDet([
                        'op_id' => $op->otdet_id, 
                        'otdet_id' => $request->otdet_id,
                        'obs' => $request->obsimp,
                        'maquina_id' => $request->impresora
                    ]);
                }
                if($request->selladora != "X"){
                    $opdets[] = new OpDet([
                        'op_id' => $op->otdet_id, 
                        'otdet_id' => $request->otdet_id,
                        'obs' => $request->obssell,
                        'maquina_id' => $request->selladora
                    ]);
                }
                $aux_save = $op->opdets()->saveMany($opdets);
                if($aux_save){
                    return response()->json([
                        'mensaje' => 'Registro guardo con exito.',
                        'status' => '0',
                        'id' => $request->otdet_id,
                        'nfila' => $request->otdet_id,
                        'dte_id' => $request->otdet_id,
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
            } */
            //dd($request->EtapasProduccion);
            DB::beginTransaction();

            try {
                //$otdet->kgprog = $otdet->kgprog + $request->kgprod;
                $otdet->updated_at = date("Y-m-d H:i:s");
                $otdet->save();

                if(isset($request->statusCerrar_otDet) and $request->statusCerrar_otDet == 1){
                    $otdetcerr = new OtDetCerr();
                    $otdetcerr->otdet_id = $request->otdet_id;
                    $otdetcerr->obs = "Cerrado programacion. Cantida de KG programados + KG produccion es >= a KG produccion original.";
                    $otdetcerr->tipo = 1;
                    $otdetcerr->usuario_id = auth()->id();
                    $otdetcerr->save();
                }

                $op = new Op();
                $op->otdet_id = $request->otdet_id;
                $op->cantprod = round(($request->kgprod * $otdet->cant) / $otdet->kg, 2);
                $op->kgprod = $request->kgprod;
                $op->prioridad = $request->prioridad;
                $op->obs = $request->obs ?? null;
                $op->usuario_id = auth()->id();
                $op->save();
                $i = 0;

                foreach ($request->EtapasProduccion as $EtapasProduccion) {
                    /* $aux_saldokg = 0;
                    if($i == 0){
                        //Si es el proceso inicial se cierra de forma automatica y se asigna el saldo de kg = al kgprod
                        $aux_saldokg = $request->kgprod;
                    } */
                    $aux_saldokg = $request->kgprod;
                    $opdet = $op->opdets()->create([
                        'apsucetapaprod_id' => $EtapasProduccion["apsucetapaprod_id"],
                        'obs' => $EtapasProduccion["observacion"] ?? null,
                        'kg' => $request->kgprod ?? null,
                        'cant' => $op->cantprod ?? null,
                        'saldokg' => 0, //Se inicializa en 0 y se actualiza luego
                    ]);
                    if($i == 0){
                        /* $opdet->cantrec = $otdet->cantprod;
                        $opdet->kgrec = $otdet->kgprod; */
                        $opdet->cantrec = $op->cantprod;
                        $opdet->kgrec = $aux_saldokg;
                        $opdet->saldokg = $aux_saldokg;
                        $opdet->save();
                    }
                    if($EtapasProduccion["maquina_id"] > 0){
                        $opdetmaquina = new OpDetMaquina();
                        $opdetmaquina->opdet_id = $opdet->id;
                        $opdetmaquina->maquina_id = $EtapasProduccion["maquina_id"];
                        $opdetmaquina->save();
                        /* $opdet->opdetmaquina()->create([
                            'maquina_id' => $apetapaprod["maquina_id"],
                        ]); */
                    }
                    $i++;
                }
                


                /* $opDetData = $request->only([
                    'id', 'extrusora', 'obsext', 'impresora', 'obsimp', 'selladora', 'obssell'
                ]);
                dd($request);
                $service->guardarDesdeRequest($op, $opDetData); */

                DB::commit();

                return response()->json([
                    'mensaje' => 'Registro guardado con éxito.',
                    'status'  => '0',
                    'id'      => $request->otdet_id,
                    'nfila'   => $request->otdet_id,
                    'dte_id'  => $request->otdet_id,
                    'op_id'   => $op->id, // número de OP generada para mostrar al programador
                ]);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'id' => 0,
                    'mensaje' => "Error: " . $e->getMessage(),
                    'tipo_alert' => 'error'
                ]);
            }
        }
    }

    public function cerraropdet(Request $request, OpDetService $service)
    {
        if ($request->ajax()) {
            //dd($request->obs);
            $otdet = OtDet::findOrFail($request->otdet_id);

            $ot = $otdet->ot; //Ot::findOrFail($otdet->ot_id);
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
                /* return redirect('ot')->with([
                    "mensaje" => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                    "tipo_alert" => "alert-error"
                ]); */
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                    'tipo_alert' => 'error'
                ]);
            }
            $request1->merge(['deldesbloqueo' => 1]);
            $request1->request->set('deldesbloqueo', 1);
            $clibloq = clienteBloqueado($ot->cliente_id,0,$request1);


            DB::beginTransaction();

            try {
                //$otdet->kgprog = $otdet->kgprog + $request->kgprod;
                $otdet->updated_at = date("Y-m-d H:i:s");
                $otdet->save();

                if(isset($request->statusCerrar_otDet) and $request->statusCerrar_otDet == 1){
                    $otdetcerr = new OtDetCerr();
                    $otdetcerr->otdet_id = $request->otdet_id;
                    $otdetcerr->obs = $request->obs;
                    $otdetcerr->tipo = 1;
                    $otdetcerr->usuario_id = auth()->id();
                    $otdetcerr->save();
                }

                DB::commit();

                return response()->json([
                    'mensaje' => 'Registro guardado con éxito.',
                    'status' => '0',
                    'id' => $request->otdet_id,
                    'nfila' => $request->otdet_id,
                    'dte_id' => $request->otdet_id,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'id' => 0,
                    'mensaje' => "Error: " . $e->getMessage(),
                    'tipo_alert' => 'error'
                ]);
            }
        }
    }
}
