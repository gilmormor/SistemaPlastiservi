<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarAreaProduccionSucEtapaProd;
use App\Http\Requests\ValidarAreaProduccionSucLinea;
use App\Http\Requests\ValidarEtapaProd;
use App\Models\AreaProduccionSuc;
use App\Models\AreaProduccionSucEtapaProd;
use App\Models\EtapaProd;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaProduccionSucEtapaProdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-area-produccion-suc-etapa-prod');
        //$datas = FormaPago::orderBy('id')->get();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        return view('areaproduccionsucetapaprod.index',compact('tablas'));
    }

    public function areaproduccionsucetapaprodpage(Request $request){
        //dd($request);
        $datas = AreaProduccionSuc::report($request);
        return datatables($datas)->toJson();
    }

    public function sucetapaprodpage(Request $request){
        //dd($request);
        $aux_areaproduccionsuc_idCond = " true ";
        if(isset($request->areaproduccionsuc_id) and $request->areaproduccionsuc_id >= 0){
            $aux_areaproduccionsuc_idCond = "areaproduccionsucetapaprod.areaproduccionsuc_id = $request->areaproduccionsuc_id";
        }
        $sql = "SELECT areaproduccionsucetapaprod.id,etapaprod.nombre as etapaprod_nombre,
            areaproduccionsucetapaprod.orden,
            UNIX_TIMESTAMP(areaproduccionsucetapaprod.updated_at) as updatednum_at,
            areaproduccionsucetapaprod.updated_at
        from areaproduccionsucetapaprod INNER JOIN etapaprod
        ON areaproduccionsucetapaprod.etapaprod_id = etapaprod.id
        where $aux_areaproduccionsuc_idCond;";
        $datas = DB::select($sql);
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
        can('editar-area-produccion-suc-etapa-prod');
        $data = AreaProduccionSuc::findOrFail($id);
        $tablas = array();
        $tablas['etapaprods'] = EtapaProd::orderBy('id')->get();
        $tablas['aux_sta'] = 2;
        return view('areaproduccionsucetapaprod.editar', compact('data','tablas'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarAreaProduccionSucEtapaProd $request, $id)
    {
        can('guardar-area-produccion-suc-etapa-prod');
        $areaproduccionsuc = AreaProduccionSuc::findOrFail($id);
        //dd($request->etapaprod_id);
        //$areaproduccion->update($request->all());

        DB::beginTransaction();

        try {
            $areaproduccionsuc->etapaprods()->sync($request->etapaprod_id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('areaproduccionsucetapaprod')->with([
                'mensaje'=> "Error: " . $e->getMessage(),
                'tipo_alert' => 'alert-error'
            ]);
        }
        return redirect('areaproduccionsucetapaprod')->with('mensaje','Area Produccion Sucursal actualizado con exito');
    }

    public function guardarordenetapaprod(Request $request)
    {
        can('guardar-area-produccion-suc-etapa-prod');
        //dd($request);
        $AreaProduccionSucEtapaProd = AreaProduccionSucEtapaProd::findOrFail($request->areaproduccionsucetapaprod_id);
        if(strtotime($AreaProduccionSucEtapaProd->updated_at) != $request->updatednum_at){
            return response()->json([
                'id' => 0,
                'mensaje'=>'Registro no pudo ser Actualizado. Registro Editado por otro usuario. Fecha Hora: '.$AreaProduccionSucEtapaProd->updated_at,
                'tipo_alert' => 'error'
            ]);    
        }

        $AreaProduccionSucEtapaProd->orden = $request->orden;
        $AreaProduccionSucEtapaProd->updated_at = date("Y-m-d H:i:s");
        if($AreaProduccionSucEtapaProd->save()){
            return response()->json([
                'id' => $AreaProduccionSucEtapaProd->id,
                'error'=>'0',
                'mensaje'=>'Registro guardo con exito.',
                'updated_at' => date('Y-m-d H:i:s', strtotime($AreaProduccionSucEtapaProd->updated_at)),
                'tipo_alert' => 'success'
            ]);
        } else {
            return response()->json([
                'id' => 0,
                'error'=>'0',
                'mensaje'=>'Ocurrio un error al intenta guardar.',
                'tipo_alert' => 'error'
            ]);
        }
    }
}
