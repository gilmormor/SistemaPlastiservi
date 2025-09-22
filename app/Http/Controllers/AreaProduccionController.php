<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarAreaProduccion;
use App\Models\AreaProduccion;
use App\Models\AreaProduccionEtapaProd;
use App\Models\EtapaProd;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaProduccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-areaproduccion');
        $datas = AreaProduccion::orderBy('id')->get();
        return view('areaproduccion.index', compact('datas'));

    }

    public function areaproduccionpage(){    
        $sql = "SELECT areaproduccion.id,areaproduccion.nombre
        from areaproduccion
        where isnull(areaproduccion.deleted_at);";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
    }
    
    public function areaproduccionetapaprodpage(Request $request){
        //dd($request);
        $aux_areaproduccion_idCond = " true ";
        if(isset($request->areaproduccion_id) and $request->areaproduccion_id >= 0){
            $aux_areaproduccion_idCond = "areaproduccion.id = $request->areaproduccion_id";
        }
        $sql = "SELECT areaproduccionetapaprod.id,etapaprod.nombre,
            areaproduccionetapaprod.orden,
            UNIX_TIMESTAMP(areaproduccionetapaprod.updated_at) as updatednum_at,
            areaproduccionetapaprod.updated_at
        from areaproduccion INNER JOIN areaproduccionetapaprod
        ON areaproduccionetapaprod.areaproduccion_id = areaproduccion.id
        INNER JOIN etapaprod
        ON areaproduccionetapaprod.etapaprod_id = etapaprod.id
        where $aux_areaproduccion_idCond
        AND isnull(areaproduccion.deleted_at);";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-areaproduccion');
        $tablas = array();
        $tablas['sucursales'] = Sucursal::orderBy('id')->get();
        $tablas['etapaprods'] = EtapaProd::orderBy('id')->get();
        $tablas['aux_cont'] = 0;
        $tablas['aux_sta'] = 1;
        return view('areaproduccion.crear', compact('tablas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarAreaProduccion $request)
    {
        can('guardar-areaproduccion');
        $areaproduccion = AreaProduccion::create($request->all());
        $areaproduccion->sucursales()->sync($request->sucursal_id);
        $areaproduccion->etapaprods()->sync($request->etapaprod_id);
        return redirect('areaproduccion')->with('mensaje','AreaProduccion creado con exito');
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
        can('editar-areaproduccion');
        $data = AreaProduccion::findOrFail($id);
        $tablas = array();
        $tablas['sucursales'] = Sucursal::orderBy('id')->get();
        $tablas['etapaprods'] = EtapaProd::orderBy('id')->get();
        $tablas['aux_sta'] = 2;
        return view('areaproduccion.editar', compact('data','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarAreaProduccion $request, $id)
    {
        can('guardar-areaproduccion');
        $areaproduccion = AreaProduccion::findOrFail($id);
        $areaproduccion->update($request->all());
        $areaproduccion->sucursales()->sync($request->sucursal_id);
        $areaproduccion->etapaprods()->sync($request->etapaprod_id);
        return redirect('areaproduccion')->with('mensaje','AreaProduccion actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if ($request->ajax()) {
            if (AreaProduccion::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }    
        } else {
            abort(404);
        }
    }

    public function guardarordenetapaprod(Request $request)
    {
        can('guardar-areaproduccion');
        //dd($request);
        $AreaProduccionEtapaProd = AreaProduccionEtapaProd::findOrFail($request->areaproduccionetapaprod_id);
        if(strtotime($AreaProduccionEtapaProd->updated_at) != $request->updatednum_at){
            return response()->json([
                'id' => 0,
                'mensaje'=>'Registro no pudo ser Actualizado. Registro Editado por otro usuario. Fecha Hora: '.$AreaProduccionEtapaProd->updated_at,
                'tipo_alert' => 'error'
            ]);    
        }

        $AreaProduccionEtapaProd->orden = $request->orden;
        $AreaProduccionEtapaProd->updated_at = date("Y-m-d H:i:s");
        if($AreaProduccionEtapaProd->save()){
            return response()->json([
                'id' => $AreaProduccionEtapaProd->id,
                'error'=>'0',
                'mensaje'=>'Registro guardo con exito.',
                'updated_at' => date('Y-m-d H:i:s', strtotime($AreaProduccionEtapaProd->updated_at)),
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
        dd($AreaProduccionEtapaProd);
        if(count($despachosol->notaventa->notaventacerradas) == 0){
            foreach ($despachosol->notaventa->cliente->clientebloqueados as $clientebloqueado) {
                return [
                    'mensaje'=>'Registro no fue guardado. Condición financiera en revisión: ' . $clientebloqueado->descripcion ,
                    'tipo_alert' => 'error'
                ];
            }
            /*
            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$despachosol->notaventa->cliente_id)->get();
            if(count($clibloq) > 0){
                return [
                    'mensaje'=>'Registro no fue guardado. Condición financiera en revisión: ' . $clibloq[0]->descripcion ,
                    'tipo_alert' => 'error'
                ];
            }
            */
            if($despachosol->updated_at == $request->updated_at){
                $dateInput = explode('/',$request->aux_fechaestdesp);
                $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];  
                $despachosol->fechaestdesp = $request->fechaestdesp;
                if($despachosol->save()){
                    return response()->json([
                        'error'=>'0',
                        'mensaje'=>'Registro actualizado con exito.',
                        'fechaestdesp' => $despachosol->fechaestdesp,
                        'updated_at' => date('Y-m-d H:i:s', strtotime($despachosol->updated_at)),
                        'tipo_alert' => 'success'
                    ]);
                }else{
                    return response()->json([
                        'error'=>'1',
                        'mensaje'=>'Registro no fue actualizado.',
                        'tipo_alert' => 'error'
                    ]);
                }
            }else{
                return response()->json([
                    'error'=>'1',
                    'mensaje'=>'Registro modificado por otro usuario. Fecha Hora: '.$despachosol->updated_at,
                    'tipo_alert' => 'error'
                ]);
            }
        }else{
            return response()->json([
                'error'=>'1',
                'mensaje'=>'Registro no fue Modificado. La nota de venta fue Cerrada. Observ: ' . $despachosol->notaventa->id,
                'tipo_alert' => 'error'
            ]);
/*
            return redirect('despachosol')->with([
                'mensaje'=>'Registro no fue Modificado. La nota de venta fue Cerrada. Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at)),
                'tipo_alert' => 'alert-error'
            ]);*/
        }

    }
}
