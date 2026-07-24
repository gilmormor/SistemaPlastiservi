<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarOt;
use App\Models\AreaProduccion;
use App\Models\CentroEconomico;
use App\Models\Cliente;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Foliocontrol;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\InvMovModulo;
use App\Models\NotaVenta;
use App\Models\NotaVentaCerrada;
use App\Models\NotaVentaDetalle;
use App\Models\Ot;
use App\Models\OtAnul;
use App\Models\OtDet;
use App\Models\OtDetNVDet;
use App\Models\OtNotaVenta;
use App\Models\OtOc;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\UnidadMedida;
use App\Models\Vendedor;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class OtController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-ot');
        return view('ot.index');
    }

    public function otpage($id = ""){
        $datas = consultaindex($id);
        return datatables($datas)->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-ot');
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedidas'] = UnidadMedida::orderBy('id')->get();

        //dd($tablas);
        return view('ot.crear',compact('tablas'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        can('guardar-ot');
        //dd($request);
        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            return redirect('ot')->with([
                'mensaje'=>'No hay items, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $cliente = Cliente::findOrFail($request->cliente_id);
        foreach ($cliente->clientebloqueados as $clientebloqueado) {
            return redirect('ot')->with([
                'id' => 0,
                'mensaje'=>'No es posible procesar registro, Condición financiera en revisión: ' . $clientebloqueado->descripcion,
                'tipo_alert' => 'alert-error'
            ]);
        }
        $request1 = new Request();
        $request1->merge(['modulo_id' => 31]);
        $request1->request->set('modulo_id', 31);
        $request1->merge(['deldesbloqueo' => 0]);
        $request1->request->set('deldesbloqueo', 0);
        $clibloq = clienteBloqueado($request->cliente_id,0,$request1);
        if(!is_null($clibloq["bloqueo"])){
            /* $request1 = new Request();
            $request1->merge(['cliente_id' => $request->cliente_id]);
            $request1->request->set('cliente_id', $request->cliente_id);
            $respuesta = DataCobranza::llenartabla($request1); */

            return redirect('ot')->with([
                "mensaje" => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                "tipo_alert" => "alert-error"
            ]);
        }
        
        $ot = new Ot();
        if(!is_null($request->oc_id)){
            $otoc = new OtOc();
            $otoc->ot_id = "";
            $otoc->oc_id = $request->oc_id;
            $ot->otocs[] = $otoc;
            //$otoc->save();
        }
    
        $Tmntneto = 0;
        $Tiva = 0;
        $Tmnttotal = 0;
        $Tkgtotal = 0;
        //$dtedtes = [];
        $aux_nrolindet = 0;
        for ($i=0; $i < $cont_producto ; $i++){
            if(is_null($request->producto_id[$i])==false AND (is_null($request->qtyitem[$i])==false)){
                if(cadVacia($request->producto_id[$i])){
                    mensajeRespuesta([
                        'mensaje' => "Campo producto_id no puede quedar Vacio, Item: " . strval($i + 1),
                        'tipo_alert' => 'alert-error'
                    ]);                    
                }
                if(cadVacia($request->vlrcodigo[$i])){
                    mensajeRespuesta([
                        'mensaje' => "Campo vlrcodigo no puede quedar Vacio, Item: " . strval($i + 1),
                        'tipo_alert' => 'alert-error'
                    ]);                    
                }
                if(cadVacia($request->qtyitem[$i])){
                    mensajeRespuesta([
                        'mensaje' => "Campo qtyitem no puede quedar Vacio, Item: " . strval($i + 1),
                        'tipo_alert' => 'alert-error'
                    ]);                    
                }
                if(cadVacia($request->unidadmedidainp_id[$i])){
                    mensajeRespuesta([
                        'mensaje' => "Campo unidadmedida_id no puede quedar Vacio, Item: " . strval($i + 1),
                        'tipo_alert' => 'alert-error'
                    ]);                    
                }
                /* if(cadVacia($request->prcitem[$i])){
                    mensajeRespuesta([
                        'mensaje' => "Campo prcitem no puede quedar Vacio, Item: " . strval($i + 1),
                        'tipo_alert' => 'alert-error'
                    ]);                    
                }
                if(cadVacia($request->montoitem[$i])){
                    mensajeRespuesta([
                        'mensaje' => "Campo montoitem no puede quedar Vacio, Item: " . strval($i + 1),
                        'tipo_alert' => 'alert-error'
                    ]);                    
                } */
                $producto = Producto::findOrFail($request->producto_id[$i]);
                $aux_espesor = $producto->espesor;
                if($producto->acuerdotecnico){
                    $aux_espesor = $producto->acuerdotecnico->at_espesor;
                }

                $unidadmedida = UnidadMedida::findOrFail($request->unidadmedidainp_id[$i]);
                $otdet = new OtDet();
                $otdet->producto_id = $request->producto_id[$i];
                $aux_cant = convertirNumeroLatinoaUS($request->cant[$i]);
                $aux_cant = is_numeric($aux_cant) ? $aux_cant : 0;

                $otdet->cant = $aux_cant;
                $otdet->cantprod = $aux_cant;
                $otdet->espesorprod = $aux_espesor;
                $otdet->unidadmedida_id = $request->unidadmedidainp_id[$i];
                //$otdet->prcitem = $request->prcitem[$i]; //$request->montoitem[$i]/$request->qtyitem[$i]; //$request->prcitem[$i];
                //$otdet->montoitem = $request->montoitem[$i];
                //$otdet->montoitem = round($otdet->qtyitem * $otdet->prcitem,0); //$request->montoitem[$i];
                //$otdet->obsdet = $request->obsdet[$i];
                $otdet->obs = $request->otdet_obs[$i];
                $aux_itemkg = convertirNumeroLatinoaUS($request->itemkg[$i]);
                $aux_itemkg = is_numeric($aux_itemkg) ? $aux_itemkg : 0;
                $otdet->kg = $aux_itemkg;
                $otdet->kgprod = $aux_itemkg;
                //$otdet->save();
                $ot->otdets[] = $otdet;

                //$Tmntneto += $otdet->montoitem; //$request->montoitem[$i];
                $Tkgtotal += $aux_itemkg;
            }
        }
        if($Tkgtotal <= 0){
            return redirect('ot')->with([
                'mensaje'=> "Neto total Kgs debe ser mayor a cero" ,
                'tipo_alert' => 'alert-error'
            ]);
        }

        $hoy = date("Y-m-d H:i:s");
        $ot->fechahora = $hoy;
        $ot->sucursal_id = $request->sucursal_id;
        $ot->cliente_id = $cliente->id;
        $ot->vendedor_id = 1;//$request->vendedor_id;
        $ot->obs = $request->obs;
        $dateInput = explode('/',$request->fechaestdesp);
        $ot->fechaestdesp = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        //dd($request);
        /* $ot->mntneto = $Tmntneto; */
        $ot->totalkg = $Tkgtotal;
        $ot->usuario_id = auth()->id();

        $otNew = Ot::create($ot->toArray());
        if(isset($otoc)){
            if ($foto = Dte::setFoto($request->oc_file,$otNew->id,$request,"OT","oc")){ //2 ultimos parametros son origen de orden de compra FC Factura y la carpeta donde se guarda la OC
                $otoc->ot_id = $otNew->id;
                $otoc->oc_file = $foto;
                $otoc->save();
            }
        }
        foreach ($ot->otdets as $otdet) {
            $otdet->ot_id = $otNew->id;
            $aux_otdet = $otdet->toArray();
            /* unset($aux_dtedet["despachoorddet_id"]); //ELIMINO PARA EVITAR EL ERROR AL INTERTAR A DteDet
            unset($aux_dtedet["notaventadetalle_id"]); //ELIMINO PARA EVITAR EL ERROR AL INTERTAR A DteDet */
            OtDet::create($aux_otdet);
        }


        $aux_mensaje = "Registro creado con exito.";
        $aux_tipo_alert = 'alert-success';

        return redirect('ot')->with([
            'mensaje'=> $aux_mensaje,
            'tipo_alert' => $aux_tipo_alert
        ]);
    }

    public function editar($id,$updatednum_at)
    {
        can('editar-ot');
        //dd($id);
        $data = Ot::findOrFail($id);
        if(strtotime($data->updated_at) != $updatednum_at){
            return redirect('ot')->with([
                'mensaje'=>'Registro no pudo ser editado. Registro Editado por otro usuario. Fecha Hora: '.$data->updated_at,
                'tipo_alert' => 'alert-error'
            ]);    
        }

        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $vendedor_id=$data->vendedor_id;

        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedidas'] = UnidadMedida::orderBy('id')->get();
        $array_sucursales_cliente = $data->cliente->sucursales->pluck('id')->toArray();
        $user = Usuario::findOrFail(auth()->id());
        $tablas['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablas['sucursales'] = Sucursal::orderBy('id')
                                ->whereIn('sucursal.id', $tablas['sucurArray'])
                                ->whereIn('sucursal.id', $array_sucursales_cliente)
                                ->get();



        $fecha = date("d/m/Y", strtotime($data->fechahora));

        return view('ot.editar', compact('data','tablas'));
    }

    public function actualizar(Request $request, $id)
    {
        can('guardar-ot');
        //dd($request->all());
        //dd($request);
        $ot = Ot::findOrFail($id);
        if(strtotime($ot->updated_at) != $request->updatednum_at){
            return redirect('ot')->with([
                'mensaje'=>'Registro no pudo ser editado. Registro Editado por otro usuario. Fecha Hora: '.$ot->updated_at,
                'tipo_alert' => 'alert-error'
            ]);    
        }

        $aux_fechaestdesp= DateTime::createFromFormat('d/m/Y', $request->fechaestdesp)->format('Y-m-d');
        $request->request->add(['fechaestdesp' => $aux_fechaestdesp]);

        $ot->updated_at = date("Y-m-d H:i:s");
        $ot->update($request->all());

        if(!is_null($request->oc_id)){
            //$ot->otoc->update($request->all());
            if ($foto = Dte::setFoto($request->oc_file,$ot->id,$request,"OT","oc")){ //2 ultimos parametros son origen de orden de compra FC Factura y la carpeta donde se guarda la OC
                DB::table('otoc')->updateOrInsert(
                    ['id' => $ot->id],
                    [
                        'ot_id' => $ot->id,
                        'oc_id' => $request->oc_id,
                        'oc_file' => $foto
                    ]
                );
    
            }

        }
        if($ot->otoc and (is_null($request->oc_id) or $request->oc_id == "")){
            $ot->otoc->delete();
        }
        $auxOtDet=OtDet::where('ot_id',$id)->whereNotIn('id', $request->otdet_id)->pluck('id')->toArray(); //->destroy();
        for ($i=0; $i < count($auxOtDet) ; $i++){
            OtDet::destroy($auxOtDet[$i]);
        }
        $cont_cotdet = count($request->otdet_id);
        if($cont_cotdet>0){
            $Tkgtotal = 0;
            for ($i=0; $i < count($request->otdet_id) ; $i++){
                $aux_itemkg = is_numeric($request->totalkilos[$i]) ? $request->totalkilos[$i] : 0;
                $producto = Producto::findOrFail($request->producto_id[$i]);
                $aux_espesor = $producto->espesor;
                if($producto->acuerdotecnico){
                    $aux_espesor = $producto->acuerdotecnico->at_espesor;
                }
                Otdet::updateOrCreate(
                    ['id' => $request->otdet_id[$i], 'ot_id' => $id],
                    [
                        'producto_id' => $request->producto_id[$i],
                        'cant' => $request->cant[$i],
                        'cantprod' => $request->cant[$i],
                        'espesorprod' => $aux_espesor,
                        'unidadmedida_id' => $request->unidadmedidainp_id[$i],
                        'obs' => $request->otdet_obs[$i],
                        'kg' => $aux_itemkg,
                        'kgprod' => $aux_itemkg,
                        'requiere_fabricacion' => 1
                    ]
                );
                $Tkgtotal += $aux_itemkg;
            }
        }
        return redirect('ot')->with([
                                        'mensaje'=>'Registro Actualizado con exito.',
                                        'tipo_alert' => 'alert-success'
                                    ]);
    }

    public function procesar(Request $request)
    {
        if ($request->ajax()) {
            $ot = Ot::findOrFail($request->id);
            if(strtotime($ot->updated_at) != $request->updatednum_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro no pudo ser Actualizado. Registro Editado por otro usuario. Fecha Hora: '.$ot->updated_at,
                    'tipo_alert' => 'error'
                ]);    
            }

            $request1 = new Request();
            $request1->merge(['modulo_id' => 31]);
            $request1->request->set('modulo_id', 31);
            $request1->merge(['deldesbloqueo' => 0]);
            $request1->request->set('deldesbloqueo', 0);
            $clibloq = clienteBloqueado($ot->cliente_id,0,$request1);
            if(!is_null($clibloq["bloqueo"])){
                return redirect('ot')->with([
                    "mensaje" => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                    "tipo_alert" => "alert-error"
                ]);
            }
            $request1->merge(['deldesbloqueo' => 1]);
            $request1->request->set('deldesbloqueo', 1);
            $clibloq = clienteBloqueado($ot->cliente_id,0,$request1);

            //dd($request);

            $ot->aprobstatus = 1;
            $ot->obsrechazo = null;
            $ot->aprobusu_id = auth()->id();
            $ot->aprobfechahora = date("Y-m-d H:i:s");
            $ot->updated_at = date("Y-m-d H:i:s");
            if($ot->save()){
                return response()->json([
                                            'mensaje' => 'Registro guardo con exito.',
                                            'status' => '0',
                                            'id' => $ot->id,
                                            'nfila' => $ot->id,
                                            'dte_id' => $ot->id,
                                        ]);
            } else {
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Ocurrio un error al intenta guardar.",
                    'tipo_alert' => 'error'
                ]);
            }
        }
    }

    public function anular(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            $ot = Ot::findOrFail($request->id);
            if(strtotime($ot->updated_at) != $request->updatednum_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro no pudo ser Actualizado. Registro Editado por otro usuario. Fecha Hora: '.$ot->updated_at,
                    'tipo_alert' => 'error'
                ]);    
            }
            if(!isset($request->obs) or $request->obs == "" or $request->obs == null){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'El campo observacion es obligatorio',
                    'tipo_alert' => 'error'
                ]);    
            }

            //dd($request);

            $ot->updated_at = date("Y-m-d H:i:s");
            if($ot->save()){
                $request->request->add(['ot_id' => $ot->id]);
                $request->request->add(['usuario_id' => auth()->id()]);
                $otanul = OtAnul::create($request->all());

                return response()->json([
                                            'mensaje' => 'Registro guardo con exito.',
                                            'status' => '0',
                                            'id' => $ot->id,
                                            'nfila' => $ot->id,
                                            'dte_id' => $ot->id,
                                        ]);
            } else {
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Ocurrio un error al intenta guardar.",
                    'tipo_alert' => 'error'
                ]);
            }
        }
    }

    public function exportPdf($id)
    {
        if(can('ver-pdf-ot',false)){
            $ot = Ot::findOrFail($id);
            //dd($ot);
            $otdets = $ot->otdets()->get();
            //dd($otdets);
            $empresa = Empresa::orderBy('id')->get();
            $rut = number_format( substr ( $ot->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $ot->cliente->rut, strlen($ot->cliente->rut) -1 , 1 );
            //dd($empresa[0]['iva']);
            $pdf = PDF::loadView('ot.reporte', compact('ot','otdets','empresa'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream(str_pad($ot->id, 5, "0", STR_PAD_LEFT) .' - '. $ot->cliente->razonsocial . '.pdf');
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
    }

}

function consultaindex(){
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql = "SELECT ot.id,ot.fechahora,cliente.razonsocial,cliente.rut,
    otoc.oc_id,otoc.oc_file,ot.obsrechazo,
    comuna.nombre as comuna_nombre,
    UNIX_TIMESTAMP(ot.updated_at) as updatednum_at,ot.updated_at,
    clientebloqueado.descripcion as clientebloqueado_descripcion,
    if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
    cliente.limitecredito,
    IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
    IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
    IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
    IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
    modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
    IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs,
    sum(otdet.kg) as aux_totalkg,
    '' as obsdev, '' as rutaeditar
    FROM ot INNER JOIN otdet
    ON ot.id = otdet.ot_id
    INNER JOIN cliente
    ON cliente.id = ot.cliente_id AND isnull(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = cliente.comunap_id AND isnull(comuna.deleted_at)
    LEFT JOIN clientebloqueado
    ON clientebloqueado.cliente_id = ot.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    LEFT JOIN vista_datacobranza
    ON vista_datacobranza.cliente_id = ot.cliente_id
    LEFT JOIN clientedesbloqueado
    ON clientedesbloqueado.cliente_id = ot.cliente_id and isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
    LEFT JOIN clientedesbloqueadomodulo
    ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 16
    LEFT JOIN modulo
    ON modulo.id = clientedesbloqueadomodulo.modulo_id
    LEFT JOIN clientedesbloqueadopro
    ON clientedesbloqueadopro.cliente_id = ot.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
    LEFT JOIN otoc
    ON otoc.ot_id = ot.id
    WHERE (ot.aprobstatus = 0 or ot.aprobstatus = 3)
    AND ot.id NOT IN (SELECT otanul.ot_id FROM otanul WHERE ISNULL(otanul.deleted_at))
    AND ot.sucursal_id in ($sucurcadena)
    AND ot.id NOT IN (SELECT otnotaventa.ot_id FROM otnotaventa WHERE ISNULL(otnotaventa.deleted_at))
    GROUP BY ot.id;";
    //dd($sql);
    $datas = DB::select($sql);
    foreach ($datas as &$data) {
        $data->rutaeditar = route('editar_ot', ['id' => $data->id,'updatednum_at' => $data->updatednum_at]);
    }
    //dd($datas);
    return $datas;

}
