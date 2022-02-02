<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarSucursal;
use App\Models\Area;
use App\Models\CategoriaProd;
use App\Models\Comuna;
use App\Models\Provincia;
use App\Models\Region;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-sucursal');
        $datas = Sucursal::orderBy('id')->get();
        return view('sucursal.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-sucursal');
        $regiones = Region::orderBy('id')->get();
        $areas = Area::orderBy('id')->pluck('nombre', 'id')->toArray();
        $aux_sta=1;
        return view('sucursal.crear',compact('regiones','areas','aux_sta'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarSucursal $request)
    {
        //dd($request);
        can('guardar-sucursal');
        $sucursal = Sucursal::create($request->all());
        $sucursal->areas()->sync($request->area_id);
        return redirect('sucursal')->with('mensaje','Sucursal creada con exito');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function mostrar($id)
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
        can('editar-sucursal');
        $data = Sucursal::findOrFail($id);
        $regiones = Region::orderBy('id')->get();
        $provincias = Provincia::where('region_id',$data->region_id)->orderBy('id')->get();
        $comunas = Comuna::where('provincia_id',$data->provincia_id)->orderBy('id')->get();
        $areas = Area::orderBy('id')->pluck('nombre', 'id')->toArray();
        $aux_sta=2;
        return view('sucursal.editar', compact('data','regiones','provincias','comunas','areas','aux_sta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarSucursal $request, $id)
    {
        //dd($request->area_id);
        can('guardar-sucursal');
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->update($request->all());
        $sucursal->areas()->sync($request->area_id);
        return redirect('sucursal')->with('mensaje','Sucursal actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('eliminar-sucursal');
        if ($request->ajax()) {
            if (Sucursal::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function obtProvincias(Request $request)
    {
        if($request->ajax()){
            $provincias = Provincia::where('region_id', $request->region_id)->get();
            foreach($provincias as $provincia){
                $provinciasArray[$provincia->id] = $provincia->nombre;
            }
            //dd($provinciasArray);
            return response()->json($provinciasArray);
        }
    }
    public function obtComunas(Request $request)
    {
        if($request->ajax()){
            $comunas = Comuna::where('provincia_id', $request->provincia_id)->get();
            foreach($comunas as $comuna){
                $comunasArray[$comuna->id] = $comuna->nombre;
            }
            //dd($provinciasArray);
            return response()->json($comunasArray);
        }
    }

    public function obtsucursalescategoriaprod(Request $request){
        $categoriaprod = CategoriaProd::findOrFail($request->categoriaprod_id);
        $sucurcatprodarray=$categoriaprod->sucursales->pluck('id')->toArray();
        /*
        $array_excluirid = json_decode($request->array_excluirid);
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        */
        $datas = Sucursal::whereIn('sucursal.id', $sucurcatprodarray)
                    //->whereNotIn('sucursal.id', $array_excluirid)
                    ->get();

        //$datas = CategoriaProd::catxUsuCostoAnnoMes($request);
        return $datas; //response()->json($data)
    }

}
