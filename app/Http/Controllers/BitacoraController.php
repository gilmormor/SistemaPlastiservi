<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function guardar(Request $request)
    {
        //dd($request);
        //dd(getRealIP());
        $bitacora = new Bitacora();
        $bitacora->empresa_id = 1;
        $bitacora->usuario_id = auth()->id();
        $bitacora->codmov = $request->codmov;
        $bitacora->desc = $request->desc;
        $bitacora->ip = getRealIP();
        $bitacora->save();
        //$bitacora = Bitacora::create($request->all());
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


function getRealIP(){

    if (isset($_SERVER["HTTP_CLIENT_IP"])){

        return $_SERVER["HTTP_CLIENT_IP"];

    }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){

        return $_SERVER["HTTP_X_FORWARDED_FOR"];

    }elseif (isset($_SERVER["HTTP_X_FORWARDED"])){

        return $_SERVER["HTTP_X_FORWARDED"];

    }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){

        return $_SERVER["HTTP_FORWARDED_FOR"];

    }elseif (isset($_SERVER["HTTP_FORWARDED"])){

        return $_SERVER["HTTP_FORWARDED"];

    }else{

        return $_SERVER["REMOTE_ADDR"];

    }
}       