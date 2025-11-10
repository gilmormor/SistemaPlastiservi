<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarOpDetRegProdTemp;
use App\Models\AcuerdoTecnico;
use App\Models\EtapaProd;
use App\Models\OpDet;
use App\Models\OpDetRegProdTemp;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpDetRegProdTempController extends Controller
{
public function etapaprod()
    {
        can('listar-registro-produccion');
        $data = EtapaProd::etapasProdxPersona();
        $aux_contEtapasProd = count($data);
        if($aux_contEtapasProd==0){
            return redirect()->route('inicio')->with('mensaje','No tiene etapas de produccion asignadas, consulte con el administrador del sistema.')->send();
        }
        if($aux_contEtapasProd==1){
            session(['etapaprod_id' => $data[0]->etapaprod_id]);
            return redirect()->route('opdetregprodtemp');
        }else{
            return redirect()->route('opdetregprodtemp_selecetapaprod');
        }
    }

    public function selecetapaprod(){
        can('listar-registro-produccion');
        return view('opdetregprodtemp.selecetapaprod');
    }
    public function selecetapaprodpage(){
        $datas = EtapaProd::etapasProdxPersona();
        return datatables($datas)->toJson();
    }

    public function setId($id)
    {
        session(['etapaprod_id' => $id]);
        return redirect()->route('opdetregprodtemp_index01');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-registro-produccion');
        $data = EtapaProd::etapasProdxPersona();
        $aux_contEtapasProd = count($data);
        if($aux_contEtapasProd==0){
            return redirect()->route('inicio')->with('mensaje','No tiene etapas de produccion asignadas, consulte con el administrador del sistema.')->send();
        }
        if($aux_contEtapasProd==1){
            session(['etapaprod_id' => $data[0]->etapaprod_id]);
            return redirect()->route('opdetregprodtemp_index01');
        }else{
            return redirect()->route('opdetregprodtemp_selecetapaprod');
        }

        //$datas = FormaPago::orderBy('id')->get();
        //return view('opdetregprodtemp.index');
    }

    public function index01()
    {
        can('listar-registro-produccion');
        //dd(session('etapaprod_id'));
        //$datas = FormaPago::orderBy('id')->get();
        $tablas["etapaprod"] = EtapaProd::findOrFail(session('etapaprod_id'));
        return view('opdetregprodtemp.index', compact('tablas'));
    }

    public function opdetregprodtemppage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_etapaprod_id = session('etapaprod_id');
        //dd($aux_etapaprod_id);

        $aux_statusaprob = "(opdetregprodtemp.aprobstatus in (0,3) or ISNULL(opdetregprodtemp.aprobstatus))";
        
        $sql = "SELECT opdetregprodtemp.id,opdetregprodtemp.etapaprod_id,opdet.op_id,opdetregprodtemp.opdet_id,
                sucursal.nombre AS sucursal_nombre,
                cliente.razonsocial,
                opdet.kg as opdet_kg,opdet.cant as opdet_cant,opdet.cantrec as opdet_cantrec,
                opdet.kgrec as opdet_kgrec,opdet.cantprod as opdet_cantprod,
                opdet.kgprod as opdet_kgprod,opdet.kgscrap as opdet_kgscrap,
                opdetregprodtemp.kg,opdetregprodtemp.kgscrap,opdetregprodtemp.cant,
                (opdet.kgrec - (opdet.kgprod + opdetregprodtemp.kg + opdetregprodtemp.kgscrap)) as kgsaldo,
                opdetregprodtemp.aprobstatus,opdetregprodtemp.aprobobs,
                usuario.nombre AS usuario_nombre,operario.nombre AS operario_nombre,
                opdetregprodtemp.updated_at,
                UNIX_TIMESTAMP(opdetregprodtemp.updated_at) as updatednum_at
                from opdetregprodtemp INNER JOIN sucursal 
                ON opdetregprodtemp.sucursal_id=sucursal.id
                INNER JOIN producto
                ON opdetregprodtemp.producto_id=producto.id
                INNER JOIN etapaprod
                ON opdetregprodtemp.etapaprod_id=etapaprod.id 
                INNER JOIN opdet
                ON opdetregprodtemp.opdet_id=opdet.id
                INNER JOIN op
                ON opdet.op_id=op.id
                INNER JOIN otdet
                ON op.otdet_id=otdet.id
                INNER JOIN ot
                ON otdet.ot_id=ot.id
                INNER JOIN cliente
                ON ot.cliente_id=cliente.id
                INNER JOIN usuario
                ON opdetregprodtemp.usuario_id=usuario.id
                INNER JOIN operario
                ON opdetregprodtemp.operario_id=operario.id
                where opdetregprodtemp.sucursal_id IN ($sucurcadena) 
                AND opdetregprodtemp.etapaprod_id=$aux_etapaprod_id
                AND $aux_statusaprob
                AND isnull(opdetregprodtemp.deleted_at)
                ORDER BY opdetregprodtemp.id desc;";
        //dd($sql);
        $datas = DB::select($sql);
        //dd($datas);
        return datatables($datas)->toJson();
    }

    public function listaropdet()
    {
        $fechaAct = date("d/m/Y");
        $user = Usuario::findOrFail(auth()->id());
        $tablashtml['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablashtml['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablashtml['sucurArray'])->get();
        $tablashtml["etapaprod"] = EtapaProd::findOrFail(session('etapaprod_id'));
        return view('opdetregprodtemp.listaropdet', compact('fechaAct','tablashtml'));
    }
    public function listaropdetpage(Request $request){
        $datas = consultaopdet($request);
        return datatables($datas)->toJson();
    }

    public function crearini($id, $updatednum_at)
    {
        can('crear-registro-produccion');
        session(['opdet_id' => $id]);
        session(['opdet_updatednum_at' => $updatednum_at]);
        session()->save();
        return redirect()->route('crear_opdetregprodtemp');
        //dd($updated_at);
        return view('opdetregprodtemp.crear');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-registro-produccion');
        //dd(session('opdet_id'));
        $opdet_id = session('opdet_id');
        $updatednum_at = session('opdet_updatednum_at');
        $opdet = OpDet::findOrFail($opdet_id);
        //dd($opdet->areaproduccionsucetapaprod->etapaprod->nombre);
        if(strtotime($opdet->updated_at) != $updatednum_at){
            return redirect('opdetregprodtemp/listaropdet')->with([
                'mensaje'=>'Registro Editado por otro usuario. Fecha Hora: '.$opdet->updated_at,
                'tipo_alert' => 'alert-error'
            ]);    
        }
        $tablas["operarios"] = consultaOperarios($opdet->areaproduccionsucetapaprod->etapaprod_id);
        //dd($tablas["operarios"]);
        return view('opdetregprodtemp.crear', compact('opdet','tablas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarOpDetRegProdTemp $request)
    {
        can('guardar-registro-produccion');
        $opdet = OpDet::findOrFail($request->opdet_id);
        if(strtotime($opdet->updated_at) != $request->guardar_updatednum_at){
            return redirect('opdetregprodtemp/listaropdet')->with([
                'mensaje'=>'Registro Editado por otro usuario. Fecha Hora: '.$opdet->updated_at,
                'tipo_alert' => 'alert-error'
            ]);
        }
        $kgProduccion_temp = $opdet->totales_pendientes->total_kg + $opdet->totales_pendientes->total_kgscrap;
        $saldokg_temp = $opdet->saldokg - $kgProduccion_temp;
        if($saldokg_temp < ($request->kg + $request->kgscrap)){
            return redirect('opdetregprodtemp/listaropdet')->with([
                'mensaje'=>'La cantidad de Kg a registrar excede el saldo disponible en la Orden de Producción Detalle. Kg Saldo Disponible: '.number_format($saldokg_temp, 2, ',', '.'),
                'tipo_alert' => 'alert-error'
            ]);
        }
        DB::beginTransaction();
        try {
            $opdetregprodtemp = OpDetRegProdTemp::create($request->all());
            DB::commit();
            return redirect()->route('opdetregprodtemp_index01')->with('mensaje','Registro de Produccion creado con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('opdetregprodtemp/listaropdet')->with([
                'mensaje'=>"Error: " . $e->getMessage(),
                'tipo_alert' => 'alert-error'
            ]);
        }
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
    public function editar($cadena)
    {
        can('editar-registro-produccion');
        //dd($cadena);
        // Separamos por el carácter "&"
        list($id, $updatednum_at) = explode("&", $cadena);
        //$data = OpDetRegProdTemp::findOrFail($id);
        $data = OpDetRegProdTemp::withTrashed()->find($id);    
        if (!$data) {
            return redirect('opdetregprodtemp/index01')->with([
                'mensaje' => 'El registro no existe.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        if ($data->deleted_at) {
            return redirect('opdetregprodtemp/index01')->with([
                'mensaje' => 'El registro fue eliminado por otro usuario.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        if(strtotime($data->updated_at) != $updatednum_at){
            return redirect('opdetregprodtemp/index01')->with([
                'mensaje'=>'Registro Editado por otro usuario. Fecha Hora: '.$data->updated_at,
                'tipo_alert' => 'alert-error'
            ]);
        }
        //dd($id);
        /* $opdet_id = session('opdet_id');
        $updatednum_at = session('opdet_updatednum_at'); */
        $opdet = OpDet::findOrFail($data->opdet_id);
        $tablas["operarios"] = consultaOperarios($data->etapaprod_id);
        return view('opdetregprodtemp.editar', compact('data','opdet','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarOpDetRegProdTemp $request, $id)
    {
        //$opdetregprodtemp = OpDetRegProdTemp::findOrFail($id);
        $opdetregprodtemp = OpDetRegProdTemp::withTrashed()->find($id);    
        if (!$opdetregprodtemp) {
            return redirect('opdetregprodtemp/index01')->with([
                'mensaje'=>'El registro no existe',
                'tipo_alert' => 'alert-error'
            ]);
        }
        if ($opdetregprodtemp->deleted_at) {
            return redirect('opdetregprodtemp/index01')->with([
                'mensaje'=>'El registro fue eliminado por otro usuario.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        if(strtotime($opdetregprodtemp->updated_at) != $request->editar_updatednum_at){
            return redirect('opdetregprodtemp/index01')->with([
                'mensaje'=>'Registro Editado por otro usuario. Fecha Hora: '.$opdetregprodtemp->updated_at,
                'tipo_alert' => 'alert-error'
            ]);
        }
        DB::beginTransaction();
        try {
            $opdetregprodtemp->update($request->all());
            DB::commit();
            return redirect()->route('opdetregprodtemp_index01')->with('mensaje','Registro de produccion actualizado con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('opdetregprodtemp_index01')->with([
                'mensaje'=>"Error: " . $e->getMessage(),
                'tipo_alert' => 'alert-error'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-registro-produccion',false)){
            //dd($request);
            if ($request->ajax()) {
                $opdetregprodtemp = OpDetRegProdTemp::withTrashed()->find($request->id);    
                if (!$opdetregprodtemp) {
                    return response()->json([
                        'id' => 0,
                        'mensaje' => 'El registro no existe.',
                        'tipo_alert' => 'error'
                    ]);
                }

                if ($opdetregprodtemp->deleted_at) {
                    return response()->json([
                        'id' => 0,
                        'mensaje' => 'El registro fue eliminado por otro usuario.',
                        'tipo_alert' => 'error'
                    ]);
                }
                
                if(strtotime($opdetregprodtemp->updated_at) != $request->updated_at){
                    return response()->json([
                        'id' => 1,
                        'mensaje' => "Registro no se pudo eliminar, fue modificado por otro usuario.",
                        'tipo_alert' => "error"
                    ]);
                }

                $aux_regAso = false;
                $aux_tabla = [];
                /* if(count($opdetregprodtemp->areaproduccion) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Orden de Produccion";
                } */
                if($aux_regAso){
                    return response()->json([
                        'id' => 1,
                        'mensaje' => "No se puede eliminar, tiene registros asociados a la tabla: " . implode(", ", $aux_tabla) . ".",
                        'tipo_alert' => "error"
                    ]);
                }
                DB::beginTransaction();
                try {
                    OpDetRegProdTemp::destroy($request->id);
                    $opdetregprodtemp = OpDetRegProdTemp::withTrashed()->findOrFail($request->id);
                    $opdetregprodtemp->usuariodel_id = auth()->id();
                    $opdetregprodtemp->save();
                    DB::commit();
                    return response()->json(['mensaje' => 'ok']);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'mensaje'=>"Error: " . $e->getMessage(),
                        'tipo_alert' => 'alert-error'
                    ]);
                }
            } else {
                abort(404);
            }
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }

    public function enviaraprob(Request $request)
    {
        if ($request->ajax()) {
            $opdetregprodtemp = OpDetRegProdTemp::withTrashed()->find($request->id);    
            if (!$opdetregprodtemp) {
                return response()->json([
                    'id' => 0,
                    'mensaje' => 'El registro no existe.',
                    'tipo_alert' => 'error'
                ]);
            }

            if ($opdetregprodtemp->deleted_at) {
                return response()->json([
                    'id' => 0,
                    'mensaje' => 'El registro fue eliminado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            if(strtotime($opdetregprodtemp->updated_at) != $request->updated_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }

            $opdetregprodtemp->aprobstatus = 1;
            $opdetregprodtemp->aprobusu_id = auth()->id();
            $opdetregprodtemp->aprobfechahora = date("Y-m-d H:i:s");

            DB::beginTransaction();
            try {
                $opdetregprodtemp->save();
                DB::commit();
                return response()->json([
                    'mensaje' => 'ok',
                    'id' => $request->id,
                    'nfila' => $request->nfila,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'mensaje'=>"Error: " . $e->getMessage(),
                    'tipo_alert' => 'alert-error'
                ]);
            }

        }
    }
}

function consultaopdet($request){
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);
    $aux_etapaprod_id = session('etapaprod_id');

    if(!isset($request->sucursal_id) or empty($request->sucursal_id)){
        $aux_condsucursal_id = " true ";
    }else{
        if(is_array($request->sucursal_id)){
            $aux_sucursal = implode ( ',' , $request->sucursal_id);
        }else{
            $aux_sucursal = $request->sucursal_id;
        }
        $sucurArray = implode ( ',' , $user->sucursales->pluck('id')->toArray());
        $aux_condsucursal_id = " (ot.sucursal_id in ($aux_sucursal) and ot.sucursal_id in ($sucurArray))";
    }

    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "opdet.fechahora>='$fechad' and opdet.fechahora<='$fechah'";
    }
    if(empty($request->rut)){
        $aux_condrut = " true";
    }else{
        $aux_condrut = "cliente.rut='$request->rut'";
    }
    if(empty($request->oc_id)){
        $aux_condoc_id = " true";
    }else{
        $aux_condoc_id = "notaventa.oc_id='$request->oc_id'";
    }
    if(empty($request->notaventa_id)){
        $aux_condnotaventa_id = " true";
    }else{
        $aux_condnotaventa_id = "otnotaventa.notaventa_id='$request->notaventa_id'";
    }

    $aux_condproducto_id = " true";
    if(!empty($request->producto_id)){
        /*
        $aux_condproducto_id = str_replace(".","",$request->producto_id);
        $aux_condproducto_id = str_replace("-","",$aux_condproducto_id);
        $aux_condproducto_id = "notaventadetalle.producto_id='$aux_condproducto_id'";
        */

        $aux_codprod = explode(",", $request->producto_id);
        $aux_codprod = implode ( ',' , $aux_codprod);
        $aux_condproducto_id = "otdet.producto_id in ($aux_codprod)";
    }

    $aux_condmodulo_id = "";
    if(isset($request->modulo_id)){
        $aux_condmodulo_id = " and clientedesbloqueadomodulo.modulo_id = $request->modulo_id";
    }

    $sql = "SELECT opdet.id,op.id AS op_id,ot.id AS ot_id,otdet.id AS otdet_id,
            otnotaventa.notaventa_id,opdet.created_at,etapaprod.nombre AS etapaprod_nombre,cliente.razonsocial ,opdet.obs,maquina.nombre AS maquina_nombre,
            acuerdotecnico.id as acuerdotecnico_id,otdet.producto_id as producto_id,
			opdet.kg,opdet.cant,opdet.cantrec,opdet.kgrec,opdet.cantprod,opdet.kgprod,opdet.kgscrap,opdet.mtslineal,
            opdet.saldokg,
            ot.cliente_id,cliente.limitecredito,
            IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
            IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
            IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
            IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
            clientedesbloqueado.obs as clientedesbloqueado_obs,
            opdet.updated_at,UNIX_TIMESTAMP(opdet.updated_at) as updatednum_at
            from opdet INNER JOIN op
            ON opdet.op_id=op.id
            INNER JOIN otdet
            ON op.otdet_id=otdet.id
            INNER JOIN ot
            ON otdet.ot_id=ot.id
            INNER JOIN areaproduccionsucetapaprod
            ON areaproduccionsucetapaprod.id = opdet.apsucetapaprod_id
            INNER JOIN cliente
            ON cliente.id = ot.cliente_id
            INNER JOIN etapaprod
            ON etapaprod.id = areaproduccionsucetapaprod.etapaprod_id
            LEFT JOIN otnotaventa
            ON otnotaventa.ot_id = ot.id AND ISNULL(otnotaventa.deleted_at)
            LEFT JOIN opdetmaquina
            ON opdetmaquina.opdet_id = opdet.id
            LEFT JOIN maquina
            ON maquina.id = opdetmaquina.maquina_id
            LEFT JOIN vista_datacobranza
            ON vista_datacobranza.cliente_id = ot.cliente_id

            LEFT JOIN clientedesbloqueado
            ON clientedesbloqueado.cliente_id = ot.cliente_id and clientedesbloqueado.notaventa_id = otnotaventa.notaventa_id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
            LEFT JOIN clientedesbloqueadomodulo
            ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id $aux_condmodulo_id
            LEFT JOIN modulo
            ON modulo.id = clientedesbloqueadomodulo.modulo_id
            LEFT JOIN clientedesbloqueadopro
            ON clientedesbloqueadopro.cliente_id = ot.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
            
            LEFT JOIN clientedesbloqueado as clientedesbloqueado_orddesp
            ON clientedesbloqueado_orddesp.cliente_id = ot.cliente_id and clientedesbloqueado_orddesp.notaventa_id = otnotaventa.notaventa_id and not isnull(clientedesbloqueado_orddesp.notaventa_id) and isnull(clientedesbloqueado_orddesp.deleted_at)
            LEFT JOIN clientedesbloqueadomodulo as clientedesbloqueadomodulo_orddesp
            ON clientedesbloqueadomodulo_orddesp.clientedesbloqueado_id = clientedesbloqueado_orddesp.id and clientedesbloqueadomodulo_orddesp.modulo_id = 7
            LEFT JOIN acuerdotecnico
            ON acuerdotecnico.producto_id = otdet.producto_id AND ISNULL(acuerdotecnico.deleted_at)
            where ot.sucursal_id IN ($sucurcadena) 
            AND areaproduccionsucetapaprod.etapaprod_id=$aux_etapaprod_id
            AND $aux_condsucursal_id
            AND $aux_condFecha
            AND $aux_condrut
            AND $aux_condnotaventa_id
            AND $aux_condproducto_id
            AND opdet.kgrec > 0 AND (opdet.saldokg > 0)
            AND opdet.id not in (SELECT opdetcerr.opdet_id from opdetcerr WHERE  isnull(opdetcerr.deleted_at))
            AND isnull(opdet.deleted_at);";
    //dd($sql);

    $datas = DB::select($sql);
    $resultado = [];
    //dd($datas);
    foreach ($datas as &$data) {
        $acuerdotecnico = AcuerdoTecnico::findOrFail($data->acuerdotecnico_id);
        $data->nombre_producto = $acuerdotecnico->nombre_producto;
        $opdet = OpDet::findOrFail($data->id);
        $pendientes = $opdet->totales_pendientes;
        //dd($data);

        $data->cantprod += $pendientes->total_cant;
        $data->kgprod += $pendientes->total_kg;
        $data->kgscrap += $pendientes->total_kgscrap;
        $data->mtslineal += $pendientes->total_mtslineal;
        $data->saldokg -= ($pendientes->total_kg + $pendientes->total_kgscrap);
        if($data->saldokg > 0)
            $resultado[] = $data;
        //dd($opdet->opdetregprodtemps);
        //$data->operarios = $opdet->operariosAsignados();
    }
    $datas = $resultado;
    filtrarclientesbloqueados($request,$datas);
    return $datas;    
}

function consultaOperarios($etapaprod_id){
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);
    $aux_etapaprod_id = $etapaprod_id;
    $sql = "SELECT operario.id,operario.nombre,sucursal.nombre as sucursal_nombre,
            GROUP_CONCAT(DISTINCT concat(sucursal.nombre, '/' ,areaproduccion.nombre,'/',etapaprod.nombre)) as areaproduccion_nombre
            from operario LEFT JOIN operario_areaproduccionsucep
            ON operario.id = operario_areaproduccionsucep.operario_id
            LEFT JOIN areaproduccionsucetapaprod
            ON operario_areaproduccionsucep.areaproduccionsucep_id = areaproduccionsucetapaprod.id
            LEFT JOIN areaproduccionsuc
            ON areaproduccionsucetapaprod.areaproduccionsuc_id = areaproduccionsuc.id
            LEFT JOIN etapaprod
            ON etapaprod.id = areaproduccionsucetapaprod.etapaprod_id
            LEFT JOIN sucursal
            ON sucursal.id = areaproduccionsuc.sucursal_id
            LEFT JOIN areaproduccion
            ON areaproduccion.id = areaproduccionsuc.areaproduccion_id
            where operario.activo=1
            AND isnull(operario.deleted_at)
            AND sucursal.id in ($sucurcadena)
            AND etapaprod.id = $aux_etapaprod_id
            GROUP BY operario.id;";

    return DB::select($sql);
}