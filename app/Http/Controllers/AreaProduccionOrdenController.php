<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccionSuc;
use App\Models\Ot;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class AreaProduccionOrdenController extends Controller
{
    public function index()
    {
        can('listar-area-produccion-orden');
        //$datas = FormaPago::orderBy('id')->get();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        return view('areaproduccionorden.index',compact('tablas'));
    }

    public function areaproduccionordenpage(Request $request){
        //dd($request);
        $datas = AreaProduccionSuc::report($request);
        return datatables($datas)->toJson();
    }

    public function guardar(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            $areaproduccionsuc = AreaProduccionSuc::findOrFail($request->id);
            if(strtotime($areaproduccionsuc->updated_at) != $request->updatednum_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>"Registro modificado por otro usuario. \nFecha: " . date('d-m-Y h:i:s A', strtotime($areaproduccionsuc->updated_at)),
                    'tipo_alert' => 'error'
                ]);    
            }
            if(strtotime($areaproduccionsuc->sucursal->updated_at) != $request->sucursal_updatednum_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>"Registro modificado por otro usuario. \nFecha: " . date('d-m-Y h:i:s A', strtotime($areaproduccionsuc->sucursal->updated_at)),
                    'tipo_alert' => 'error'
                ]);    
            }
            if(strtotime($areaproduccionsuc->areaproduccion->updated_at) != $request->areaproduccion_updatednum_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>"Registro modificado por otro usuario. \nFecha: " . date('d-m-Y h:i:s A', strtotime($areaproduccionsuc->areaproduccion->updated_at)),
                    'tipo_alert' => 'error'
                ]);    
            }
            $areaproduccionsuc->orden = $request->orden;
            if($areaproduccionsuc->save()){
                return response()->json([
                    'mensaje' => 'Registro guardo con exito.',
                    'status' => '0',
                    'id' => 0, //$request->id,
                    'nfila' => $request->id,
                    'dte_id' => $request->id,
                    'tipo_alert' => 'success'
                ]);
            }else{
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Ocurrio un error al intenta guardar.",
                    'tipo_alert' => 'error'
                ]);
            }           
        }
    }

}