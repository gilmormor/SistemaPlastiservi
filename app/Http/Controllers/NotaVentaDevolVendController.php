<?php

namespace App\Http\Controllers;

use App\Models\NotaVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaVentaDevolVendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        can('listar-devolver-nota-venta-vendedor');
        $datas = consulta("");

        return view('notaventadevolvend.index', compact('datas'));
    }

    public function indexanular()
    {        
        can('listar-anular-nota-venta');
        $datas = consulta("");

        return view('notaventaanular.index', compact('datas'));
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
    public function store(Request $request)
    {
        //
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
    public function actualizar(Request $request, $id)
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

    public function actualizarreg(Request $request)
    {
        can('guardar-devolver-nota-venta-vendedor');
        if ($request->ajax()) {
            $datas = consulta($request->id);
            //dd(count($datas));

            $notaventa = NotaVenta::findOrFail($request->id);
            $notaventa->aprobstatus = null;
            $notaventa->aprobusu_id = null;
            $notaventa->aprobfechahora = null;
            $notaventa->aprobobs = null;
            
            if(count($datas) > 0){
                if ($notaventa->save()) {
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }    
            }else{
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actualizanular(Request $request)
    {
        can('guardar-anular-nota-venta');
        if ($request->ajax()) {
            $datas = consulta($request->id);
            //dd(count($datas));

            $notaventa = NotaVenta::findOrFail($request->id);
            $notaventa->anulada = date("Y-m-d H:i:s");;
            
            if(count($datas) > 0){
                if ($notaventa->save()) {
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }    
            }else{
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }


}


function consulta($id){
    $aux_condid = "true";
    if($id!=""){
        $aux_condid = "notaventa.id=$id";
    }
    //Consultar registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4

    $sql = "SELECT notaventa.id,notaventa.fechahora,notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs,oc_id,oc_file,
                (SELECT COUNT(*) 
                FROM notaventadetalle 
                WHERE notaventadetalle.notaventa_id=notaventa.id and 
                notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
            FROM notaventa inner join cliente
            on notaventa.cliente_id = cliente.id
            where $aux_condid
            and isnull(notaventa.findespacho)
            and isnull(anulada)
            and (aprobstatus=1 or aprobstatus=3)
            and notaventa.id not in (SELECT notaventa_id 
                                    FROM despachosol 
                                    where isnull(despachosol.deleted_at) and despachosol.id 
                                    not in (SELECT despachosolanul.despachosol_id 
                                            from despachosolanul 
                                            where isnull(despachosolanul.deleted_at)
                                           )
                                    )
            and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
            and isnull(notaventa.deleted_at)
            order by notaventa.id desc;";
        //dd($sql);
    $datas = DB::select($sql);
    return $datas;

}