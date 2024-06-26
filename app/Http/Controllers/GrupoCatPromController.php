<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarGrupoCatProm;
use App\Models\GrupoCatProm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoCatPromController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-grupo-cat-prom');
        //$datas = FormaPago::orderBy('id')->get();
        return view('grupocatprom.index');
    }

    public function grupocatprompage(){
        $sql = "SELECT grupocatprom.id,grupocatprom.nombre,
        GROUP_CONCAT(DISTINCT categoriaprod.nombre ORDER BY categoriaprod.id) AS categoriaprod_nombre
        from grupocatprom inner join grupocatpromcategoriaprod
        on grupocatprom.id = grupocatpromcategoriaprod.grupocatprom_id
        INNER JOIN categoriaprod
        ON categoriaprod.id = grupocatpromcategoriaprod.categoriaprod_id
        where isnull(grupocatprom.deleted_at) 
        GROUP BY grupocatprom.id";

        $datas =  DB::select($sql);
        return datatables($datas)->toJson();
        return datatables()
            ->eloquent(GrupoCatProm::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-grupo-cat-prom');
        $sql = "SELECT categoriaprod.id,categoriaprod.nombre
                FROM categoriaprod
                where categoriaprod.id not in (SELECT categoriaprod_id 
                                                FROM grupocatpromcategoriaprod)
                AND isnull(categoriaprod.deleted_at);";
        $categoriaprods = DB::select($sql);
        return view('grupocatprom.crear',compact('categoriaprods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarGrupoCatProm $request)
    {
        can('guardar-grupo-cat-prom');
        $grupocatprom = GrupoCatProm::create($request->all());
        $grupocatprom->categoriaprods()->sync($request->categoriaprod_id);
        return redirect('grupocatprom')->with('mensaje','GrupoCatProm creado con exito');
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
        can('editar-grupo-cat-prom');
        $data = GrupoCatProm::findOrFail($id);
        $sql = "SELECT categoriaprod.id,categoriaprod.nombre
                FROM categoriaprod
                where categoriaprod.id not in (SELECT categoriaprod_id 
                                                FROM grupocatpromcategoriaprod
                                                WHERE grupocatprom_id != $id)
                AND isnull(categoriaprod.deleted_at);";
        $categoriaprods = DB::select($sql);
        return view('grupocatprom.editar', compact('data','categoriaprods'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarGrupoCatProm $request, $id)
    {
        can('guardar-grupo-cat-prom');
        $grupocatprom = GrupoCatProm::findOrFail($id);
        $grupocatprom->update($request->all());
        $grupocatprom->categoriaprods()->sync($request->categoriaprod_id);
        return redirect('grupocatprom')->with('mensaje','GrupoCatProm actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-grupo-cat-prom',false)){
            if ($request->ajax()) {
                if (GrupoCatProm::destroy($request->id)) {
                    $grupocatprom = GrupoCatProm::withTrashed()->findOrFail($request->id);
                    $grupocatprom->usuariodel_id = auth()->id();
                    $grupocatprom->save();
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            } else {
                abort(404);
            }
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }

    public function arraygrupocatprom(){
        $datas = GrupoCatProm::arraygrupocatprom();
        return datatables($datas)->toJson();
        return datatables()
            ->eloquent(GrupoCatProm::query())
            ->toJson();
    }

}