<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarBanco;
use App\Models\Banco;
use App\Models\BancoTipoCta;
use Illuminate\Http\Request;

class BancoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-banco');
        return view('banco.index');
    }

    public function bancopage(){
        return datatables()
            ->eloquent(Banco::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-banco');
        $bancotipoctas = BancoTipoCta::orderBy('id')->get();
        return view('banco.crear',compact('bancotipoctas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarBanco $request)
    {
        can('guardar-banco');
        $request->merge(['usuario_id' => auth()->id()]);
        Banco::create($request->all());
        return redirect('banco')->with('mensaje','Registro creado con exito');
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
        can('editar-banco');
        $data = Banco::findOrFail($id);
        $bancotipoctas = BancoTipoCta::orderBy('id')->get();
        return view('banco.editar', compact('data','bancotipoctas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarBanco $request, $id)
    {
        Banco::findOrFail($id)->update($request->all());
        return redirect('banco')->with('mensaje','Registro actualizado con exito');
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
            if (Banco::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
        */
        if(can('eliminar-banco',false)){
            if ($request->ajax()) {
                $aux_contRegistos = 0;
                /* $data = Banco::findOrFail($request->id);
                $sql = "SELECT COUNT(*) as cont
                        FROM acuerdotectemp_banco
                        WHERE banco_id = $request->id 
                        AND deleted_at is null;";
                $datacont = DB::select($sql);
                if($datacont){
                    $aux_contRegistos += $datacont[0]->cont;
                }
                $sql = "SELECT COUNT(*) as cont
                        FROM noconformidad_banco
                        WHERE banco_id = $request->id 
                        AND deleted_at is null;";
                $datacont = DB::select($sql);
                if($datacont){
                    $aux_contRegistos += $datacont[0]->cont;
                } */
                if($aux_contRegistos > 0){
                    return response()->json(['mensaje' => 'cr']);
                }else{
                    if (Banco::destroy($request->id)) {
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $banco = Banco::withTrashed()->findOrFail($request->id);
                        $banco->usuariodel_id = auth()->id();
                        $banco->save();
                        /* $AcuerdoTecCertificado = AcuerdoTecBanco::where('banco_id', '=', $request->id);
                        $AcuerdoTecCertificado->delete();
                        $NoConformidad_Certificado = NoConformidad_Banco::where('banco_id', '=', $request->id);
                        $NoConformidad_Certificado->delete(); */
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
