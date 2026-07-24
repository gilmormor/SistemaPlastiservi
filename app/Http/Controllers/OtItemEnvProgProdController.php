<?php

namespace App\Http\Controllers;

use App\Models\MateriaPrima;
use App\Models\Ot;
use App\Models\OtDet;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class OtItemEnvProgProdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-envia-item-ot-prog');
        //$datas = FormaPago::orderBy('id')->get();
        $tablas['materiaprimas'] = MateriaPrima::orderBy('id')->get();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $selecmultprod = 1;
        return view('otitemenvprogprod.index',compact('tablas','selecmultprod'));
    }

    public function otitemenvprogprodpage(Request $request){
        $request->request->set('aux_estado', 3);
        $request->request->set('noanul', 1);
        $request->request->set('modulo_id', 34);
        // Mostrar ítems no enviados (0) y con envío parcial (1)
        $request->request->set('sta_envprog', '0,1');
        $request->request->set('sta_fabricacion', 1);

        $datas = Ot::reportOtItem($request);
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
            $request1->merge(['modulo_id' => 34]);
            $request1->request->set('modulo_id', 34);
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

            // Validar que no se envíe más del saldo pendiente
            $cantPendiente = $otdet->cant - ($otdet->cantenvprog ?? 0);
            if ($request->cantprod > $cantPendiente + 0.001) {
                return response()->json([
                    'id' => 0,
                    'mensaje' => "La cantidad a enviar ({$request->cantprod}) supera el saldo pendiente (" . number_format($cantPendiente, 2) . ").",
                    'tipo_alert' => 'error'
                ]);
            }

            // Acumular en los campos de envío a programación (NO tocar cantprod/kgprod que son de producción real)
            $otdet->cantenvprog = ($otdet->cantenvprog ?? 0) + $request->cantprod;
            $otdet->kgenvprog   = ($otdet->kgenvprog   ?? 0) + $request->kgprod;
            $otdet->espesorprod   = $request->espesorprod;
            $otdet->envprogusu_id = auth()->id();
            $otdet->envprogfecha  = date("Y-m-d H:i:s");

            // Estado: 1=parcial, 2=todo enviado
            if ($otdet->cantenvprog >= $otdet->cant) {
                $otdet->sta_envprog = 2;
            } else {
                $otdet->sta_envprog = 1;
            }

            $ot->updated_at = date("Y-m-d H:i:s");
            if($ot->save() and $otdet->save()){
                return response()->json([
                    'mensaje'     => 'Registro guardado con exito.',
                    'status'      => '0',
                    'id'          => $otdet->id,
                    'nfila'       => $otdet->id,
                    'dte_id'      => $otdet->id,
                    'sta_envprog' => $otdet->sta_envprog,
                    'cantenvprog' => $otdet->cantenvprog,
                    'kgenvprog'   => $otdet->kgenvprog,
                    'cant'        => $otdet->cant,
                    'kg'          => $otdet->kg,
                ]);
            } else {
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Ocurrio un error al intentar guardar.",
                    'tipo_alert' => 'error'
                ]);
            }

            
        }
    }

    public function calcularPeso(Request $request){
        //dd($request);
        $producto = Producto::findOrFail($request->producto_id);
        $at = $producto->acuerdotecnico;
        $at->at_peso = 0;
        $at->at_espesor = $request->espesorprod;
        return pesounitat($at);
        /* dd(pesounitat($at));
        dd($at); */
    }

}
