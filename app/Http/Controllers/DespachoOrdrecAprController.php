<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DespachoOrdRecAprController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-rechazo-orden-despacho');
        $pantalla = 1;
        return view('despachoordrec.index',compact('pantalla'));
    }

    public function despachoordrecpageapr(){
        $sql = "SELECT despachoordrec.id,DATE_FORMAT(despachoordrec.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                cliente.razonsocial,despachoord_id,despachoordrec.documento_id,despachoordrec.documento_file,
                '' as pdfcot,
                despachoordrec.fechahora as fechahora_aaaammdd,
                despachoord.notaventa_id,despachoord.despachosol_id,despachoordrec.aprobstatus,despachoordrec.aprobobs
            FROM despachoordrec inner join despachoord
            on despachoord.id = despachoordrec.despachoord_id and isnull(despachoord.deleted_at)
            and despachoord.id not in (select despachoordanul.despachoord_id from despachoordanul where isnull(despachoordanul.deleted_at))
            inner join notaventa
            on notaventa.id = despachoord.notaventa_id and isnull(notaventa.deleted_at) and isnull(notaventa.anulada)
            inner join cliente
            on cliente.id = notaventa.cliente_id and isnull(cliente.deleted_at)
            where despachoordrec.aprobstatus=1
            and isnull(despachoordrec.anulada) and isnull(despachoordrec.deleted_at)
            ORDER BY despachoordrec.id desc;";
        $datas = DB::select($sql);
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
}
