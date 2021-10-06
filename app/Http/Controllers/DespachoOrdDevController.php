<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarDespachoOrdDev;
use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoObs;
use App\Models\DespachoOrd;
use App\Models\DespachoOrdDev;
use App\Models\DespachoOrdDevDet;
use App\Models\DespachoOrdDevMotivo;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespachoOrdDevController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-devolucion-orden-despacho');
        return view('despachoorddev.index');
    }

    public function despachoorddevpage(){
        $sql = "SELECT despachoorddev.id,DATE_FORMAT(despachoorddev.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                cliente.razonsocial,despachoord_id,
                '' as pdfcot,
                despachoorddev.fechahora as fechahora_aaaammdd,
                despachoord.notaventa_id,despachoord.despachosol_id
            FROM despachoorddev inner join despachoord
            on despachoord.id = despachoorddev.despachoord_id and isnull(despachoord.deleted_at)
            and despachoord.id not in (select despachoordanul.despachoord_id from despachoordanul where isnull(despachoordanul.deleted_at))
            inner join notaventa
            on notaventa.id = despachoord.notaventa_id and isnull(notaventa.deleted_at) and isnull(notaventa.anulada)
            inner join cliente
            on cliente.id = notaventa.cliente_id and isnull(cliente.deleted_at)
            where isnull(despachoorddev.anulada) and isnull(despachoorddev.deleted_at)
            ORDER BY despachoorddev.id desc;";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //public function guardar(ValidarDespachoOrdDev $request)
    public function guardar(Request $request)
    {
        can('guardar-devolucion-orden-despacho');
        $despachoord = DespachoOrd::find($request->despachoord_id);
        if($despachoord != null){
            if(isset($despachoord->despachoordanul) == false){
                if($request->updated_at == $despachoord->updated_at){
                    //dd($request);
                    $despachoord->updated_at = date("Y-m-d H:i:s");
                    $despachoord->save();
                    $hoy = date("Y-m-d H:i:s");
                    $request->request->add(['fechahora' => $hoy]);
                    $request->request->add(['usuario_id' => auth()->id()]);
                    $despachoorddev = DespachoOrdDev::create($request->all());
                    $despachoorddev_id = $despachoorddev->id;
                    $cont_producto = count($request->producto_id);
                    if($cont_producto>0){
                        for ($i=0; $i < $cont_producto ; $i++){
                            $aux_cantord = $request->cantord[$i];
                            if(is_null($request->producto_id[$i])==false && is_null($aux_cantord)==false && $aux_cantord > 0){
                                $despachoorddevdet = new DespachoOrdDevDet();
                                $despachoorddevdet->despachoorddev_id = $despachoorddev_id;
                                $despachoorddevdet->despachoorddet_id = $request->despachoorddet_id[$i];
                                $despachoorddevdet->cantdev = $request->cantord[$i];
                                if($despachoorddevdet->save()){
                                    /*
                                    $notaventadetalle = NotaVentaDetalle::findOrFail($request->NVdet_id[$i]);
                                    $notaventadetalle->cantsoldesp = $request->cantsoldesp[$i];
                                    $notaventadetalle->save();
                                    */
                                    //$despacho_id = $despachoord->id;
                                }
                            }
                        }
                    }
                    return redirect('despachoorddev')->with([
                        'mensaje'=>'Registro creado con exito.',
                        'tipo_alert' => 'alert-success'
                    ]);

                }else{
                    return redirect('despachoorddev/reporte')->with([
                        'mensaje'=>'Registro no fue creado. Registro modificado por otro usuario. Fecha Hora: '.$despachoord->updated_at,
                        'tipo_alert' => 'alert-error'
                    ]);
                }    
            }else{
                return redirect('despachoorddev/reporte')->with([
                    'mensaje'=>'No se puede hacer devolución, Orden de despacho fue anulada.',
                    'tipo_alert' => 'alert-error'
                ]);
            }
        }else{
            return redirect('despachoorddev/consultadespordfact')->with([
                'mensaje'=>'No se puede hacer devolución, Registro fue eliminado.',
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
    public function editar($id)
    {
        can('editar-devolucion-orden-despacho');

        $despachoorddev = DespachoOrdDev::findOrFail($id);
        $despachoorddet_idArray = DespachoOrdDevDet::where('despachoorddev_id',$id)->pluck('despachoorddet_id')->toArray();
        $despachoorddevdets = $despachoorddev->despachoorddevdets()->get();
        $data = DespachoOrd::findOrFail($despachoorddev->despachoord_id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $detalles = $data->despachoorddets()
                    ->whereIn('despachoorddet.id', $despachoorddet_idArray)
                    ->get();

                    //->whereIn('despachoorddet.id', $sucurArray)
        //dd($detalles);
        $empresa = Empresa::findOrFail(1);
        $despachoorddevmotivos = DespachoOrdDevMotivo::orderBy('id')->get();
        $fecha = date("d/m/Y", strtotime($data->fechahora));

        $aux_sta=3;
        $aux_statusPant = 0;

        return view('despachoorddev.editar', compact('data','detalles','empresa','aux_sta','fecha','aux_statusPant','despachoorddev','despachoorddevdets','despachoorddevmotivos'));
  
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
        can('guardar-motivo-devolucion-despacho');
        $despachoorddev = DespachoOrdDev::find($request->despachoorddev_id);
        //dd($request);
        if($despachoorddev != null){

            if($despachoorddev->despachoord->updated_at == $request->updated_at and $despachoorddev->updated_at == $request->devupdated_at ){
                $despachoorddev->updated_at = date("Y-m-d H:i:s");
                $despachoorddev->despachoorddevmotivo_id = $request->despachoorddevmotivo_id;
                $despachoorddev->obs = $request->obs;
                if($despachoorddev->save()){
                    $cont_producto = count($request->producto_id);
                    if($cont_producto>0){
                        for ($i=0; $i < $cont_producto ; $i++){
                            if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                                if(is_null($request->despachoorddevdet_id[$i])){
                                    if($request->cantord[$i] > 0){
                                        $despachoorddevdet = new DespachoOrdDevDet();
                                        $despachoorddevdet->despachoorddev_id = $request->despachoorddev_id;
                                        $despachoorddevdet->despachoorddet_id = $request->despachoorddet_id[$i];
                                        $despachoorddevdet->cantdev = $request->cantord[$i];
                                        $despachoorddevdet->save();
                                    }
                                }else{
                                    $despachoorddevdet = DespachoOrdDevDet::findOrFail($request->despachoorddevdet_id[$i]);
                                    $despachoorddevdet->cantdev = $request->cantord[$i];
                                    if($despachoorddevdet->save()){
                                        if($request->cantord[$i]==0){
                                            $despachoorddevdet->usuariodel_id = auth()->id();
                                            $despachoorddevdet->save();
                                            $despachoorddevdet->delete();
                                        }
                                    }    
                                }
                            }
                        }
                    }
                    return redirect('despachoorddev')->with([
                        'mensaje'=>'Registro actualizado con exito.',
                        'tipo_alert' => 'alert-success'
                    ]);
                }else{
                    return redirect('despachoorddev')->with([
                        'mensaje'=>'Registro no fue modificado. Error al intentar Actualizar.',
                            'tipo_alert' => 'alert-error'
                        ]);    
                }
            }else{
                return redirect('despachoorddev')->with([
                    'mensaje'=>'Registro no fue modificado. Registro Editado por otro usuario. Fecha Hora: '.$despachoorddev->updated_at,
                        'tipo_alert' => 'alert-error'
                    ]);
            }
        }else{
            return redirect('despachoorddev')->with([
                'mensaje'=>'Registro no fue Modificado. La devolucion fue eliminada por otro usuario.',
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
    public function eliminar(Request $request,$id)
    {
        can('eliminar-cotizacion');
        //dd($request);
        if ($request->ajax()) {
            //dd($id);
            if (DespachoOrdDev::destroy($id)) {
                //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                $despachoorddev = DespachoOrdDev::withTrashed()->findOrFail($id);
                $despachoorddev->usuariodel_id = auth()->id();
                $despachoorddev->save();
                //Eliminar detalle de cotizacion
                DespachoOrdDevDet::where('despachoorddev_id', $id)->update(['usuariodel_id' => auth()->id()]);
                DespachoOrdDevDet::where('despachoorddev_id', '=', $id)->delete();
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }

    public function consultadespordfact(Request $request){
        $respuesta = cargadatos();
        $clientes = $respuesta['clientes'];
        $tablashtml['giros'] = $respuesta['giros'];
        $tablashtml['areaproduccions'] = $respuesta['areaproduccions'];
        $tablashtml['tipoentregas'] = $respuesta['tipoentregas'];
        $tablashtml['fechaServ'] = [
                    'fecha1erDiaMes' => $respuesta['fecha1erDiaMes'],
                    'fechaAct' => $respuesta['fechaAct']
                    ];

        $tablashtml['aux_verestado']='1'; //Mostrar todas los opciopnes de estado de OD

        $tablashtml['titulo'] = "Consultar Orden Despacho, Guia, Factura, cerrada";
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $tablashtml['rutacreardev'] = route('creardev_despachoorddev', ['id' => '0']);
        return view('despachoorddev.consulta', compact('clientes','tablashtml'));
    }

    public function reporte(Request $request){
        $respuesta = array();
        $respuesta['exito'] = false;
        $respuesta['mensaje'] = "Código no Existe";
        $respuesta['tabla'] = "";
    
        if($request->ajax()){
            $datas = consultaorddesp($request);
            return datatables($datas)->toJson();
        }
    }

    public function creardev($id){
        can('crear-orden-despacho');
        $data = DespachoOrd::findOrFail($id);
        //dd($data);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $detalles = $data->despachoorddets()->get();
        //dd($detalles);
        $vendedor_id=$data->notaventa->vendedor_id;
        $fecha = date("d/m/Y", strtotime($data->fechahora));
        $empresa = Empresa::findOrFail(1);
        $despachoorddevmotivos = DespachoOrdDevMotivo::orderBy('id')->get();
        $aux_sta=2;
        $aux_statusPant = 0;

        //dd($clientedirecs);
        return view('despachoorddev.crear', compact('data','detalles','fecha','empresa','aux_sta','aux_cont','aux_statusPant','vendedor_id','despachoorddevmotivos'));
        
    }
}

function cargadatos(){
    $respuesta = array();
    $clientesArray = Cliente::clientesxUsuario();
    $clientes = $clientesArray['clientes'];
    $vendedor_id = $clientesArray['vendedor_id'];
    $sucurArray = $clientesArray['sucurArray'];

    
    $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
    $vendedores1 = $arrayvend['vendedores'];
    $clientevendedorArray = $arrayvend['clientevendedorArray'];

    $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

    $giros = Giro::orderBy('id')->get();
    $areaproduccions = AreaProduccion::orderBy('id')->get();
    $tipoentregas = TipoEntrega::orderBy('id')->get();
    $comunas = Comuna::orderBy('id')->get();

    $respuesta['clientes'] = $clientes;
    $respuesta['vendedores'] = $vendedores;
    $respuesta['vendedores1'] = $vendedores1;
    $respuesta['giros'] = $giros;
    $respuesta['areaproduccions'] = $areaproduccions;
    $respuesta['tipoentregas'] = $tipoentregas;
    $respuesta['comunas'] = $comunas;
    $respuesta['fecha1erDiaMes'] = date("01/m/Y");
    $respuesta['fechaAct'] = date("d/m/Y");

    return $respuesta;
}

function consultaorddesp($request){
    if(empty($request->vendedor_id)){
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $vendedorcond = "notaventa.vendedor_id=" . $vendedor_id ;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
            $sucurArray = $user->sucursales->pluck('id')->toArray();
        }else{
            $vendedorcond = " true ";
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
    }else{
        if(is_array($request->vendedor_id)){
            $aux_vendedorid = implode ( ',' , $request->vendedor_id);
        }else{
            $aux_vendedorid = $request->vendedor_id;
        }
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";

        //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }

    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "despachoord.fechahora>='$fechad' and despachoord.fechahora<='$fechah'";
    }

    if(empty($request->fechadfac) or empty($request->fechahfac)){
        $aux_condFechaFac = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechadfac);
        $fechadfac = date_format($fecha, 'Y-m-d');
        $fecha = date_create_from_format('d/m/Y', $request->fechahfac);
        $fechahfac = date_format($fecha, 'Y-m-d');
        $aux_condFechaFac = "despachoord.fechafactura>='$fechadfac' and despachoord.fechafactura<='$fechahfac'";
    }

    if(empty($request->fechaestdesp)){
        $aux_condFechaED = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechaestdesp);
        $fechaestdesp = date_format($fecha, 'Y-m-d');
        $aux_condFechaED = "despachoord.fechaestdesp ='$fechaestdesp'";
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
    if(empty($request->giro_id)){
        $aux_condgiro_id = " true";
    }else{
        $aux_condgiro_id = "notaventa.giro_id='$request->giro_id'";
    }
    if(empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = " true";
    }else{
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }
    if(empty($request->tipoentrega_id)){
        $aux_condtipoentrega_id = " true";
    }else{
        $aux_condtipoentrega_id = "notaventa.tipoentrega_id='$request->tipoentrega_id'";
    }
    if(empty($request->notaventa_id)){
        $aux_condnotaventa_id = " true";
    }else{
        $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
    }

    if(empty($request->statusOD)){
        $aux_statusOD = " true";
    }else{
        switch ($request->statusOD) {
            case 1: //Emitidas
                $aux_statusOD = "isnull(despachoord.aprguiadesp) and isnull(despachoordanul.id)";
                break;
            case 2: //Anuladas
                $aux_statusOD = "isnull(despachoord.aprguiadesp) and not isnull(despachoordanul.id)";
                break;
            case 3: //Esperando por guia
                $aux_statusOD = "despachoord.aprguiadesp=1 and isnull(despachoord.guiadespacho) and isnull(despachoordanul.id)";
                break;    
            case 4: //Esperando por Factura
                $aux_statusOD = "not isnull(despachoord.guiadespacho) and isnull(despachoord.numfactura) and isnull(despachoordanul.id)";
                break;
            case 5: //Cerradas
                $aux_statusOD = "not isnull(despachoord.numfactura) and isnull(despachoordanul.id)";
                break;
        }
        
    }
    $aux_statusOD = "not isnull(despachoord.numfactura) and isnull(despachoordanul.id)";
/*
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
    }
*/
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true ";
    }else{
        if(is_array($request->comuna_id)){
            $aux_comuna = implode ( ',' , $request->comuna_id);
        }else{
            $aux_comuna = $request->comuna_id;
        }
        $aux_condcomuna_id = " notaventa.comunaentrega_id in ($aux_comuna) ";
    }

    if(empty($request->id)){
        $aux_condid = " true";
    }else{
        $aux_condid = "despachoord.id='$request->id'";
    }

       
    if(empty($request->despachosol_id)){
        $aux_conddespachosol_id = " true";
    }else{
        $aux_conddespachosol_id = "despachoord.despachosol_id='$request->despachosol_id'";
    }

    if(empty($request->guiadespacho)){
        $aux_condguiadespacho = " true";
    }else{
        $aux_condguiadespacho = "despachoord.guiadespacho='$request->guiadespacho'";
    }
    if(empty($request->numfactura)){
        $aux_condnumfactura = " true";
    }else{
        $aux_condnumfactura = "despachoord.numfactura='$request->numfactura'";
    }

    $aux_condaprobord = "true";

    //$suma = despachoord::findOrFail(2)->despachoorddets->where('notaventadetalle_id',1);

    $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,cliente.rut,
            cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,
            comuna.nombre as comunanombre,
            despachoord.notaventa_id,despachoord.fechaestdesp,
            sum(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) AS totalkilos,
            round(sum((notaventadetalle.preciounit * despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS subtotal,
            despachoord.aprguiadesp,despachoord.aprguiadespfh,
            despachoord.guiadespacho,despachoord.guiadespachofec,despachoord.numfactura,despachoord.fechafactura,
            despachoordanul.id as despachoordanul_id
            FROM despachoord INNER JOIN despachoorddet
            ON despachoord.id=despachoorddet.despachoord_id
            INNER JOIN notaventa
            ON notaventa.id=despachoord.notaventa_id
            INNER JOIN notaventadetalle
            ON despachoorddet.notaventadetalle_id=notaventadetalle.id
            INNER JOIN producto
            ON notaventadetalle.producto_id=producto.id
            INNER JOIN categoriaprod
            ON categoriaprod.id=producto.categoriaprod_id
            INNER JOIN areaproduccion
            ON areaproduccion.id=categoriaprod.areaproduccion_id
            INNER JOIN cliente
            ON cliente.id=notaventa.cliente_id
            INNER JOIN comuna
            ON comuna.id=despachoord.comunaentrega_id
            LEFT JOIN despachoordanul
            ON despachoordanul.despachoord_id=despachoord.id
            LEFT JOIN vista_sumdevorddespdet
            ON vista_sumdevorddespdet.despachoorddet_id=despachoorddet.id
            WHERE $vendedorcond
            and $aux_condFecha
            and $aux_condFechaFac
            and $aux_condFechaED
            and $aux_condrut
            and $aux_condoc_id
            and $aux_condgiro_id
            and $aux_condareaproduccion_id
            and $aux_condtipoentrega_id
            and $aux_condnotaventa_id
            and $aux_statusOD
            and $aux_condcomuna_id
            and $aux_condaprobord
            and $aux_condid
            and $aux_conddespachosol_id
            and $aux_condguiadespacho
            and $aux_condnumfactura
            and isnull(despachoord.deleted_at) AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
            AND despachoorddet.cantdesp>if(isnull(vista_sumdevorddespdet.cantdev),0,vista_sumdevorddespdet.cantdev)
            GROUP BY despachoord.id desc;";
            
            //Linea en comentario para poder mostrar todos los registros incluso las notas de venta que  que fueron cerradas de manera forzada
            //and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))

            //and despachoord.id not in (SELECT despachoord_id from despachoordanul where isnull(deleted_at))

    //dd($sql);
    $datas = DB::select($sql);

    return $datas;
}