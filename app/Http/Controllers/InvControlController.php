<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarInvControl;
use App\Models\CategoriaGrupoValMes;
use App\Models\InvControl;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvControlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-inventario-control');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $sucursales = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();

        return view('invcontrol.index', compact('sucursales'));
        //return view('invcontrol.index');
    }

    //public function invcontrolpage($mesanno,$sucursal_id){
    public function invcontrolpage(Request $request){
        //dd($request);
        $datas = InvMov::stocksql($request);
        return datatables($datas)->toJson();
        /*
        return datatables()
        ->eloquent(InvMov::stock($request))
        ->toJson();
        */
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarInvControl $request)
    {
 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function procesarcierreini(Request $request){
        can('guardar-inventario-control');
        if ($request->ajax()) {
            $aux_annomes = CategoriaGrupoValMes::annomes($request->annomes);
            $tipomensaje = 'error';
            $mensaje = "";
            if($aux_annomes >= date('Ym')){
                $mensaje = 'No se puede cerrar un mes mayor o igual al actual.';
            }else{
                $invcontrol = InvControl::where('annomes','=',$aux_annomes)
                                        ->where('sucursal_id','=',$request->sucursal_id)
                                        ->get();
                if(count($invcontrol) == 0){
                    $mensaje = 'Mes no ha sido aperturado';
                }else{
                    if($invcontrol[0]->status == 1){
                        $mensaje = 'Mes ya fue cerrado';
                    }else{
                        $annomesini = date("Ym",strtotime($aux_annomes.'01'."+ 1 month"));
                        $invcontrol = InvControl::where('annomes','=',$annomesini)
                                                ->where('sucursal_id','=',$request->sucursal_id);
                        //dd($annomesini);
                        if($invcontrol->count() == 0){
                            $invcontrol = InvControl::create([
                                'annomes' => $annomesini,
                                'sucursal_id' => $request->sucursal_id,
                                'usuario_id' => auth()->id()
                            ]);
                            /*
                            $invmovdets = InvMov::join("invmovdet","invmov.id","=","invmovdet.invmov_id")
                                    ->where("annomes","=",$aux_annomes)
                                    ->join('invbodega', 'invmovdet.invbodega_id', '=', 'invbodega.id')
                                    ->select([
                                                'invbodegaproducto_id',
                                                'producto_id',
                                                'invbodega_id',
                                                'invmovdet.sucursal_id',
                                                'unidadmedida_id',
                                                'invmovdet.invmovtipo_id',
                                                DB::raw('sum(cant) as cant'),
                                                DB::raw('sum(cantkg) as cantkg')
                                            ])
                                    ->whereNull('invmov.deleted_at')
                                    ->whereNull('invmovdet.deleted_at')        
                                    ->groupBy("invmovdet.invbodegaproducto_id")
                                    ->get();
                            */
                            $aux_sucursal_id = $request->sucursal_id;
                            $invmovdets = InvMov::join('invmovdet', function ($join)  use ($aux_sucursal_id) {
                                                    $join->on('invmov.id', '=', 'invmovdet.invmov_id')
                                                        ->where("invmovdet.sucursal_id",$aux_sucursal_id);
                                                })
                                    ->where("annomes","=",$aux_annomes)
                                    ->join('invbodega', 'invmovdet.invbodega_id', '=', 'invbodega.id')
                                    ->select([
                                                'invbodegaproducto_id',
                                                'producto_id',
                                                'invbodega_id',
                                                'invmovdet.sucursal_id',
                                                'unidadmedida_id',
                                                'invmovdet.invmovtipo_id',
                                                DB::raw('sum(cant) as cant'),
                                                DB::raw('sum(cantkg) as cantkg')
                                            ])
                                    ->whereNull('invmov.deleted_at')
                                    ->whereNull('invmovdet.deleted_at')
                                    ->where("invmovdet.sucursal_id",$aux_sucursal_id)
                                    ->groupBy("invmovdet.invbodegaproducto_id")
                                    ->get();
                            $invmov_array = array();
                            $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                            $invmov_array["annomes"] = $annomesini;
                            $invmov_array["desc"] = "Movimiento inicio mes: " . $annomesini;
                            $invmov_array["obs"] = "Movimiento inicio mes: " . $annomesini;
                            $invmov_array["invmovmodulo_id"] = 4; //Cierre inicio Mes Inv
                            $invmov_array["idmovmod"] = $invcontrol->id;
                            $invmov_array["invmovtipo_id"] = 6;
                            $invmov_array["sucursal_id"] = $request->sucursal_id;
                            $invmov_array["usuario_id"] = auth()->id();
                            $arrayinvmov_id = array();
                            
                            $invmov = InvMov::create($invmov_array);
                            foreach ($invmovdets as $invmovdet) {            
                                $array_invmovdet = $invmovdet->attributesToArray();
                                //dd($array_invmovdet);
                                /*
                                $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                                $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                                $array_invmovdet["invbodega_id"] = $request->invbodega_id;
                                $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                                */
                                $array_invmovdet["invmovtipo_id"] = 6;
                                $array_invmovdet["sucursal_id"] = $request->sucursal_id;
                                $array_invmovdet["usuario_id"] = auth()->id();

                                $array_invmovdet["invmov_id"] = $invmov->id;
                                $invmovdet = InvMovDet::create($array_invmovdet);
                            }
                        }else{
                            $mensaje = 'Mes ya fue cerrado';
                        }
                        InvControl::where('annomes','=',$aux_annomes)
                                    ->where('sucursal_id','=',$request->sucursal_id)
                                    ->update(['status' => 1]);
                        $mensaje = 'Mes procesado con exito';
                        $tipomensaje = 'success';
                    }
                }
            }
            return response()->json([
                'tipomensaje' => $tipomensaje,
                'mensaje' => $mensaje
            ]);
        }
    }
}
