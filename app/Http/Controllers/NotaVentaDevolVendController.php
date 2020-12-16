<?php

namespace App\Http\Controllers;

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
        can('listar-nota-venta-cerrada');
        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = 'SELECT notaventa.id,notaventa.fechahora,notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs,oc_id,oc_file,
                (SELECT COUNT(*) 
                FROM notaventadetalle 
                WHERE notaventadetalle.notaventa_id=notaventa.id and 
                notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
            FROM notaventa inner join cliente
            on notaventa.cliente_id = cliente.id
            where isnull(notaventa.findespacho)
            and isnull(anulada)
            and (aprobstatus=1 or aprobstatus=3)
            and notaventa.id not in (SELECT notaventa_id FROM despachoord where isnull(despachoord.deleted_at) and despachoord.id not in (SELECT despachoordanul.despachoord_id from despachoordanul where isnull(despachoordanul.deleted_at)) )
            and isnull(notaventa.deleted_at);';
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);

        return view('notaventadevolvend.index', compact('datas'));
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
}
