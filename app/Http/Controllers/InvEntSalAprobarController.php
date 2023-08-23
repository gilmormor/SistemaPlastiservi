<?php

namespace App\Http\Controllers;

use App\Models\InvEntSal;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvEntSalAprobarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-aprobar-entrada-salida-inventario');
        return view('inventsalaprobar.index');
    }

    public function inventsalaprobarpage(){
        $datas = consultaindex();
        return datatables($datas)->toJson();

/*
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        return datatables()
            ->eloquent(InvEntSal::query()
                        ->where('staaprob','=',1)
                        ->whereIn('inventsal.sucursal_id', $sucurArray)                        
                    )
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

function consultaindex(){
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurArray = implode(",", $sucurArray);
    $arraySucFisxUsu = implode(",", sucFisXUsu($user->persona));
    //dd($arraySucPerUsu);
    //CON ESTA INSTRUCCION SQL VALIDO QUE LAS SUCURSALES QUE TIENE USUARIO CREADOR DEL REGISTRO
    //ESTEN CONTENIDAS EN LAS SUCURSALES QUE TIENE EL USUARIO QUE VA A VALIDAR.
    //ES DECIR QUE AL USUARIO QUE VA A VALIDAR SE FILTREN SOLO LOS REGISTROS QUE CONTENGAN SU SUCURSAL SOLO LE SALGAN LOS REGISTROS QUE CONTENGAN SU MISMA SUCURSAL
    $sql = "SELECT *
        FROM inventsal INNER JOIN vista_sucfisxusu
        ON inventsal.usuario_id = vista_sucfisxusu.usuario_id
        WHERE staaprob = 1
        and vista_sucfisxusu.sucursal_id IN ($arraySucFisxUsu) 
        and isnull(inventsal.deleted_at)
        GROUP BY inventsal.id;";
    return DB::select($sql);
}