<?php

namespace App\Http\Controllers;

use App\Models\MateriaPrima;
use App\Models\Ot;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;

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
        $selecmultprod = 1;
        return view('otitemprogramacion.index',compact('tablas','selecmultprod'));
    }

    public function otitemprogramacionpage(Request $request){
        $request->request->set('aux_estado', 3);
        $request->request->set('noanul', 1);
        $request->request->set('modulo_id', 34);
        //$request->merge(['sta_envprog' => 1]);
        $request->request->set('sta_envprog', 1);
        
        //dd($request);
        $datas = Ot::reportotitem($request);
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
