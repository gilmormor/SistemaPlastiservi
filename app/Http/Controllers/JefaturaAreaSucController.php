<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\ClienteVendedor;
use App\Models\Jefatura;
use App\Models\JefaturaSucursalArea;
use App\Models\Persona;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\SucursalArea;
use Illuminate\Http\Request;

class JefaturaAreaSucController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //can('listar-jefatura');
        //return view('jefaturaAreaSuc.index', compact('datas'));
        //$sucursales = Sucursal::with('areas')->orderBy('id')->get(); //->pluck('nombre', 'id')->toArray();
        //$jefaturas = Jefatura::orderBy('id')->get();
        $sucursales  = SucursalArea::join('sucursal','sucursal_area.sucursal_id','=','sucursal.id')
                                    ->join('area','sucursal_area.area_id','=','area.id')
                                    ->select(['sucursal_area.id as id','sucursal.nombre as suc_nombre','area.nombre as are_nombre'])
                                    ->get();
        //dd($sucursales);
        return view('jefaturaAreaSuc.index',compact('sucursales'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
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
    public function editar($id)
    {
        //findOrFail($id)
        $sucursales = SucursalArea::where('sucursal_area.id', '=', $id)
                                    ->join('sucursal','sucursal_area.sucursal_id','=','sucursal.id')
                                    ->join('area','sucursal_area.area_id','=','area.id')
                                    ->select(['sucursal_area.id as id','sucursal.nombre as suc_nombre','area.nombre as are_nombre'])
                                    ->get();
        $jefaturas = Jefatura::orderBy('id')->pluck('nombre', 'id')->toArray();
        //dd($jefaturas);
        //dd($sucursales);
        $jefaturasucursalareas = JefaturaSucursalArea::where('sucursal_area_id', '=', $id)->get();
        //dd($jefaturasucursalareas);
        $user = Usuario::findOrFail(auth()->id());
        $personas = Usuario::join('sucursal_usuario', function ($join) {
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
            ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
                    })
            ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
            ->select([
                'persona.id',
                'persona.nombre',
                'persona.apellido'
            ])
            ->get();
        $personas = Persona::orderBy('id')
                    ->where('activo', '=', 1)
                    ->get();
        //dd($personas);
        return view('jefaturaAreaSuc.editar',compact('sucursales','jefaturas','jefaturasucursalareas','personas'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(Request $request, $id)
    {
        $sucursalArea = SucursalArea::findOrFail($id);
        $sucursalArea->jefaturas()->sync($request->jefatura_id);
        return redirect('jefaturaAreaSuc')->with('mensaje','Sucursal actualizada con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
    }

    public function ObtAreas1()
    {
        $areas  = SucursalArea::where('sucursal_id',2)
                                  ->join('area','sucursal_area.area_id','=','area.id')
                                  ->get();
        $sucursales = SucursalArea::with('SucursalArea:id,area_id,sucursal_id')->where('sucursal_id',2)->get();
        //dd($sucursales);
    }

    public function ObtAreas(Request $request)
    {
        if($request->ajax()){
            //$areas = Area::where('id', 1)->get();  //$request->area_id
            
            $areas  = SucursalArea::where('sucursal_id',$request->sucursal_id)
                                  ->join('area','sucursal_area.area_id','=','area.id')
                                  ->select(['sucursal_area.id as id','area.nombre as nombre'])
                                  ->get();
            
            $sucursalAreas  = SucursalArea::where('sucursal_id',$request->sucursal_id)
                                  ->get();
            //dd($areas);
            $areasArray = [];
            //foreach($sucursalAreas as $sucursalArea){
            foreach($areas as $area){
                //$area = Area::findOrFail($sucursalArea->area_id);
                //$areasArray[$sucursalArea->id] = $area->nombre;
                $areasArray[$area->id] = $area->nombre;
            }
            //dd($areasArray);
            return response()->json($areasArray);
        }
    }

    public function asignarjefej(Request $request)
    {
        //dd($request->aux_vectorJ[0][1]);
        if ($request->ajax()) {
            $respuesta = ['mensaje' => 'ng'];
            for ( $i = 0; $i < $request->cont; $i++ ){
                $id = $request->aux_vectorJ[$i][0];
                $persona_id = $request->aux_vectorJ[$i][1];
                if(!is_null($persona_id)){
                    $jefaturasucursalarea = JefaturaSucursalArea::findOrFail($id);
                    $jefaturasucursalarea->persona_id = $persona_id;
                    if ($jefaturasucursalarea->save()) {
                        $respuesta = ['mensaje' => 'ok'];
                    } else {
                        $respuesta = ['mensaje' => 'ng'];
                        return response()->json(['mensaje' => 'ng']);
                    }    
                }
    
            }
            return response()->json($respuesta);
    
        } else {
            abort(404);
        }
    }

    public function asignarjefe(Request $request)
    {
        //dd($request);
        $cont_id = count($request->id);
        if($cont_id>0){
            for ($i=0; $i < $cont_id ; $i++){
                if(is_null($request->personal_idD[$i])==false){
                    $jefaturasucursalarea = JefaturaSucursalArea::findOrFail($request->id[$i]);
                    $jefaturasucursalarea->persona_id = $request->personal_idD[$i];
                    $jefaturasucursalarea->save();

                }
            }
        }
        return redirect('jefaturaAreaSuc')->with('mensaje','Se actualizó con exito.');
    }


}
