<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarProduccionReg;
use App\Models\AcuerdoTecnico;
use App\Models\EtapaProd;
use App\Models\OpDet;
use App\Models\ProduccionReg;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduccionRegController extends Controller
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
            return redirect()->route('produccionreg');
        }else{
            return redirect()->route('produccionreg_selecetapaprod');
        }
    }

    public function selecetapaprod(){
        can('listar-registro-produccion');
        return view('produccionreg.selecetapaprod');
    }
    public function selecetapaprodpage(){
        $datas = EtapaProd::etapasProdxPersona();
        return datatables($datas)->toJson();
    }

    public function setId($id)
    {
        session(['etapaprod_id' => $id]);
        return redirect()->route('produccionreg_index01');
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
            return redirect()->route('produccionreg_index01');
        }else{
            return redirect()->route('produccionreg_selecetapaprod');
        }

        //$datas = FormaPago::orderBy('id')->get();
        //return view('produccionreg.index');
    }

    public function index01()
    {
        can('listar-registro-produccion');
        //dd(session('etapaprod_id'));
        //$datas = FormaPago::orderBy('id')->get();
        return view('produccionreg.index');
    }

    public function produccionregpage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_etapaprod_id = session('etapaprod_id');
        
        $sql = "SELECT produccionreg.id,opdet.op_id,produccionreg.opdet_id,
                sucursal.nombre AS sucursal_nombre
                from produccionreg INNER JOIN sucursal 
                ON produccionreg.sucursal_id=sucursal.id
                INNER JOIN producto
                ON produccionreg.producto_id=producto.id
                INNER JOIN etapaprod
                ON produccionreg.etapaprod_id=etapaprod.id 
                INNER JOIN opdet
                ON produccionreg.opdet_id=opdet.id
                where produccionreg.sucursal_id IN ($sucurcadena) 
                AND etapaprod.id=$aux_etapaprod_id
                AND isnull(produccionreg.deleted_at);";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
    }

    public function listaropdet()
    {
        $fechaAct = date("d/m/Y");
        $user = Usuario::findOrFail(auth()->id());
        $tablashtml['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablashtml['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablashtml['sucurArray'])->get();
        return view('produccionreg.listaropdet', compact('fechaAct','tablashtml'));
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
        return redirect()->route('crear_produccionreg');
        dd($updated_at);
        return view('produccionreg.crear');
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
            return redirect('produccionreg/listaropdet')->with([
                'mensaje'=>'Registro Editado por otro usuario. Fecha Hora: '.$opdet->updated_at,
                'tipo_alert' => 'alert-error'
            ]);    
        }
        return view('produccionreg.crear', compact('opdet'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarProduccionReg $request)
    {
        can('guardar-registro-produccion');
        //dd($request);
        $produccionreg = ProduccionReg::create($request->all());
        return redirect('produccionreg')->with('mensaje','Etapa Produccion creado con exito');
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
        can('editar-registro-produccion');
        dd($id);
        $data = ProduccionReg::findOrFail($id);
        return view('produccionreg.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarProduccionReg $request, $id)
    {
        $produccionreg = ProduccionReg::findOrFail($id);
        $produccionreg->update($request->all());
        return redirect('produccionreg')->with('mensaje','Etapa produccion actualizado con exito');
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
            if ($request->ajax()) {
                $produccionreg = ProduccionReg::findOrFail($request->id);
                $aux_regAso = false;
                $aux_tabla = [];
                if(count($produccionreg->areaproduccion) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Orden de Produccion";
                }
                if($aux_regAso){
                    return response()->json([
                        'id' => 1,
                        'mensaje' => "No se puede eliminar, tiene registros asociados a la tabla: " . implode(", ", $aux_tabla) . ".",
                        'tipo_alert' => "error"
                    ]);
                }
                if (ProduccionReg::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $produccionreg = ProduccionReg::withTrashed()->findOrFail($request->id);
                    $produccionreg->usuariodel_id = auth()->id();
                    $produccionreg->save();
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
}

function consulta($request,$aux_sql,$orden){
    //dd($request);

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

    $sql = "SELECT opdet.id,otnotaventa.notaventa_id,opdet.created_at,etapaprod.nombre AS etapaprod_nombre,cliente.razonsocial ,opdet.obs,maquina.nombre AS maquina_nombre,
            acuerdotecnico.id as acuerdotecnico_id,otdet.producto_id as producto_id,
			opdet.kg,opdet.cant,opdet.cantrec,opdet.kgrec,opdet.cantprod,opdet.kgprod,
            cliente.limitecredito,
            IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
            IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
            IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
            IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
            clientedesbloqueado.obs as clientedesbloqueado_obs

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
            AND isnull(opdet.deleted_at);";
    //dd($sql);

    $datas = DB::select($sql);
    //dd($datas);
    foreach ($datas as &$data) {
        $acuerdotecnico = AcuerdoTecnico::findOrFail($data->acuerdotecnico_id);
        //dd($acuerdotecnico->nombre_producto);
        $data->nombre_producto = $acuerdotecnico->nombre_producto;
        //$nombre_producto = $acuerdotecnico->nombre_producto;
        /* // Array para almacenar el resultado final
        $detalleArrayFinal = [];

        // Dividir el campo detallenv en registros individuales
        $detalleArray = explode(';', $data->nvdetalle);
        //dd($detalleArray);

        // Crear un array con todos los producto_id
        $productoIds = array_map(function ($detalle) {
            return explode('|', $detalle)[0]; // Extraemos solo producto_id
        }, $detalleArray);

        // Procesar cada registro de detallenv y agregar el nombre del producto
        //dd($detalleArray);
        foreach ($detalleArray as $index => $detalle) {
            //dd($detalle);
            $productoarray = Producto::atributosProducto($productoIds[$index]);
            list($producto_id, $cant, $precio, $subtotal, $cantsoldesp, $requiere_fabricacion, $id, $totalkilos) = explode('|', $detalle);
            //$producto_nombre = isset($productos[$producto_id]) ? $productos[$producto_id] : 'Desconocido';
            $detalleFinal = implode('|', [$producto_id, $cant, $precio, $subtotal, $cantsoldesp, $productoarray["nombre"], $requiere_fabricacion, $id, $totalkilos]);
            $detalleArrayFinal[] = $detalleFinal;
        }
        //dd(implode(';', $detalleArrayFinal));

        // Reconstruir el campo detallenv con los nuevos valores
        $data->nvdetalle = implode(';', $detalleArrayFinal); */
    }
    //dd($datas);
    filtrarclientesbloqueados($request,$datas);
    return $datas;

    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);
    $arraySucFisxUsu = implode(",", sucFisXUsu($user->persona));
    if($aux_sql==1){
        $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,notaventa.aprobstatus,visto,notaventa.oc_file,
        comuna.nombre as comunanombre,sucursal.nombre as sucursal_nombre,
        vista_notaventatotales.cant,
        vista_notaventatotales.precioxkilo,
        vista_notaventatotales.totalkilos,
        vista_notaventatotales.subtotal,
        sum(if(areaproduccion.id=1,notaventadetalle.totalkilos,0)) AS pvckg,
        sum(if(areaproduccion.id=2,notaventadetalle.totalkilos,0)) AS cankg,
        sum(if(areaproduccion.id=1,notaventadetalle.subtotal,0)) AS pvcpesos,
        sum(if(areaproduccion.id=2,notaventadetalle.subtotal,0)) AS canpesos,
        sum(notaventadetalle.subtotal) AS totalps,
        (SELECT sum(kgsoldesp) as kgsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id) as totalkgsoldesp,
        (SELECT sum(subtotalsoldesp) as subtotalsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id) as totalsubtotalsoldesp,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho,
        tipoentrega.nombre as tipentnombre,tipoentrega.icono,
        (SELECT CONCAT(dte.nrodocto,';',oc_id,';',oc_folder,'/',oc_file) as nrodocto
            FROM dteoc INNER JOIN dte
            ON dteoc.dte_id = dte.id AND ISNULL(dteoc.deleted_at) AND ISNULL(dte.deleted_at)
            INNER JOIN dteguiadesp
            ON dteoc.dte_id = dteguiadesp.dte_id AND ISNULL(dteguiadesp.deleted_at)
            WHERE dteoc.oc_id = notaventa.oc_id
            AND isnull(dteguiadesp.notaventa_id)
            AND dte.cliente_id= notaventa.cliente_id
            AND dteguiadesp.dte_id NOT IN (SELECT dteanul.dte_id 
                                    FROM dteanul 
                                    WHERE dteanul.dte_id = dteguiadesp.dte_id 
                                    and ISNULL(dteanul.deleted_at))) as dte_nrodocto,
        if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
        '' as rutanuevasoldesp,
        notaventa.aprobfechahora as notaventa_aprobfechahora,
        cliente.limitecredito,
        IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
        IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
        IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
        IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
        clientedesbloqueado.obs as clientedesbloqueado_obs,
        modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
        clientedesbloqueadomodulo_orddesp.modulo_id as modulo_id_orddesp,
        IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs,
        GROUP_CONCAT(
            CONCAT_WS('|', notaventadetalle.producto_id, notaventadetalle.cant, notaventadetalle.preciounit, notaventadetalle.subtotal,if(ISNULL(vista_sumsoldespdet.cantsoldesp),0,vista_sumsoldespdet.cantsoldesp), notaventadetalle.requiere_fabricacion, if(ISNULL(acuerdotecnico.id),0,acuerdotecnico.id),notaventadetalle.totalkilos)
            SEPARATOR ';'
        ) AS nvdetalle,
        ot.id as ot_id,ot.aprobstatus as ot_aprobstatus
        FROM notaventa INNER JOIN notaventadetalle
        ON notaventa.id=notaventadetalle.notaventa_id and 
        if((SELECT cantsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventadetalle_id=notaventadetalle.id
                ) >= notaventadetalle.cant,false,true)
        LEFT JOIN vista_sumsoldespdet
        ON vista_sumsoldespdet.notaventadetalle_id=notaventadetalle.id
        INNER JOIN producto
        ON notaventadetalle.producto_id=producto.id
        INNER JOIN categoriaprod
        ON categoriaprod.id=producto.categoriaprod_id AND ISNULL(categoriaprod.deleted_at)
        INNER JOIN areaproduccion
        ON areaproduccion.id=categoriaprod.areaproduccion_id
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id
        INNER JOIN comuna
        ON comuna.id=notaventa.comunaentrega_id
        INNER JOIN tipoentrega
        ON tipoentrega.id=notaventa.tipoentrega_id
        INNER JOIN vista_notaventatotales
        ON notaventa.id=vista_notaventatotales.id
        INNER JOIN sucursal
        ON notaventa.sucursal_id = sucursal.id AND ISNULL(sucursal.deleted_at)
        LEFT JOIN clientebloqueado
        ON notaventa.cliente_id = clientebloqueado.cliente_id and isnull(clientebloqueado.deleted_at)
        LEFT JOIN vista_datacobranza
        ON vista_datacobranza.cliente_id = notaventa.cliente_id
        LEFT JOIN clientedesbloqueado
        ON clientedesbloqueado.cliente_id = notaventa.cliente_id and clientedesbloqueado.notaventa_id = notaventa.id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
        LEFT JOIN clientedesbloqueadomodulo
        ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id $aux_condmodulo_id
        LEFT JOIN modulo
        ON modulo.id = clientedesbloqueadomodulo.modulo_id
        LEFT JOIN clientedesbloqueadopro
        ON clientedesbloqueadopro.cliente_id = notaventa.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
        
        LEFT JOIN clientedesbloqueado as clientedesbloqueado_orddesp
        ON clientedesbloqueado_orddesp.cliente_id = notaventa.cliente_id and clientedesbloqueado_orddesp.notaventa_id = notaventa.id and not isnull(clientedesbloqueado_orddesp.notaventa_id) and isnull(clientedesbloqueado_orddesp.deleted_at)
        LEFT JOIN clientedesbloqueadomodulo as clientedesbloqueadomodulo_orddesp
        ON clientedesbloqueadomodulo_orddesp.clientedesbloqueado_id = clientedesbloqueado_orddesp.id and clientedesbloqueadomodulo_orddesp.modulo_id = 7
        LEFT JOIN otnotaventa
        ON otnotaventa.notaventa_id = notaventa.id and otnotaventa.ot_id not in (SELECT otanul.ot_id from otanul WHERE ISNULL(otanul.deleted_at))
        LEFT JOIN ot
        ON ot.id = otnotaventa.ot_id AND ot.id AND ISNULL(ot.deleted_at)
        LEFT JOIN acuerdotecnico
        ON acuerdotecnico.producto_id = notaventadetalle.producto_id
        WHERE
        categoriaprod.id in (SELECT categoriaprodsuc.categoriaprod_id 
            FROM categoriaprodsuc 
            WHERE categoriaprodsuc.categoriaprod_id = categoriaprod.id
            AND categoriaprodsuc.sucursal_id IN ($arraySucFisxUsu))
        and $vendedorcond
        and $aux_condFecha
        and $aux_condrut
        and $aux_condoc_id
        and $aux_condgiro_id
        and $aux_condareaproduccion_id
        and $aux_condtipoentrega_id
        and $aux_condnotaventa_id
        and $aux_aprobstatus
        and $aux_condcomuna_id
        and $aux_condplazoentrega
        and $aux_condproducto_id
        AND $aux_condsucursal_id
        and notaventa.anulada is null
        and notaventa.findespacho is null
        and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
        and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        AND notaventa.sucursal_id in ($sucurcadena)
        GROUP BY notaventadetalle.notaventa_id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,notaventa.aprobstatus,visto,notaventa.oc_file,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho
        ORDER BY $aux_orden;";
    }
    if($aux_sql==2){
        //if(categoriaprod.unidadmedida_id=3,producto.diamextpg,producto.diamextmm) AS diametro,
        $sql = "SELECT notaventadetalle.producto_id,producto.nombre,
        producto.diametro,sucursal.nombre as sucursal_nombre,
        claseprod.cla_nombre,producto.long,producto.peso,producto.tipounion,
        cant,cantsoldesp,
        totalkilos,
        subtotal,
        kgsoldesp,subtotalsoldesp,
        sum(cant-if(isnull(cantsoldesp),0,cantsoldesp)) as saldocant,
        sum(totalkilos-if(isnull(kgsoldesp),0,kgsoldesp)) as saldokg,
        sum(subtotal-if(isnull(subtotalsoldesp),0,subtotalsoldesp)) as saldoplata,
        (SELECT CONCAT(dte.nrodocto,';',oc_id,';',oc_folder,'/',oc_file) as nrodocto
            FROM dteoc INNER JOIN dte
            ON dteoc.dte_id = dte.id AND ISNULL(dteoc.deleted_at) AND ISNULL(dte.deleted_at)
            INNER JOIN dteguiadesp
            ON dteoc.dte_id = dteguiadesp.dte_id AND ISNULL(dteguiadesp.deleted_at)
            WHERE dteoc.oc_id = notaventa.oc_id
            AND isnull(dteguiadesp.notaventa_id)
            AND dte.cliente_id= notaventa.cliente_id
            AND dteguiadesp.dte_id NOT IN (SELECT dteanul.dte_id 
                                    FROM dteanul 
                                    WHERE dteanul.dte_id = dteguiadesp.dte_id 
                                    and ISNULL(dteanul.deleted_at))) as dte_nrodocto,
        if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
        '' as rutanuevasoldesp
        FROM notaventadetalle INNER JOIN notaventa
        ON notaventadetalle.notaventa_id=notaventa.id
        INNER JOIN producto
        ON notaventadetalle.producto_id=producto.id
        INNER JOIN claseprod
        ON producto.claseprod_id=claseprod.id
        INNER JOIN categoriaprod
        ON producto.categoriaprod_id=categoriaprod.id
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id
        LEFT JOIN vista_sumsoldespdet
        ON vista_sumsoldespdet.notaventadetalle_id=notaventadetalle.id
        INNER JOIN sucursal
        ON notaventa.sucursal_id = sucursal.id AND ISNULL(sucursal.deleted_at)
        LEFT JOIN clientebloqueado
        ON notaventa.cliente_id = clientebloqueado.cliente_id and isnull(clientebloqueado.deleted_at)
        WHERE 
        categoriaprod.id in (SELECT categoriaprodsuc.categoriaprod_id 
            FROM categoriaprodsuc 
            WHERE categoriaprodsuc.categoriaprod_id = categoriaprod.id
            AND categoriaprodsuc.sucursal_id IN (SELECT vista_sucfisxusu.sucursal_id
                    FROM vista_sucfisxusu
                    WHERE vista_sucfisxusu.usuario_id=$user->id))
        and $vendedorcond
        and $aux_condFecha
        and $aux_condrut
        and $aux_condoc_id
        and $aux_condgiro_id
        and $aux_condareaproduccion_id
        and $aux_condtipoentrega_id
        and $aux_condnotaventa_id
        and $aux_aprobstatus
        and $aux_condcomuna_id
        and $aux_condplazoentrega
        and $aux_condproducto_id
        AND $aux_condsucursal_id
        AND isnull(notaventa.findespacho)
        AND isnull(notaventa.anulada)
        AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
        and notaventadetalle.notaventa_id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        AND notaventa.sucursal_id in ($sucurcadena)
        GROUP BY notaventadetalle.producto_id
        ORDER BY producto.nombre,producto.peso;";
    }
    

    if($aux_sql==3){
        $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
        comuna.nombre as comunanombre,
        vista_notaventatotales.cant,
        vista_notaventatotales.precioxkilo,
        sum(vista_notaventatotales.totalkilos) as totalkilos,
        sum(vista_notaventatotales.subtotal) as subtotal,
        sum(if(areaproduccion.id=1,notaventadetalle.totalkilos,0)) AS pvckg,
        sum(if(areaproduccion.id=2,notaventadetalle.totalkilos,0)) AS cankg,
        sum(if(areaproduccion.id=1,notaventadetalle.subtotal,0)) AS pvcpesos,
        sum(if(areaproduccion.id=2,notaventadetalle.subtotal,0)) AS canpesos,
        sum(notaventadetalle.subtotal) AS totalps,
        sum((SELECT sum(kgsoldesp) as kgsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id)) as totalkgsoldesp,
        sum((SELECT sum(subtotalsoldesp) as subtotalsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id)) as totalsubtotalsoldesp,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho,
        tipoentrega.nombre as tipentnombre,tipoentrega.icono,
        if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
        '' as rutanuevasoldesp
        FROM notaventa INNER JOIN notaventadetalle
        ON notaventa.id=notaventadetalle.notaventa_id and 
        if((SELECT cantsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventadetalle_id=notaventadetalle.id
                ) >= notaventadetalle.cant,false,true)
        INNER JOIN producto
        ON notaventadetalle.producto_id=producto.id
        INNER JOIN categoriaprod
        ON categoriaprod.id=producto.categoriaprod_id
        INNER JOIN areaproduccion
        ON areaproduccion.id=categoriaprod.areaproduccion_id
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id
        INNER JOIN comuna
        ON comuna.id=notaventa.comunaentrega_id
        INNER JOIN tipoentrega
        ON tipoentrega.id=notaventa.tipoentrega_id
        INNER JOIN vista_notaventatotales
        ON notaventa.id=vista_notaventatotales.id
        LEFT JOIN clientebloqueado
        ON notaventa.cliente_id = clientebloqueado.cliente_id and isnull(clientebloqueado.deleted_at)
        WHERE 
        categoriaprod.id in (SELECT categoriaprodsuc.categoriaprod_id 
            FROM categoriaprodsuc 
            WHERE categoriaprodsuc.categoriaprod_id = categoriaprod.id
            AND categoriaprodsuc.sucursal_id IN (SELECT vista_sucfisxusu.sucursal_id
                    FROM vista_sucfisxusu
                    WHERE vista_sucfisxusu.usuario_id=$user->id))
        and $vendedorcond
        and $aux_condFecha
        and $aux_condrut
        and $aux_condoc_id
        and $aux_condgiro_id
        and $aux_condareaproduccion_id
        and $aux_condtipoentrega_id
        and $aux_condnotaventa_id
        and $aux_aprobstatus
        and $aux_condcomuna_id
        and $aux_condplazoentrega
        and $aux_condproducto_id
        and notaventa.anulada is null
        and notaventa.findespacho is null
        and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
        and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        AND notaventa.sucursal_id in ($sucurcadena)
        GROUP BY notaventa.cliente_id
        ORDER BY $aux_orden;";
        //dd($sql);
    }

    $datas = DB::select($sql);
    //dd($datas);
    if($aux_sql==1){
        foreach ($datas as &$data) {
            //dd($data->nvdetalle);
    
            // Array para almacenar el resultado final
            $detalleArrayFinal = [];
    
            // Dividir el campo detallenv en registros individuales
            $detalleArray = explode(';', $data->nvdetalle);
            //dd($detalleArray);
    
            // Crear un array con todos los producto_id
            $productoIds = array_map(function ($detalle) {
                return explode('|', $detalle)[0]; // Extraemos solo producto_id
            }, $detalleArray);
    
            // Procesar cada registro de detallenv y agregar el nombre del producto
            //dd($detalleArray);
            foreach ($detalleArray as $index => $detalle) {
                //dd($detalle);
                $productoarray = Producto::atributosProducto($productoIds[$index]);
                list($producto_id, $cant, $precio, $subtotal, $cantsoldesp, $requiere_fabricacion, $id, $totalkilos) = explode('|', $detalle);
                //$producto_nombre = isset($productos[$producto_id]) ? $productos[$producto_id] : 'Desconocido';
                $detalleFinal = implode('|', [$producto_id, $cant, $precio, $subtotal, $cantsoldesp, $productoarray["nombre"], $requiere_fabricacion, $id, $totalkilos]);
                $detalleArrayFinal[] = $detalleFinal;
            }
            //dd(implode(';', $detalleArrayFinal));
    
            // Reconstruir el campo detallenv con los nuevos valores
            $data->nvdetalle = implode(';', $detalleArrayFinal);
        }
    }
    //dd($datas);
    filtrarclientesbloqueados($request,$datas);
    return $datas;
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
			opdet.kg,opdet.cant,opdet.cantrec,opdet.kgrec,opdet.cantprod,opdet.kgprod,
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
            AND isnull(opdet.deleted_at);";
    //dd($sql);

    $datas = DB::select($sql);
    foreach ($datas as &$data) {
        $acuerdotecnico = AcuerdoTecnico::findOrFail($data->acuerdotecnico_id);
        $data->nombre_producto = $acuerdotecnico->nombre_producto;
    }
    filtrarclientesbloqueados($request,$datas);
    return $datas;    
}