<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarBancoTipoCta;
use App\Models\BancoTipoCta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BancoTipoCtaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-banco-tipo-cta');
        return view('bancotipocta.index');
    }

    public function bancotipoctapage(){
        return datatables()
            ->eloquent(BancoTipoCta::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-banco-tipo-cta');
        return view('bancotipocta.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarBancoTipoCta $request)
    {
        can('guardar-banco-tipo-cta');
        $request->merge(['usuario_id' => auth()->id()]);
        BancoTipoCta::create($request->all());
        return redirect('bancotipocta')->with('mensaje','Registro creado con exito');
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
        can('editar-banco-tipo-cta');
        $data = BancoTipoCta::findOrFail($id);
        return view('bancotipocta.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarBancoTipoCta $request, $id)
    {
        BancoTipoCta::findOrFail($id)->update($request->all());
        return redirect('bancotipocta')->with('mensaje','Registro actualizado con exito');
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
            if (BancoTipoCta::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
        */
        if(can('eliminar-banco-tipo-cta',false)){
            if ($request->ajax()) {
                $aux_contRegistos = 0;
                $data = BancoTipoCta::findOrFail($request->id);
                $sql = "SELECT COUNT(*) as cont
                        FROM banco
                        WHERE bancotipocta_id = $request->id 
                        AND deleted_at is null;";
                $datacont = DB::select($sql);
                if($datacont){
                    $aux_contRegistos += $datacont[0]->cont;
                }
                /* $sql = "SELECT COUNT(*) as cont
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
                    if (BancoTipoCta::destroy($request->id)) {
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $banco = BancoTipoCta::withTrashed()->findOrFail($request->id);
                        $banco->usuariodel_id = auth()->id();
                        $banco->save();
                        /* $AcuerdoTecCertificado = AcuerdoTecBancoTipoCta::where('banco_id', '=', $request->id);
                        $AcuerdoTecCertificado->delete();
                        $NoConformidad_Certificado = NoConformidad_BancoTipoCta::where('banco_id', '=', $request->id);
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
