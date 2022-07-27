<?php

namespace App\Http\Controllers;

use App\Models\ClienteTemp;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;

class ClienteTempController extends Controller
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
    
    public function buscarCliTemp(Request $request){
        if($request->ajax()){
            //dd($request);
            $clientetemp = ClienteTemp::join('cotizacion', 'clientetemp.id', '=', 'cotizacion.clientetemp_id')
                            ->where('clientetemp.rut', $request->rut)
                            ->whereNull("clientetemp.deleted_at")
                            ->whereNull("cotizacion.deleted_at")
                            ->select([
                                'clientetemp.id',
                                'cotizacion.id as cotizacion_id',
                                'clientetemp.rut',
                                'clientetemp.razonsocial',
                                'clientetemp.direccion',
                                'clientetemp.telefono',
                                'clientetemp.email',
                                'clientetemp.vendedor_id',
                                'clientetemp.giro_id',
                                'clientetemp.giro',
                                'clientetemp.comunap_id',
                                'clientetemp.formapago_id',
                                'clientetemp.plazopago_id',
                                'clientetemp.contactonombre',
                                'clientetemp.contactoemail',
                                'clientetemp.contactotelef',
                                'clientetemp.finanzascontacto',
                                'clientetemp.finanzanemail',
                                'clientetemp.finanzastelefono',
                                'clientetemp.sucursal_id',
                                'clientetemp.observaciones',
                                'clientetemp.usuariodel_id'                        
                            ]);
            return response()->json($clientetemp->get());
        }
    }
}
