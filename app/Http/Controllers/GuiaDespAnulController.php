<?php

namespace App\Http\Controllers;

use App\Models\DespachoOrd;
use App\Models\GuiaDesp;
use App\Models\GuiaDespAnul;
use Illuminate\Http\Request;

class GuiaDespAnulController extends Controller
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
        $guiadesp = GuiaDesp::findOrFail($request->guiadesp_id);
        if($request->updated_at != $guiadesp->updated_at){
            return redirect('guiadesp')->with([
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'alert-error'
            ]);
        }
        //dd($request);
        $request->request->add(['usuario_id' => auth()->id()]);
        $guiadespanul = GuiaDespAnul::create($request->all());
        $guiadesp->updated_at = date("Y-m-d H:i:s");
        $guiadesp->save();
        $despachoord = DespachoOrd::findOrFail($guiadesp->despachoord_id);
        $despachoord->updated_at = date("Y-m-d H:i:s");
        $despachoord->save();
        return response()->json(['mensaje' => 'ok']);

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
