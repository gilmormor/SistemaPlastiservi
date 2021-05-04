<?php

namespace App\Http\Controllers;

use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CotizacionAprobarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        can('listar-aprobar-cotizacion');
        //session(['aux_aprocot' => '0']) 0=Pantalla Normal CRUD de Cotizaciones
        //session(['aux_aprocot' => '1']) 1=Pantalla Solo para aprobar cotizacion para luego emitir la Nota de Venta
        session(['aux_aprocot' => '1']);
        $user = Usuario::findOrFail(auth()->id());
        //$vendedor_id=$user->persona->vendedor->id;

        //$aux_statusPant 0=Pantalla Normal CRUD de Cotizaciones
        //$aux_statusPant 1=Pantalla Solo para aprobar cotizacion para luego emitir la Nota de Venta
        $aux_statusPant = 1;

        $sql = 'SELECT cotizacion.id,cotizacion.fechahora,
                    if(isnull(cliente.razonsocial),clientetemp.razonsocial,cliente.razonsocial) as razonsocial,
                    aprobstatus, 
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and cotizaciondetalle.precioxkilo<cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion left join cliente
                on cotizacion.cliente_id = cliente.id
                left join clientetemp
                on cotizacion.clientetemp_id = clientetemp.id
                where aprobstatus=2
                and cotizacion.deleted_at is null;';
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);
        //dd($datas);
        
        //$datas = Cotizacion::where('usuario_id',auth()->id())->get();
        return view('cotizacionaprobar.index', compact('datas'));
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
