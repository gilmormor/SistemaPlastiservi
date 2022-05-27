<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarInvMovTipo;
use App\Models\InvMovTipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvMovTipoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-tipo-movimiento-inventario');
        return view('invmovtipo.index');
    }

    public function invmovtipopage(){
        return datatables()
            ->eloquent(InvMovTipo::query())
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-tipo-movimiento-inventario');
        return view('invmovtipo.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarInvMovTipo $request)
    {
        can('guardar-tipo-movimiento-inventario');
        $request->request->add(['usuario_id' => auth()->id()]);
        InvMovTipo::create($request->all());
        return redirect('invmovtipo')->with('mensaje','Tipo Movimiento creado con exito');
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
        can('editar-tipo-movimiento-inventario');
        $data = InvMovTipo::findOrFail($id);
        return view('invmovtipo.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarInvMovTipo $request, $id)
    {
        $invmovtipo = InvMovTipo::findOrFail($id);
        InvMovTipo::findOrFail($id)->update($request->all());
        return redirect('invmovtipo')->with('mensaje','Tipo Movimiento actualizado con exito');
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
            if (Certificado::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
        */
        if(can('eliminar-invmovtipo',false)){
            can('eliminar-tipo-movimiento-inventario');
                /*
            if ($request->ajax()) {
                $data = InvMovTipo::findOrFail($request->id);
                $aux_contRegistos = 0;
                $sql = "SELECT COUNT(*) as cont
                        FROM acuerdotectemp_invmovtipo
                        WHERE invmovtipo_id = $request->id 
                        AND deleted_at is null;";
                $datacont = DB::select($sql);
                if($datacont){
                    $aux_contRegistos += $datacont[0]->cont;
                }
                $sql = "SELECT COUNT(*) as cont
                        FROM noconformidad_invmovtipo
                        WHERE invmovtipo_id = $request->id 
                        AND deleted_at is null;";
                $datacont = DB::select($sql);
                if($datacont){
                    $aux_contRegistos += $datacont[0]->cont;
                }
                if($aux_contRegistos > 0){
                    return response()->json(['mensaje' => 'cr']);
                }else{
                    if (InvMovTipo::destroy($request->id)) {
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $invmovtipo = InvMovTipo::withTrashed()->findOrFail($request->id);
                        $invmovtipo->usuariodel_id = auth()->id();
                        $invmovtipo->save();
                        $AcuerdoTecCertificado = AcuerdoTecCertificado::where('invmovtipo_id', '=', $request->id);
                        $AcuerdoTecCertificado->delete();
                        $NoConformidad_Certificado = NoConformidad_Certificado::where('invmovtipo_id', '=', $request->id);
                        $NoConformidad_Certificado->delete();
                        return response()->json(['mensaje' => 'ok']);
                    } else {
                        return response()->json(['mensaje' => 'ng']);
                    }    
                }
            } else {
                abort(404);
            }
            */
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }
}