<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCategoriaProd;
use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\CategoriaProdSuc;
use App\Models\ClaseProd;
use App\Models\GrupoProd;
use App\Models\Sucursal;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaProdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-categoriaprod');
        $datas = CategoriaProd::orderBy('id')->get();
        return view('categoriaprod.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-categoriaprod');
        $sucursales = Sucursal::orderBy('id')->pluck('nombre', 'id')->toArray();
        $areaproduccions = AreaProduccion::orderBy('id')->pluck('nombre', 'id')->toArray();
        $unidadmedidas = UnidadMedida::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $aux_sta=1;
        $aux_cont=0;
        $aux_contG=0;
        return view('categoriaprod.crear',compact('sucursales','aux_sta','aux_cont','areaproduccions','unidadmedidas','aux_contG'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarCategoriaProd $request)
    {
        can('guardar-categoriaprod');
        for ($i=0; $i < count($request->cla_nombre) ; $i++){
            if(is_null($request->cla_nombre[$i])==false && is_null($request->cla_descripcion[$i])==false && is_null($request->cla_longitud[$i])==false){
                $array_claseprod[$i] = array(
                    'cla_nombre' => $request->cla_nombre[$i],
                    'cla_descripcion' => $request->cla_descripcion[$i],
                    'cla_longitud' => $request->cla_longitud[$i]
                );    
            }
        }
        for ($i=0; $i < count($request->gru_nombre) ; $i++){
            if(is_null($request->gru_nombre[$i])==false && is_null($request->gru_descripcion[$i])==false){
                $array_grupoprod[$i] = array(
                    'gru_nombre' => $request->gru_nombre[$i],
                    'gru_descripcion' => $request->gru_descripcion[$i]
                );    
            }
        }

        $categoriaprod = CategoriaProd::create($request->all());
        $categoriaprod->sucursales()->sync($request->sucursal_id);
        if(isset($array_claseprod)){
            $categoriaprod->claseprods()->createMany($array_claseprod);
        }
        if(isset($array_grupoprod)){
            $categoriaprod->grupoprods()->createMany($array_grupoprod);
        }

        return redirect('categoriaprod')->with('mensaje','Categoría creada con exito');
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
        can('editar-categoriaprod');
        $data = CategoriaProd::findOrFail($id);
        $sucursales = Sucursal::orderBy('id')->pluck('nombre', 'id')->toArray();
        $claseprods = $data->claseprods()->select(['id as cla_id','cla_nombre','cla_descripcion','cla_longitud'])->get();
        $aux_cont=(count($claseprods));
        $grupoprods = $data->grupoprods()->select(['id as gru_id','gru_nombre','gru_descripcion'])->get();
        $aux_contG=(count($grupoprods));
        $areaproduccions = AreaProduccion::orderBy('id')->pluck('nombre', 'id')->toArray();
        $unidadmedidas = UnidadMedida::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $aux_sta=2;
        return view('categoriaprod.editar', compact('data','sucursales','claseprods','aux_sta','aux_cont','areaproduccions','grupoprods','aux_contG','unidadmedidas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCategoriaProd $request, $id)
    {
        
        can('guardar-categoriaprod');
        $categoriaprod = CategoriaProd::findOrFail($id);
        $categoriaprod->update($request->all());
        $categoriaprod->sucursales()->sync($request->sucursal_id);
        //dd($request->cla_id);
        $auxcla=ClaseProd::where('categoriaprod_id',$id)->whereNotIn('id', $request->cla_id)->pluck('id')->toArray(); //->destroy();
        for ($i=0; $i < count($auxcla) ; $i++){
            ClaseProd::destroy($auxcla[$i]);
        }
        for ($i=0; $i < count($request->cla_nombre) ; $i++){
            if(is_null($request->cla_nombre[$i])==false && is_null($request->cla_descripcion[$i])==false && is_null($request->cla_longitud[$i])==false)
            {
                $array_claseprod[$i] = array(
                    'id' => $request->cla_id[$i],
                    'categoriaprod_id' => $id,
                    'cla_nombre' => $request->cla_nombre[$i],
                    'cla_descripcion' => $request->cla_descripcion[$i],
                    'cla_longitud' => $request->cla_longitud[$i]
                );
                DB::table('ClaseProd')->updateOrInsert(
                    ['id' => $request->cla_id[$i], 'categoriaprod_id' => $id],
                    [
                        'cla_nombre' => $request->cla_nombre[$i],
                        'cla_descripcion' => $request->cla_descripcion[$i],
                        'cla_longitud' => $request->cla_longitud[$i]
                    ]
                );    
            }
        }
        $auxgru=GrupoProd::where('categoriaprod_id',$id)->whereNotIn('id', $request->gru_id)->pluck('id')->toArray(); //->destroy();
        for ($i=0; $i < count($auxgru) ; $i++){
            GrupoProd::destroy($auxgru[$i]);
        }
        for ($i=0; $i < count($request->gru_nombre) ; $i++){
            if(is_null($request->gru_nombre[$i])==false && is_null($request->gru_descripcion[$i])==false)
            {
                $array_grupoprod[$i] = array(
                    'id' => $request->gru_id[$i],
                    'categoriaprod_id' => $id,
                    'gru_nombre' => $request->gru_nombre[$i],
                    'gru_descripcion' => $request->gru_descripcion[$i]
                );
                DB::table('GrupoProd')->updateOrInsert(
                    ['id' => $request->gru_id[$i], 'categoriaprod_id' => $id],
                    [
                        'gru_nombre' => $request->gru_nombre[$i],
                        'gru_descripcion' => $request->gru_descripcion[$i]
                    ]
                );    
            }
        }
        //dd($array_claseprod);
        //$categoriaprod->claseprods()->update($array_claseprod);
        return redirect('categoriaprod')->with('mensaje','Categoría actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request,$id)
    {
        can('eliminar-categoriaprod');
        if ($request->ajax()) {
            if (CategoriaProd::destroy($id)) {
                //Eliminar los hijos en categoriaprodsuc
                $categoriaprodsuc = CategoriaProdSuc::where('categoriaprod_id', '=', $id);
                $categoriaprodsuc->delete();
                //Eliminar los hijos en ClaseProd
                $claseprod = ClaseProd::where('categoriaprod_id', '=', $id);
                $claseprod->delete();
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
