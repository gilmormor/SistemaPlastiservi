<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProd;
use App\Models\CategoriaProd_Giro;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaProd_GiroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-categoriaprod-giro');
        return view('categoriaprod_giro.index');
    }

    public function categoriaprod_giropage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $arraySucFisxUsu = implode(",", sucFisXUsu($user->persona));
        $sql = "SELECT categoriaprod.id,categoriaprod.nombre,categoriaprod.descripcion,categoriaprod.precio,
                categoriaprod.areaproduccion_id,categoriaprod.sta_precioxkilo,categoriaprod.unidadmedida_id,
                categoriaprod.unidadmedidafact_id
                FROM categoriaprod INNER JOIN categoriaprodsuc
                ON categoriaprod.id=categoriaprodsuc.categoriaprod_id AND ISNULL(categoriaprod.deleted_at)
                WHERE categoriaprodsuc.sucursal_id IN ($sucurcadena)
                AND categoriaprodsuc.sucursal_id IN ($arraySucFisxUsu)
                GROUP BY categoriaprod.id;";
        $datas =  DB::select($sql);
        return datatables($datas)->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-categoriaprod-giro');
        $data = CategoriaProd::findOrFail($id);
        $sql = "SELECT categoriaprod_giro.id, giro.id AS giro_id,giro.nombre,if(ISNULL(categoriaprod_giro.preciokg),0,categoriaprod_giro.preciokg) AS preciokg
                FROM giro LEFT JOIN categoriaprod_giro
                ON giro.id = categoriaprod_giro.giro_id AND ISNULL(categoriaprod_giro.deleted_at)
                AND categoriaprod_giro.categoriaprod_id = $id
                WHERE giro.id > 0
                order by orden;";
        $giros =  DB::select($sql);
        //dd($giros);
        return view('categoriaprod_giro.editar', compact('data','giros'));
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
        can('guardar-categoriaprod-giro');
        //dd($request);
        $user = Usuario::findOrFail(auth()->id());
        for ($i=0; $i < count($request->giro_id); $i++) { 
            $categoriaprod_giro = CategoriaProd_Giro::updateOrCreate(
                ['categoriaprod_id' => $request->categoriaprod_id,'giro_id' => $request->giro_id[$i]],
                [
                    'categoriaprod_id' => $request->categoriaprod_id,
                    'giro_id' => $request->giro_id[$i],
                    'preciokg' => $request->preciokg[$i],
                    'usuario_id' => $user->id,
                ]
            );
    
        }


        return redirect('categoriaprod_giro')->with('mensaje','Categoría actualizado con exito');
    }

    public function categoriaprodArray(Request $request)
    {
        if($request->ajax()){
            $categoriasprod = CategoriaProd::categoriasxUsuario($request->sucursal_id);
            return response()->json($categoriasprod);
        }
    }
}
