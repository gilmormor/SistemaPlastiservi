<?php

namespace App\Http\Controllers;

use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaVentaTransController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-nota-venta-transito');
        return view('notaventatransito.index');
    }

    public function notaventatranspage(){
        session(['aux_aproNV' => '0']);
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
        FROM vendedor INNER JOIN persona
        ON vendedor.persona_id=persona.id and vendedor.deleted_at is null
        INNER JOIN usuario 
        ON persona.usuario_id=usuario.id and persona.deleted_at is null
        WHERE usuario.id=' . auth()->id() . ';';
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $aux_condvend = 'notaventa.vendedor_id = ' . $vendedor_id;
            $aux_condvendcot = 'cotizacion.vendedor_id = ' . $vendedor_id;
        }else{
            $aux_condvend = 'true';
            $aux_condvendcot = 'true';
        }

        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = "SELECT notaventa.id,notaventa.fechahora,notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs,
                    (SELECT COUNT(*) 
                    FROM notaventadetalle 
                    WHERE notaventadetalle.notaventa_id=notaventa.id and 
                    notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador,
                    '' as estado,'' as pdfnv
                FROM notaventa inner join cliente
                on notaventa.cliente_id = cliente.id
                where $aux_condvend
                and anulada is null
                and (aprobstatus=2) 
                and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
                and notaventa.deleted_at is null;";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);

        //dd($datas);

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
