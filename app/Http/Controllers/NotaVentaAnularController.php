<?php

namespace App\Http\Controllers;

use App\Models\NotaVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaVentaAnularController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-anular-nota-venta');
        return view('notaventaanular.index');
    }

    public function notaventaanularpage()
    {
        $datas = consulta("");
        return datatables($datas)->toJson(); 
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

    public function actualizanular(Request $request)
    {
        can('guardar-anular-nota-venta');
        if ($request->ajax()) {
            $datas = consulta($request->id);
            //dd(count($datas));

            $notaventa = NotaVenta::findOrFail($request->id);
            $notaventa->anulada = date("Y-m-d H:i:s");
            $notaventa->cotizacion_id = null;
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

    $sql = "SELECT notaventa.id,DATE_FORMAT(notaventa.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs,oc_id,oc_file,
                (SELECT COUNT(*) 
                FROM notaventadetalle 
                WHERE notaventadetalle.notaventa_id=notaventa.id and 
                notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
            FROM notaventa inner join cliente
            on notaventa.cliente_id = cliente.id
            /* Opt: anti-join reemplaza NOT IN; evita full scan de notaventacerrada por cada fila */
            LEFT JOIN notaventacerrada
            ON notaventacerrada.notaventa_id = notaventa.id AND ISNULL(notaventacerrada.deleted_at)
            where $aux_condid
            and isnull(notaventa.findespacho)
            and isnull(anulada)
            and (aprobstatus=1 or aprobstatus=3)
            /* Opt: NOT EXISTS con anti-join interno reemplaza NOT IN anidado; despachosol puede tener N filas por notaventa */
            AND NOT EXISTS (SELECT 1 FROM despachosol
                            LEFT JOIN despachosolanul ON despachosolanul.despachosol_id = despachosol.id AND ISNULL(despachosolanul.deleted_at)
                            WHERE despachosol.notaventa_id = notaventa.id
                            AND ISNULL(despachosol.deleted_at)
                            AND despachosolanul.despachosol_id IS NULL)
            AND notaventacerrada.notaventa_id IS NULL
            and isnull(notaventa.deleted_at)
            order by notaventa.id desc;";
        //dd($sql);
    $datas = DB::select($sql);
    return $datas;

}