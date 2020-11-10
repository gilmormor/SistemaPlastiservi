<?php

namespace App\Http\Controllers;

use App\Models\DespachoOrd;
use App\Models\DespachoOrdAnulGuiaFact;
use Illuminate\Http\Request;

class DespachoOrdAnulGuiaFactController extends Controller
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

    public function guardaranularguia(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            $despachoord = DespachoOrd::findOrFail($request->id);
            $despachoordanulguiafact = new DespachoOrdAnulGuiaFact();

            $despachoordanulguiafact->despachoord_id = $request->id;
            $despachoordanulguiafact->guiadespacho = $despachoord->guiadespacho;
            $despachoordanulguiafact->guiadespachofec = $despachoord->guiadespachofec;
            $despachoordanulguiafact->numfactura = $despachoord->numfactura;
            $despachoordanulguiafact->fechafactura = $despachoord->fechafactura;
            $despachoordanulguiafact->numfacturafec = $despachoord->numfacturafec;
            $despachoordanulguiafact->observacion = $request->observacion;
            $despachoordanulguiafact->usuario_id = auth()->id();
            $despachoordanulguiafact->save();

            $despachoord->guiadespacho = NULL;
            $despachoord->guiadespachofec = NULL;
            $despachoord->numfactura = NULL;
            $despachoord->fechafactura = NULL;
            $despachoord->numfacturafec = NULL;

            if ($despachoord->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

}
