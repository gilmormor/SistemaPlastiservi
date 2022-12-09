<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCentroEconomico;
use App\Models\CentroEconomico;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class CentroEconomicoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-centro-economico');
        //$datas = CentroEconomico::orderBy('id')->get();
        return view('centroeconomico.index');
    }

    public function centroeconomicopage(){
        return datatables()
            ->eloquent(CentroEconomico::query())
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-centro-economico');
        $sucursales = Sucursal::orderBy('id')->get();
        return view('centroeconomico.crear', compact('sucursales'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarCentroEconomico $request)
    {
        can('guardar-centro-economico');
        $request->request->add(['usuario_id' => auth()->id()]);
        CentroEconomico::create($request->all());
        return redirect('centroeconomico')->with('mensaje','Forma de Pago creado con exito');
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
    public function editar($id)
    {
        can('editar-centro-economico');
        $data = CentroEconomico::findOrFail($id);
        $sucursales = Sucursal::orderBy('id')->get();
        return view('centroeconomico.editar', compact('data','sucursales'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCentroEconomico $request, $id)
    {
        CentroEconomico::findOrFail($id)->update($request->all());
        return redirect('centroeconomico')->with('mensaje','Forma Pago actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        /*
        if ($request->ajax()) {
            if (CentroEconomico::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
        */

        if(can('eliminar-centro-economico',false)){
            if ($request->ajax()) {
                $data = CentroEconomico::findOrFail($request->id);
                $aux_contRegistos = $data->guiadesps->count();
                //dd($aux_contRegistos);
                if($aux_contRegistos > 0){
                    return response()->json(['mensaje' => 'cr']);
                }else{
                    if (CentroEconomico::destroy($request->id)) {
                        //dd('entro');
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $CentroEconomico = CentroEconomico::withTrashed()->findOrFail($request->id);
                        $CentroEconomico->usuariodel_id = auth()->id();
                        $CentroEconomico->save();
                        return response()->json(['mensaje' => 'ok']);
                    } else {
                        return response()->json(['mensaje' => 'ng']);
                    }    
                }
            } else {
                abort(404);
            }
    
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }
}
