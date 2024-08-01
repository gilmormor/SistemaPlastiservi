<?php

namespace App\Http\Controllers;

use App\Events\GuardarGuiaDespacho;
use App\Http\Requests\ValidarDTE;
use App\Models\AreaProduccion;
use App\Models\CentroEconomico;
use App\Models\Cliente;
use App\Models\ClienteDesBloqueado;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DataCobranza;
use App\Models\DespachoOrd;
use App\Models\Dte;
use App\Models\DteAnul;
use App\Models\DteDespachoOrd;
use App\Models\DteDet;
use App\Models\DteDet_DespachoOrdDet;
use App\Models\DteDte;
use App\Models\DteGuiaDesp;
use App\Models\DteOC;
use App\Models\Empresa;
use App\Models\Foliocontrol;
use App\Models\Giro;
use App\Models\GuiaDespDet;
use App\Models\InvBodegaProducto;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\InvMovDet_BodOrdDesp;
use App\Models\InvMovModulo;
use App\Models\NotaVentaCerrada;
use App\Models\NotaVentaDetalle;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;


class DteGuiaDespController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-guia-despacho');
        $empresa = Empresa::findOrFail(1);
        $tablashtml['stabloxdeusiscob'] = $empresa->stabloxdeusiscob;
        return view('dteguiadesp.index',compact('tablashtml'));
    }

    public function dteguiadesppage(){
        $datas = consultaindex();
        return datatables($datas)->toJson();
    }

    public function listarorddesp()
    {
        can('listar-orden-despacho');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $empresa = Empresa::findOrFail(1);
        $tablashtml['stabloxdeusiscob'] = $empresa->stabloxdeusiscob;
        return view('dteguiadesp.listardespachoord', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));

        //can('listar-guia-despacho');
        $aux_vista = 'G';
        //dd('entro');
        return view('guiadesp.listardespachoord', compact('aux_vista'));
    }

    public function listarorddesppage(Request $request){
        $datas = consultalistarorddesppage($request);
        //dd($datas);
        return datatables($datas)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear($id,$updated_at)
    {
        can('crear-dte-guia-despacho');
        $data = DespachoOrd::findOrFail($id);

        if($updated_at != $data->updated_at){
            return redirect('dteguiadesp/listarorddesp')->with([
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'alert-error'
            ]);
        }
        if(!is_null($data->guiadespacho) or $data->guiadespacho != ""){
            return redirect('dteguiadesp/listarorddesp')->with([
                'mensaje'=>"Guia de Despacho $data->guiadespacho fue generada por otro usuario!",
                'tipo_alert' => 'alert-error'
            ]);
        }
        $request = new Request();
        $request->merge(['modulo_id' => 9]);
        $request->request->set('modulo_id', 9);
        $request->merge(['notaventa_id' => $data->notaventa_id]);
        $request->request->set('notaventa_id', $data->notaventa_id);
        //$cliente = Cliente::findOrFail($request->cliente_id);
        $clibloq = clienteBloqueado($data->notaventa->cliente_id,0,$request);
        //dd($clibloq);
        if(!is_null($clibloq["bloqueo"])){    
            return redirect('dteguiadesp/listarorddesp')->with([
                "mensaje" => "Cliente Bloqueado: " . $clibloq["bloqueo"],
                "tipo_alert" => "alert-error"
            ]);
        }

        $detalles = $data->despachoorddets;
        /*
        $dte = $data->guiadesp->whereNull('deleted_at');
        //dd(GuiaDespAnul::whereNull('deleted_at')->pluck('guiadesp_id')->toArray());
        $dte = $dte->whereNotIn('id',GuiaDespAnul::whereNull('deleted_at')->pluck('guiadesp_id')->toArray());
        if(count($dte) > 0){
            return redirect('guiadesp/listarorddesp')->with([
                'mensaje'=>'Guia despacho creada o procesada por otro usuario!',
                'tipo_alert' => 'alert-error'
            ]);
 
        }
        */
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $comunas = Comuna::orderBy('id')->get();
        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $centroeconomicos = $data->notaventa->sucursal->centroeconomicos;
        /* dd($centroeconomicos);
        $centroeconomicos = CentroEconomico::orderBy('id')
                            ->where("sucursal_")
                            ->get(); */
        $aux_status = "1"; //Crear
        //dd($data);
        return view('dteguiadesp.crear', compact('data','detalles','comunas','empresa','tipoentregas','centroeconomicos','aux_status'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarDTE $request)
    {
        can('guardar-dte-guia-despacho');
        $despachoord = DespachoOrd::findOrFail($request->despachoord_id);
        if($request->updated_at != $despachoord->updated_at){
            return redirect('dteguiadesp/listarorddesp')->with([
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $despachoord->updated_at = date("Y-m-d H:i:s");
        $despachoord->save();
        if(!is_null($despachoord->guiadespacho) or $despachoord->guiadespacho != ""){
            return redirect('dteguiadesp/listarorddesp')->with([
                'mensaje'=>"Guia de Despacho $despachoord->guiadespacho fue generada por otro usuario!",
                'tipo_alert' => 'alert-error'
            ]);
        }
        foreach ($despachoord->notaventa->cliente->clientebloqueados as $clientebloqueado) {
            return redirect('dteguiadesp/listarorddesp')->with([
                'id' => 0,
                'mensaje'=>'No es posible hacer Guia de Despacho, Cliente Bloqueado: ' . $clientebloqueado->descripcion,
                'tipo_alert' => 'alert-error'
            ]);
        }
        if(is_null($despachoord->notaventa->cliente->giro) or empty($despachoord->notaventa->cliente->giro) or $despachoord->notaventa->cliente->giro ==""){
            return redirect('dteguiadesp/listarorddesp')->with([
                'id' => 0,
                'mensaje'=>'Giro Cliente RUT ' . $despachoord->notaventa->cliente->rut . ' '. $despachoord->notaventa->cliente->razonsocial .' no puede estar vacio.',
                'tipo_alert' => 'alert-error'
            ]);
        }
    
        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            return redirect('dteguiadesp/listarorddesp')->with([
                'mensaje'=>'Guia Despacho sin items, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $foliocontrol = Foliocontrol::findOrFail(2);
        if($cont_producto > $foliocontrol->maxitemxdoc ){
            return redirect('dteguiadesp/listarorddesp')->with([
                'mensaje' => 'Total items documento: ' . $cont_producto . '. Maximo items permitido por documento: ' . $foliocontrol->maxitemxdoc,
                'tipo_alert' => 'alert-error'
            ]);
        }

        if(count($despachoord->despachoorddets) != $cont_producto){
            return redirect('dteguiadesp/listarorddesp')->with([
                'mensaje'=>'Cantidad de item diferentes a la Orden de Despacho Original.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        if(!empty($request->despachoord_id)){
            if($cont_producto>0){
                for ($i=0; $i < $cont_producto ; $i++){
                    if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                        $producto = Producto::findOrFail($request->producto_id[$i]);
                        $montoitem = round($request->montoitem[$i],0);
                        if($montoitem < 1){
                            return redirect('dteguiadesp/listarorddesp')->with([
                                'mensaje'=> 'SubTotal menor a uno (1). Producto ID:' . $producto->id . ' Nombre: ' . $producto->nombre,
                                'tipo_alert' => 'alert-error'
                            ]);                
                        }
                    }
                }
            }
        }

        $request1 = new Request();
        $request1->merge(['modulo_id' => 9]);
        $request1->request->set('modulo_id', 9);
        $request1->merge(['notaventa_id' => $despachoord->notaventa_id]);
        $request1->request->set('notaventa_id', $despachoord->notaventa_id);
        $request1->merge(['deldesbloqueo' => 1]);
        $request1->request->set('deldesbloqueo', 1);
        $clibloq = clienteBloqueado($despachoord->notaventa->cliente_id,0,$request1);
        if(!is_null($clibloq["bloqueo"])){
            $request1 = new Request();
            $request1->merge(['cliente_id' => $despachoord->notaventa->cliente_id]);
            $request1->request->set('cliente_id', $despachoord->notaventa->cliente_id);
            $respuesta = DataCobranza::llenartabla($request1);

            return redirect('dteguiadesp/listarorddesp')->with([
                "mensaje" => "Cliente Bloqueado: " . $clibloq["bloqueo"],
                "tipo_alert" => "alert-error"
            ]);
        }

        $aux_indtraslado = $request->tipoguiadesp;
        if($aux_indtraslado == 30){ //ESTO ES TEMPORAL POR EL MES DE AGOSTO 2023 PARA QUE PUEDAN HACER GUIA DE TRASLADO DE LAS GUIAS HECHAS POR EPROPLAS
            $aux_indtraslado = 6;
        }
        //dd(sanear_string("GILMER Ñ x&@x  Moreno"));
        if($request->tipoguiadesp == 20){ 
            //SI $request->tipoguiadesp = 20, GENERO DE FORMA AUTOMATICA LA GUIA DE TRASLADO, LUEGO SE GENERA LA GUIA DE VENTA
            //PASO EL VALOR DE 6 A LA FUNCION PARA IDENTIFICAR QUE VOY A GENERAR LA GUIA DE TRASLADO
            $respuesta = guardarDTE($request,6,$cont_producto);
            if($respuesta["id"] != 1){
                return redirect('dteguiadesp/listarorddesp')->with([
                    'mensaje'=>$respuesta["mensaje"] ,
                    'tipo_alert' => 'alert-error'
                ]);    
            }    
            //AL SER $request->tipoguiadesp = 9 Y HABERSE GENERADO LA GUI DE TRASLADO, DEBO CAMBIAR $aux_indtraslado A 1, PARA QUE LUEGO SE GENERE LA GUIA DE VENTA
            $aux_indtraslado = 1; 
        }
        $respuesta = guardarDTE($request,$aux_indtraslado,$cont_producto);
        if($respuesta["id"] == 1){
            $foliocontrol = Foliocontrol::findOrFail(2);
            $aux_foliosdisp = $foliocontrol->ultfoliohab - $foliocontrol->ultfoliouti;
            if($aux_foliosdisp <= $foliocontrol->folmindisp){
                return redirect('dteguiadesp')->with([
                    'mensaje'=>"Guia Despacho creada con exito. Quedan $aux_foliosdisp folios disponibles!" ,
                    'tipo_alert' => 'alert-error'
                ]);
            }else{
                return redirect('dteguiadesp')->with([
                    'mensaje'=>'Guia Despacho creada con exito.',
                    'tipo_alert' => 'alert-success'
                ]);    
            }
    
        }else{
            return redirect('dteguiadesp/listarorddesp')->with([
                'mensaje'=>$respuesta["mensaje"] ,
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
        can('editar-dte-guia-despacho');
        $dteguiadesp = Dte::findOrFail($id);
        //dd(date("d/m/Y", strtotime($dteguiadesp->fchemis)));
        if($dteguiadesp->statusgen == 1){
            return redirect('dteguiadesp')->with([
                'mensaje'=>'Guia Despacho ya fue Generada! Nro: ' . $dteguiadesp->nrodocto ,
                'tipo_alert' => 'alert-error'
            ]);
        }

        if($dteguiadesp->guiadespanul){
            return redirect('dteguiadesp')->with([
                'mensaje'=>'Registro fué anulado!',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $detalles = $dteguiadesp->dtedets;
        $data = DespachoOrd::findOrFail($dteguiadesp->dteguiadesp->despachoord_id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        //dd($data->notaventa->cliente->clientedirecs);
        $comunas = Comuna::orderBy('id')->get();
        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $centroeconomicos = CentroEconomico::orderBy('id')->get();
        $aux_status = "2"; //Editar
        //dd($data);
        return view('dteguiadesp.editar', compact('data','detalles','dteguiadesp','comunas','empresa','tipoentregas','centroeconomicos','aux_status'));
    }
     
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarDTE $request, $id)
    {
        can('guardar-guia-despacho');
        $dte = Dte::findOrFail($id);
        if($request->updated_at != $dte->updated_at){
            return redirect('dteguiadesp')->with([
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            return redirect('dteguiadesp')->with([
                'mensaje'=>'Guia Despacho sin items, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        if(count($dte->dtedets) != $cont_producto){
            return redirect('dteguiadesp')->with([
                'mensaje'=>'Cantidad de item diferentes a la Orden de espacho Original.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $empresa = Empresa::findOrFail(1);

        $date = str_replace('/', '-', $request->fchemis);
        $dte->fchemis = date('Y-m-d', strtotime($date));

        $mntneto = 0;
        $kgtotal = 0;
        //dd($dte->dteguiadesp->despachoord);
        foreach($dte->dteguiadesp->despachoord->despachoorddets as $despachoorddet){
            $NVDet = $despachoorddet->notaventadetalle;
            $mntneto += (($NVDet->subtotal/$NVDet->cant) * $despachoorddet->cantdesp);
            $kgtotal += (($NVDet->totalkilos/$NVDet->cant) * $despachoorddet->cantdesp);
        }
        $dte->obs = $request->obs;
        $dte->centroeconomico_id = $request->centroeconomico_id;
        $dte->tipodespacho = $request->tipodespacho;
        $dte->indtraslado = $request->indtraslado;
        $dte->mntneto = $mntneto;
        $dte->tasaiva = $empresa->iva;
        $dte->iva = round($empresa->iva * $mntneto/100);
        $dte->mnttotal = round($mntneto + $dte->iva);
        $dte->kgtotal = $kgtotal;
        $dte->updated_at = date("Y-m-d H:i:s");
        $dte->save();

        $dte->dteguiadesp->update([
            'ot' => $request->ot,
            'tipoentrega_id' => $request->tipoentrega_id,
            'comunaentrega_id' => $request->comunaentrega_id,
            'lugarentrega' => $request->lugarentrega
        ]);

        //$dte->ot = $request->ot;


        //$notaventaid = 1;
        //SI ESTA VACIO EL NUMERO DE COTIZACION SE CREA EL DETALLE DE LA NOTA DE VENTA DE LA TABLA DEL LADO DEL CLIENTE
        //SI NO ESTA VACIO EL NUMERO DE COTIZACION SE LLENA EL DETALLE DE LA NOTA DE VENTA DE LA TABLA DETALLE COTIZACION
        $Tmntneto = 0;
        $Tiva = 0;
        $Tmnttotal = 0;
        $Tkgtotal = 0;

        if(!empty($request->despachoord_id)){
            if($cont_producto>0){
                for ($i=0; $i < $cont_producto ; $i++){
                    if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                        $dtedet = DteDet::findOrFail($request->iddet[$i]);
                        $dtedet->nmbitem = $request->nmbitem[$i];
                        $dtedet->dscitem = $request->dscitem[$i];
                        $dtedet->qtyitem = $request->qtyitem[$i];
                        $dtedet->unmditem = $request->unmditem[$i];
                        $dtedet->unidadmedida_id = $request->unidadmedida_id[$i];
                        $dtedet->prcitem =  $request->montoitem[$i]/$request->qtyitem[$i]; // $request->prcitem[$i]
                        $dtedet->montoitem = round($request->montoitem[$i],0);
                        $dtedet->obsdet = $request->obsdet[$i];
                        $dtedet->itemkg = $request->itemkg[$i];

                        $Tmntneto += $dtedet->montoitem; //$request->montoitem[$i];
                        $Tkgtotal += $request->itemkg[$i];

                        $dtedet->save();
                    }
                }
            }
        }
        if($Tmntneto>0){
            $Tiva = round(($empresa->iva/100) * $Tmntneto);
            $Tmnttotal = round((($empresa->iva/100) + 1) * $Tmntneto);    
        }
        $dte = Dte::findOrFail($id);
        $dte->mntneto = $Tmntneto;
        $dte->tasaiva = $empresa->iva;
        $dte->iva = $Tiva;
        $dte->mnttotal = $Tmnttotal;
        $dte->kgtotal = $Tkgtotal;   
        $dte->save();
        return redirect('dteguiadesp')->with([
                                            'mensaje'=>'Guia Despacho Actualizada con exito.',
                                            'tipo_alert' => 'alert-success'
                                        ]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function totalizarindex(){
        $respuesta = array();
        $datas = consultaindex();
        $kgtotal = 0;
        //$aux_totaldinero = 0;
        foreach ($datas as $data) {
            $kgtotal += $data->kgtotal;
            //$aux_totaldinero += $data->subtotal;
        }
        $respuesta['kgtotal'] = $kgtotal;
        //$respuesta['aux_totaldinero'] = $aux_totaldinero;
        return $respuesta;
    }

    public function guardardteguiadesp(Request $request)
    {
        if ($request->ajax()) {
            $dte = Dte::findOrFail($request->guiadesp_id);
            /*
            $tipoArch = "XML";
            $ArchivoTXT = dteguiadesp($dte->id,"1234",$tipoArch);
            dd($ArchivoTXT);
            */

            if($dte->updated_at != $request->updated_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro fué creado o modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            $empresa = Empresa::findOrFail(1);
            $soap = new SoapController();
            $Estado_DTE = $soap->Estado_DTE($empresa->rut,$dte->foliocontrol->tipodocto,$dte->nrodocto);

            $despachoord = DespachoOrd::findOrFail($dte->dteguiadesp->despachoord_id);
            $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$despachoord->notaventa_id)->get();
            if(count($notaventacerrada) == 0){
                $aux_bandera = true;
                foreach ($despachoord->despachoorddets as $despachoorddet) {
                    //dd($despachoorddet->despachoorddet_invbodegaproductos);
                    $aux_respuesta = InvBodegaProducto::validarExistenciaStock($despachoorddet->despachoorddet_invbodegaproductos,$request->invbodega_id);
                    if($aux_respuesta["bandera"] == false){
                        //dd($despachoorddet->despachoorddet_invbodegaproductos);
                        //dd($request->invbodega_id);
                        $aux_bandera = $aux_respuesta["bandera"];
                        break;
                    }
                }
                if($aux_bandera){
                    $invmodulo = InvMovModulo::where("cod","ORDDESP")->get();
                    if(count($invmodulo) == 0){
                        /*
                        $foliocontrol->bloqueo = 0;
                        $foliocontrol->save();
                        */
                        return response()->json([
                            'mensaje' => 'MensajePersonalizado',
                            'menper' => "No existe modulo SOLDESP"    
                        ]);
                    }
                    $invmoduloBod = InvMovModulo::findOrFail($invmodulo[0]->id);
                    $aux_DespachoBodegaId = $invmoduloBod->invmovmodulobodents[0]->id; //Id Bodega Despacho (La bodega despacho debe ser unica)
                    $aux_bodegadespacho = 0;
                    foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                        //BUSCAR BODEGA DESPACHO DE SUCURSAL 
                        if($invmovmodulobodent->sucursal_id == $despachoord->notaventa->sucursal_id){
                            $aux_bodegadespacho = $invmovmodulobodent->id;
                        }
                    }
                    if($aux_bodegadespacho == 0){
                        /*
                        $foliocontrol->bloqueo = 0;
                        $foliocontrol->save();
                        */
                        return response()->json([
                            'mensaje' => 'No existe Bodega Despacho en Sucursal: ' . $despachoord->notaventa->sucursal->nombre
                        ]);
                    }
                    /*
                    $empresa = Empresa::findOrFail(1);
                    $soap = new SoapController();
                    $aux_folio = $dte->nrodocto;
                    if(is_null($dte->nrodocto)){
                        $bandNoExisteFolio = true;
                        do {
                            $Solicitar_Folio = $soap->Solicitar_Folio($empresa->rut,"52");
                            if(isset($Solicitar_Folio->Estatus)){
                                if($Solicitar_Folio->Estatus == 0){
                                    $Estado_DTE = $soap->Estado_DTE($empresa->rut,"52",$Solicitar_Folio->Folio);
                                    if($Estado_DTE->Estatus == 3){
                                        $bandNoExisteFolio = false;
                                        $aux_folio = $Solicitar_Folio->Folio;
                                    }
                                }else{
                                    $foliocontrol->bloqueo = 0;
                                    $foliocontrol->save();
                                    //dd($Solicitar_Folio);
                                    return response()->json([
                                        'id' => 0,
                                        'mensaje'=>'Error: #' . $Solicitar_Folio->Estatus . " " . $Solicitar_Folio->MsgEstatus,
                                        'tipo_alert' => 'error'                
                                    ]);    
                                }
                            }else{
                                $foliocontrol->bloqueo = 0;
                                $foliocontrol->save();    
                                return response()->json([
                                    'id' => 0,
                                    'mensaje'=>'Error: ' . $Solicitar_Folio,
                                    'tipo_alert' => 'error'                
                                ]);    
                            }
                        }while($bandNoExisteFolio);
                    }
                    $tipoArch = "XML";
                    $ArchivoTXT = dteguiadesp($dte->id,$aux_folio,$tipoArch);
                    $Carga_TXTDTE = $soap->Carga_TXTDTE($ArchivoTXT,$tipoArch);
                    //$Carga_TXTDTE = $soap->Carga_TXTDTE($ArchivoTXT,"XML");
                    if(isset($Carga_TXTDTE->Estatus)){
                        //ACTUALIZO EL CAMPO nrodocto
                        //SI OCURRIO ALGUN ERROR SE QUE TENGO EL FOLIO, 
                        //SE QUE NO LO PUEDO VOLVER A PEDIR PORQUE POR ALGUNA RAZON SE GENERO UN ERROR EN EL ULTIMO FOLIO SOLICITADO
                        $aux_giadesp = Dte::where('id', $dte->id)
                                ->update(['nrodocto' => $aux_folio]);
                        if($Carga_TXTDTE->Estatus == 0){
                            $dte->fchemisgen = date("Y-m-d H:i:s");
                            //$date = str_replace('/', '-', $request->fchemis);
                            //$dte->fchemis = date('Y-m-d', strtotime($date));
                            //$dte->fchemis = date("Y-m-d H:i:s");
                            $dte->statusgen = 1;
                            $dte->aprobstatus = 1;
                            $dte->aprobusu_id = auth()->id();
                            $dte->aprobfechahora = date("Y-m-d H:i:s");
                
                            $dte->save();
                            //$fchemisDMY = date("d-m-Y_His",strtotime($dte->fchemis));
                            $nombreArchPDF =  $foliocontrol->nombrepdf . str_pad($aux_folio, 8, "0", STR_PAD_LEFT);
                            Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '.xml', $Carga_TXTDTE->XML);
                            Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '.pdf', $Carga_TXTDTE->PDF);
                            Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '_cedible.pdf', $Carga_TXTDTE->PDFCedible);

                            $pdf = new Fpdi();
                            $files = array("storage/facturacion/dte/procesados/" . $nombreArchPDF .  ".pdf","storage/facturacion/dte/procesados/" . $nombreArchPDF .  "_cedible.pdf");
                            foreach ($files as $file) {
                                $pageCount = $pdf->setSourceFile($file);
                                for ($pagNo=1; $pagNo <= $pageCount; $pagNo++) { 
                                    $template = $pdf->importPage($pagNo);
                                    $size = $pdf->getTemplateSize($template);
                                    $pdf->AddPage($size['orientation'], $size);
                                    $pdf->useTemplate($template);
                                }
                            }
                            $pdf->Output("F","storage/facturacion/dte/procesados/" . $nombreArchPDF .  "_U.pdf");

                            //dd($Carga_TXTDTE);
                        }else{
                            return response()->json([
                                'id' => 0,
                                'mensaje'=>'Error: #' . $Carga_TXTDTE->Estatus . " " . $Carga_TXTDTE->MsgEstatus,
                                'tipo_alert' => 'error'                
                            ]);    
                        }
                    }else{
                        $foliocontrol->bloqueo = 0;
                        $foliocontrol->save();
                        return response()->json([
                            'id' => 0,
                            'mensaje'=>'Error: ' . $Solicitar_Folio,
                            'tipo_alert' => 'error'                
                        ]);
                    }
                    */
    
                    $invmov_array = array();
                    $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                    $invmov_array["annomes"] = $aux_respuesta["annomes"];
                    $invmov_array["desc"] = "Salida de BD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $despachoord->id;
                    $invmov_array["obs"] = "Salida de BD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $despachoord->id;
                    $invmov_array["invmovmodulo_id"] = $invmoduloBod->id; //Guia de Despacho
                    $invmov_array["idmovmod"] = $request->id;
                    $invmov_array["invmovtipo_id"] = 2;
                    $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
                    $invmov_array["usuario_id"] = auth()->id();
                    $arrayinvmov_id = array();
                    
                    $invmov = InvMov::create($invmov_array);
                    array_push($arrayinvmov_id, $invmov->id);
                    foreach ($despachoord->despachoorddets as $despachoorddet) {
                        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                            $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                                ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $aux_bodegadespacho],
                                [
                                    'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                                    'invbodega_id' => $aux_bodegadespacho
                                ]
                            );
    
                            $array_invmovdet = $oddetbodprod->attributesToArray();
                            $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                            $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                            $array_invmovdet["invbodega_id"] = $aux_bodegadespacho;
                            $array_invmovdet["sucursal_id"] = $despachoord->notaventa->sucursal_id; //$invbodegaproducto->invbodega->sucursal_id;
                            $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                            $array_invmovdet["invmovtipo_id"] = 2;
                            $array_invmovdet["invmov_id"] = $invmov->id;
                            $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                            $array_invmovdet["cantxgrupo"] = 1;
                            $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                            $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                            $invmovdet = InvMovDet::create($array_invmovdet);
                            $invmovdet_bodorddesp = InvMovDet_BodOrdDesp::create([
                                'invmovdet_id' => $invmovdet->id,
                                'despachoorddet_invbodegaproducto_id' => $oddetbodprod->id
                            ]);
                        }
                    }
                    $dte = Dte::findOrFail($request->guiadesp_id);
                    //return updatenumguia($despachoord,$dte,$foliocontrol,$request);
                    return updatenumguia($despachoord,$dte,$request);
                }else{
                    /*
                    $foliocontrol->bloqueo = 0;
                    $foliocontrol->save();
                    */
                    return response()->json([
                        'id' => 0,
                        'mensaje'=> "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"],
                        'tipo_alert' => 'error'
                    ]);

                    return response()->json([
                        'mensaje' => "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"],
                        'menper' => "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"]
                    ]);
                }
            }else{
                /*
                $foliocontrol->bloqueo = 0;
                $foliocontrol->save();
                */
                $mensaje = 'Nota Venta fue cerrada: Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at));
                return response()->json(['mensaje' => $mensaje]);
            }
        } else {
            abort(404);
        }    
    }

    public function validarupdated(Request $request)
    {
        //dd($request);
        $dte = Dte::findOrFail($request->guiadesp_id);
        if($request->updated_at == $dte->updated_at){
            return response()->json(['mensaje' => 'ok']);
        }else{
            return response()->json([
                'id' => 0,
                'mensaje'=>'Registro no puede editado, fué modificado por otro usuario.',
                'tipo_alert' => 'error'
            ]);
        }
    }

    public function guiadespanul(Request $request)
    {
        $dte = Dte::findOrFail($request->guiadesp_id);
        if($request->updated_at != $dte->updated_at){
            /*
            return redirect('dteguiadesp')->with([
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'alert-error'
            ]);
            */
            return [
                'status' => 0,
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'error'
            ];

        }
        //VALIDAR SI YA FUE ANULADA LA GUIA
        if(isset($dte->dteanul)){
            return [
                'status' => 0,
                'mensaje'=>'Guia despacho ya fue anulada, actualiza la pagina F5.',
                'tipo_alert' => 'error'
            ];
        }
        /*
        $request->request->add(['usuario_id' => auth()->id()]);
        $dteanul = DteAnul::create($request->all());
        */

        if($dte->dteguiadesp){
            //dd($dte->dteguiadesp->despachoord_id);
            if(isset($dte->dteguiadesp->despachoord)){
                $despachoord = DespachoOrd::findOrFail($dte->dteguiadesp->despachoord_id);
                //dd($despachoord->dteguiadesps);
                //ANULO LAS GUIAS ASOCIADAS A LA ORDEN DE DESPACHO
                foreach ($despachoord->dteguiadesps as $dteguiadesp) {
                    if(!isset($dteguiadesp->dte->dteanul)){
                        $arraydteguiadesp = [
                            "dte_id" => $dteguiadesp->dte_id,
                            "despachoord_id" => $request->despachoord_id,
                            "obs" => $request->obs,
                            "motanul_id" => $request->motanul_id,
                            "moddevgiadesp_id" => $request->moddevgiadesp_id,
                            "usuario_id" => auth()->id()
                        ];
                        $dteanul = DteAnul::create($arraydteguiadesp);
                    }
                }    
            }else{
                //ENTRA AQUI CUANDO LA GUIA ES DIRECTA,
                //NO TIENE OD ASOCIADA
                $arraydteguiadesp = [
                    "dte_id" => $request->dte_id,
                    "despachoord_id" => $request->despachoord_id,
                    "obs" => "Guia directa GI",
                    "motanul_id" => 5,
                    "moddevgiadesp_id" => "GI", //Guia Directa
                    "usuario_id" => auth()->id()
                ];
                $dteanul = DteAnul::create($arraydteguiadesp);        
            }
        }
        $dte->updated_at = date("Y-m-d H:i:s");
        if($dte->save()){
            //SI LA GUIA DESPACHO ES DIRECTA ES DECIR NO VIENE DE UNA ORDEN DE DESPACHO
            //NO ENTRA EN LA CONDICION
            if($dte->dteguiadesp->despachoord_id){
                $despachoord = DespachoOrd::findOrFail($dte->dteguiadesp->despachoord_id);
                $despachoord->guiadespacho = NULL;
                $despachoord->guiadespachofec = NULL;
                $despachoord->updated_at = date("Y-m-d H:i:s");
                if($despachoord->save()){
                    return response()->json(['mensaje' => 'ok']);
                }
            }
            return response()->json(['mensaje' => 'ok']);
        }
        //SI NO EJECUTA EL RETURN ANTERIOR, EJECUTA ESTE RETURN
        /*
        return redirect('dteguiadesp')->with([
            'mensaje'=>'No se actualizaron los datos, ocurrio un error al intentar guardar!',
            'tipo_alert' => 'alert-error'
        ]);
        */
        return [
            'status' => 0,
            'mensaje'=>'No se actualizaron los datos, ocurrio un error al intentar guardar!',
            'tipo_alert' => 'error'
        ];

    }

    public function guiadespanulsininv(Request $request)
    {
        $dte = Dte::findOrFail($request->id);
        if($request->updated_at != $dte->updated_at){
            return [
                'status' => 0,
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'error'
            ];
        }
        $request->request->add(['usuario_id' => auth()->id()]);
        $dteanul = DteAnul::create($request->all());
        $dte->updated_at = date("Y-m-d H:i:s");
        if($dte->save()){
            return [
                'status' => 1,
                'mensaje'=>'Informacion procesada con exito!',
                'tipo_alert' => 'success'
            ];
        }
        //SI NO EJECUTA EL RETURN ANTERIOR, EJECUTA ESTE RETURN
        return [
            'status' => 0,
            'mensaje'=>'No se actualizaron los datos, ocurrio un error al intentar guardar!',
            'tipo_alert' => 'error'
        ];
    }


    public function consultarDteGuiaDesp(Request $request)
    {
        if ($request->ajax()) {
            $dte = Dte::findOrFail($request->id);
            if ($dte) {
                return response()->json([
                                        'mensaje' => 'ok',
                                        'dte' => $dte,
                                        'despachoord_id' => $dte->dteguiadesp->despachoord_id,
                                        'fechafactura' => date("d/m/Y", strtotime($dte->fchemis))
                                        ]);
            } else {
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro no Existe. Id: ' . $request->id,
                    'tipo_alert' => 'error'
                ]);
            }
        } else {
            abort(404);
        }
    }

    public function volverGenDTE(Request $request){
        $dte = Dte::findOrFail($request->dte_id);
        $dteini = Dte::findOrFail($request->dte_id);
        $respuesta = Dte::generardteprueba($dte);
        $dtec = Dte::findOrFail($request->dte_id);
        //dd($respuesta);
        /*
        $respuesta = response()->json([
            'id' => 1
        ]);
        */
        $foliocontrol = Foliocontrol::findOrFail($dte->foliocontrol_id);
        $foliocontrol->bloqueo = 0;
        $foliocontrol->save();
        if($respuesta["id"] == 1){
            $dteini->nrodocto = $dte->nrodocto;
            $dteini->stasubsii = 1;
            if($dteini->save()){
                Dte::guardarPdfXmlSii($dteini->nrodocto,$foliocontrol,$respuesta["Carga_TXTDTE"]);
                return response()->json([
                    'id' => 1,
                    'dte_id' => $request->dte_id,
                    'mensaje'=>'DTE Generado con exito: ' . $dte->nrodocto,
                    'tipo_alert' => 'success',
                    'stasubsii' => $dteini->stasubsii,
                    'stasubcob' => $dteini->stasubcob,
                    'updated_at' => date("Y-m-d H:i:s", strtotime($dteini->updated_at))
                ]);        
            }else{
                return response()->json([
                    'id' => 0,
                    'titulo' => "",
                    'mensaje'=> "Error al Guardar en dte",
                    'tipo_alert' => 'error',
                    'stasubsii' => $dtec->stasubsii,
                    'stasubcob' => $dtec->stasubcob,
                    'updated_at' => ""
                ]);
            }
        }else{
            $stasubsii = isset($respuesta["stasubsii"]) ? $respuesta["stasubsii"] : 0;
            return response()->json([
                'id' => 0,
                'titulo' => $respuesta["titulo"],
                'mensaje'=> $respuesta["mensaje"],
                'tipo_alert' => 'error',
                'stasubsii' => $stasubsii,
                'stasubcob' => $dtec->stasubcob,
                'updated_at' => date("Y-m-d H:i:s", strtotime($dtec->updated_at))
            ]);
        }
    }

    public function procesar(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            $dte = Dte::findOrFail($request->dte_id);
            //dd($dte->dteguiadesp->despachoord);
            if($request->updated_at != $dte->updated_at){
                return response()->json([
                    'id' => 0,
                    'error' => '0',
                    'mensaje' => 'Guia Despacho fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            $despachoord = $dte->dteguiadesp->despachoord;
            if(!$despachoord){
                return response()->json([
                    'id' => 0,
                    'error' => '0',
                    'mensaje' => 'No existe Orden de despacho asociada a Guia Despacho.',
                    'tipo_alert' => 'error'
                ]);
            }
            if($request->despordupdated_at != $despachoord->updated_at){
                return response()->json([
                    'id' => 0,
                    'error' => '0',
                    'mensaje' => 'Orden Despacho fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            $request1 = new Request();
            $request1->merge(['modulo_id' => 10]);
            $request1->request->set('modulo_id', 10);
            $request1->merge(['notaventa_id' => $despachoord->notaventa_id]);
            $request1->request->set('notaventa_id', $despachoord->notaventa_id);
            $request1->merge(['deldesbloqueo' => 1]);
            $request1->request->set('deldesbloqueo', 1);
            $bloqcli = clienteBloqueado($despachoord->notaventa->cliente_id,0,$request1);
            if(!is_null($bloqcli["bloqueo"])){
                return response()->json([
                    'id' => 0,
                    'error' => 1,
                    'mensaje' => "Cliente bloqueado: \n" . $bloqcli["bloqueo"],
                    'tipo_alert' => isset($bloqcli["tipo_alert"]) ? $bloqcli["tipo_alert"] : "error"
                ]);
            }

            $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$despachoord->notaventa_id)->get();
            if(count($notaventacerrada) > 0){
                $mensaje = 'Nota Venta fue cerrada: Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at));
                return response()->json(['mensaje' => $mensaje]);
            }
            $invmodulo = InvMovModulo::where("cod","ORDDESP")->get();
            if(count($invmodulo) == 0){
                return response()->json([
                    'mensaje' => 'MensajePersonalizado',
                    'menper' => "No existe modulo SOLDESP"    
                ]);
            }
            //SOLO TOCA EL INVENTARIO SI ES GUIS DE VENTA indtraslado=1, 
            //SI ES DE TRANSLADO indtraslado=6 GENERADA DE FORMA AUTOMATICA DESDE GUIA DE DESPACHO NO TOCA EL INVENTARIO
            //dd($dte->dteguiadesp->despachoord->despachosol->tipoguiadesp);
            //EN EL CASO QUE EL TIPO DE TRASLADO DE LA GUIA DESP SEA = 6 Y $aux_tipoguiadesp = 6 Ó 30 TOCA EL INVENTARIO
            //SI $aux_indtraslado = 6 Y $aux_tipoguiadesp = 20 NO TOCA EL INVENTARIO, ESTO PORQUE FUE GENERADA A TRAVES DE UNA GUIA DOBLE, ES DECIR QUE SE GENERO AL MISMO TIEMPO UNA GUIA DE TRALADO Y UNA GUIA DE PRECIO
            $aux_indtraslado = $dte->indtraslado;
            $aux_tipoguiadesp = $dte->dteguiadesp->despachoord->despachosol->tipoguiadesp;
            //if($dte->indtraslado == 1){
            if($aux_indtraslado == 1 or ($aux_indtraslado == 6 and ($aux_tipoguiadesp == 6 or $aux_tipoguiadesp == 30))){
                $invmoduloBod = InvMovModulo::findOrFail($invmodulo[0]->id);
                foreach ($despachoord->despachoorddets as $despachoorddet) {
                    //ESTO DEBE IR EN EL PROYECTO FINAL
                    foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                        $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                        $aux_bodegadespacho_id = 0;
                        foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                            //BUSCAR BODEGA DESPACHO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                            if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                                $aux_bodegadespacho_id = $invmovmodulobodent->id;
                                $requestProd = new Request();
                                $requestProd["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                                $requestProd["invbodega_id"] = $aux_bodegadespacho_id;
                                $requestProd["tipo"] = 2;
                                $arrayExistencia = InvBodegaProducto::existencia($requestProd);
                                $existencia = $arrayExistencia["stock"]["cant"];
                                $existencia += $oddetbodprod->cant;
                                //$aux_respuesta = InvBodegaProducto::validarExistenciaStock($despachoorddet->despachoorddet_invbodegaproductos,$aux_bodegadespacho_id);
                                if($existencia < 0){
                                    //$aux_bandera = $aux_respuesta["bandera"];
                                    return response()->json([
                                        'id' => 0,
                                        'status' => "0",
                                        'title' => "Bodega sin stock!",
                                        //'mensaje1' => "Bodega: " . $oddetbodprod->invbodegaproducto->invbodega->nombre . ".\nSucursal: " . $oddetbodprod->invbodegaproducto->invbodega->sucursal->nombre . ".\nIdProd: " . $oddetbodprod->invbodegaproducto->producto_id . "\nNombre: " . $oddetbodprod->invbodegaproducto->producto->nombre. "\nCantidad movimiento: " . $oddetbodprod->cant . "\nStock actual: " . $arrayStock["stock"]["cant"],
                                        'mensaje' => "Bodega: " . $invmovmodulobodent->nombre . ".\nSucursal: " . $invmovmodulobodent->sucursal->nombre . ". IdProd: " . $requestProd["producto_id"] . ".\nNombre: " . $oddetbodprod->invbodegaproducto->producto->nombre . ".\nMov: " . $oddetbodprod->cant . ".\nStock: " . $arrayExistencia["stock"]["cant"],
                                        'tipo_alert' => 'error'
                                    ]);
                                }    
                            }
                        }
                    }
                    //ESTO DEBE IR EN EL PROYECTO FINAL
                }
                $annomes = date("Ym");
                $aux_DespachoBodegaId = $invmoduloBod->invmovmodulobodents[0]->id; //Id Bodega Despacho (La bodega despacho debe ser unica)
                validarSiExisteBodega($despachoord,$invmoduloBod);
                $invmov_array = array();
                $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                $invmov_array["annomes"] = $annomes;
                $invmov_array["desc"] = "Salida de BD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $despachoord->id . " dte_id:" . $dte->id . " GuiaDesp: " . $dte->nrodocto;
                $invmov_array["obs"] = "Salida de BD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $despachoord->id . " dte_id:" . $dte->id . " GuiaDesp: " . $dte->nrodocto;
                $invmov_array["invmovmodulo_id"] = $invmoduloBod->id; //
                $invmov_array["idmovmod"] = $despachoord->id;
                $invmov_array["invmovtipo_id"] = 2;
                $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
                $invmov_array["usuario_id"] = auth()->id();
                $arrayinvmov_id = array();
                
                $invmov = InvMov::create($invmov_array);
                array_push($arrayinvmov_id, $invmov->id);
                foreach ($despachoord->despachoorddets as $despachoorddet) {
                    foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                        //ESTO DEBE IR EN EL PROYECTO FINAL
                        $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                        foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                            //BUSCAR BODEGA DESPACHO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                            if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                                $aux_bodegadespacho_id = $invmovmodulobodent->id;
                            }
                        }
                        //ESTO DEBE IR EN EL PROYECTO FINAL

                        $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                            ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $aux_bodegadespacho_id],
                            [
                                'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                                'invbodega_id' => $aux_bodegadespacho_id
                            ]
                        );

                        $array_invmovdet = $oddetbodprod->attributesToArray();
                        $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                        $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                        $array_invmovdet["invbodega_id"] = $aux_bodegadespacho_id;
                        $array_invmovdet["sucursal_id"] = $invbodegaproducto->invbodega->sucursal_id;
                        $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                        $array_invmovdet["invmovtipo_id"] = 2;
                        $array_invmovdet["invmov_id"] = $invmov->id;
                        $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                        $array_invmovdet["cantxgrupo"] = 1;
                        $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                        $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                        $invmovdet = InvMovDet::create($array_invmovdet);
                        $invmovdet_bodorddesp = InvMovDet_BodOrdDesp ::create([
                            'invmovdet_id' => $invmovdet->id,
                            'despachoorddet_invbodegaproducto_id' => $oddetbodprod->id
                        ]);
                    }
                }
            }
            $despachoord->save(); //SALVO PARA QUE SE ACTUALICE LA FECHA UPDATE_AT
            $request->merge(['statusDTEGuiaDesp' => true]);
            $request->request->set('statusDTEGuiaDesp', true);
            Dte::procesarDTE($request); //PROCESA DTE PARA QUE PASE A GENERAR FACTURA
        } else {
            abort(404);
        }    
    }
}

function consultaindex(){

    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql ="SELECT dte.id,dte.nrodocto,dte.fechahora,dte.fchemis,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,
        notaventa.cliente_id,despachoord.notaventa_id,dte.stasubsii,
        despachoord.despachosol_id,dteguiadesp.despachoord_id,despachoord.fechaestdesp,comuna.nombre as cmnarecep,dte.kgtotal,
        dteguiadesp.tipoentrega_id,tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,
        clientebloqueado.descripcion as clientebloqueado_descripcion,dte.updated_at,despachoord.updated_at as despordupdated_at,
        dtedev.obs as dtedev_obs,usuario.nombre as dtedevusuario_nombre,dte.indtraslado,
        if(cliente.plazopago_id = 1,'Bloqueado: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
        cliente.limitecredito,
        IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
        IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
        IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
        IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
        modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
        IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs
        FROM dte INNER JOIN dteguiadesp
        ON dte.id = dteguiadesp.dte_id AND ISNULL(dte.deleted_at) and isnull(dteguiadesp.deleted_at)
        INNER JOIN despachoord
        ON dteguiadesp.despachoord_id = despachoord.id AND ISNULL(despachoord.deleted_at)
        INNER JOIN notaventa
        ON despachoord.notaventa_id = notaventa.id AND ISNULL(dte.deleted_at) and isnull(notaventa.deleted_at)
        INNER JOIN tipoentrega
        ON dteguiadesp.tipoentrega_id  = tipoentrega.id AND ISNULL(tipoentrega.deleted_at)
        INNER JOIN cliente
        ON dte.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
        inner join comuna
        ON cliente.comunap_id  = comuna.id AND ISNULL(comuna.deleted_at)
        LEFT JOIN clientebloqueado
        ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        LEFT JOIN dtedev
        ON dte.id = dtedev.dte_id AND ISNULL(dtedev.deleted_at)
        LEFT JOIN usuario
        ON dtedev.usuario_id = usuario.id AND ISNULL(usuario.deleted_at)
        INNER JOIN despachosol
        ON despachoord.despachosol_id = despachosol.id AND ISNULL(despachosol.deleted_at)
        LEFT JOIN vista_datacobranza
        ON vista_datacobranza.cliente_id = notaventa.cliente_id
        LEFT JOIN clientedesbloqueado
        ON clientedesbloqueado.cliente_id = notaventa.cliente_id and clientedesbloqueado.notaventa_id = notaventa.id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
        LEFT JOIN clientedesbloqueadomodulo
        ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 10
        LEFT JOIN modulo
        ON modulo.id = clientedesbloqueadomodulo.modulo_id
        LEFT JOIN clientedesbloqueadopro
        ON clientedesbloqueadopro.cliente_id = dte.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
        WHERE despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
        AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
        AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
        AND despachosol.sucursal_id in ($sucurcadena)
        AND ISNULL(dte.statusgen)
        AND dte.foliocontrol_id=2
        GROUP BY dte.id
        ORDER BY dte.id desc;";

    return DB::select($sql);

}

function consultalistarorddesppage($request){
    $user = Usuario::findOrFail(auth()->id());
    if(empty($request->vendedor_id)){
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
        $vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
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
        $aux_condtipoentrega_id = "dteguiadesp.tipoentrega_id='$request->tipoentrega_id'";
    }
    if(empty($request->notaventa_id)){
        $aux_condnotaventa_id = " true";
    }else{
        $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
    }

    if(empty($request->aprobstatus)){
        $aux_aprobstatus = " true";
    }else{
        switch ($request->aprobstatus) {
            case 1:
                $aux_aprobstatus = "notaventa.aprobstatus='0'";
                break;
            case 2:
                $aux_aprobstatus = "notaventa.aprobstatus='$request->aprobstatus'";
                break;    
            case 3:
                $aux_aprobstatus = "(notaventa.aprobstatus='1' or notaventa.aprobstatus='3')";
                break;
            case 4:
                $aux_aprobstatus = "notaventa.aprobstatus='$request->aprobstatus'";
                break;
        }
        
    }

    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
    }


    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql = "SELECT despachoord.id,notaventa.cotizacion_id,despachoord.despachosol_id,notaventa.cliente_id,
    despachoord.fechahora,despachoord.fechaestdesp,
    cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
    '' as notaventaxk,comuna.nombre as comuna_nombre,
    tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
    SUM(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
    sum(round((despachoorddet.cantdesp * notaventadetalle.preciounit) * ((notaventa.piva+100)/100))) as subtotal,
    despachoord.updated_at,'' as rutacrear,
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
    bloquearhacerguia,
    cliente.limitecredito,
    IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
    IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
    IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
    IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
    if(cliente.plazopago_id = 1,'Bloqueado: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
    modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
    IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs
    FROM despachoord INNER JOIN notaventa
    ON despachoord.notaventa_id = notaventa.id AND ISNULL(despachoord.deleted_at) and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
    INNER JOIN despachoorddet
    ON despachoorddet.despachoord_id = despachoord.id AND ISNULL(despachoorddet.deleted_at)
    INNER JOIN notaventadetalle
    ON notaventadetalle.id = despachoorddet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
    INNER JOIN tipoentrega
    ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
    LEFT JOIN clientebloqueado
    ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    INNER JOIN despachosol
    ON despachoord.despachosol_id = despachosol.id AND ISNULL(despachosol.deleted_at)
    LEFT JOIN vista_datacobranza
    ON vista_datacobranza.cliente_id = notaventa.cliente_id
    LEFT JOIN clientedesbloqueado
    ON clientedesbloqueado.cliente_id = notaventa.cliente_id and clientedesbloqueado.notaventa_id = notaventa.id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
    LEFT JOIN clientedesbloqueadomodulo
    ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 9
    LEFT JOIN modulo
    ON modulo.id = clientedesbloqueadomodulo.modulo_id
    LEFT JOIN clientedesbloqueadopro
    ON clientedesbloqueadopro.cliente_id = notaventa.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
    WHERE $vendedorcond
    and $aux_condFecha
    and $aux_condrut
    and $aux_condoc_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condtipoentrega_id
    and $aux_condnotaventa_id
    and $aux_aprobstatus
    and $aux_condcomuna_id
    and despachoord.aprguiadesp='1' and isnull(despachoord.guiadespacho)
    AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
    AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
    AND despachosol.sucursal_id in ($sucurcadena)
    AND despachoord.id NOT IN (SELECT guiadesp.despachoord_id FROM guiadesp WHERE ISNULL(guiadesp.deleted_at) AND guiadesp.id not in (SELECT guiadespanul.guiadesp_id FROM guiadespanul WHERE ISNULL(guiadespanul.deleted_at)))
    AND despachoord.id NOT IN (SELECT dteguiadesp.despachoord_id
        FROM dte INNER JOIN dteguiadesp
        ON dte.id=dteguiadesp.dte_id AND ISNULL(dte.deleted_at) AND ISNULL(dteguiadesp.deleted_at)
        WHERE dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
        AND NOT ISNULL(dteguiadesp.despachoord_id))
    GROUP BY despachoorddet.despachoord_id
    ORDER BY despachoorddet.despachoord_id DESC";
    $arrays = DB::select($sql);
    $i = 0;
    foreach ($arrays as $array) {
        $arrays[$i]->rutacrear = route('crear_dteguiadesp', ['id' => $array->id,'updated_at' => $array->updated_at]);
        $i++;
    }
    return $arrays;
}


//function updatenumguia($despachoord,$dte,$foliocontrol,$request){
function updatenumguia($despachoord,$dte,$request){
    /*
    $foliocontrol->ultfoliouti = $dte->nrodocto;
    $foliocontrol->bloqueo = 0;
    */
    $dte->statusgen = 1;
    $dte->aprobstatus = 1;
    $dte->aprobusu_id = auth()->id();
    $dte->aprobfechahora = date("Y-m-d H:i:s");
    //$despachoord->guiadespacho = $dte->nrodocto;
    //$despachoord->guiadespachofec = date("Y-m-d H:i:s");
    //if ($despachoord->save() and $dte->save() and $foliocontrol->save()) {
    if ($despachoord->save() and $dte->save()) {
            //dteguiadesp($dte->id);
        //Event(new GuardarGuiaDespacho($despachoord));
        return response()->json([
                                'mensaje' => 'ok',
                                'despachoord' => $despachoord,
                                'guiadespachofec' => date("Y-m-d", strtotime($despachoord->guiadespachofec)),
                                'status' => '0',
                                'id' => $request->id,
                                'nfila' => $request->nfila,
                                'nrodocto' => $dte->nrodocto
                                ]);
    } else {
        return response()->json(['mensaje' => 'ng']);
    }
}

function dteguiadesp($id,$Folio,$tipoArch){
    $Folio = str_pad($Folio, 10, "0", STR_PAD_LEFT);
    $dte = Dte::findOrFail($id);
    $rutrecep = $dte->cliente->rut;
    $rutrecep = number_format( substr ( $rutrecep, 0 , -1 ) , 0, "", "") . '-' . substr ( $rutrecep, strlen($rutrecep) -1 , 1 );

    $empresa = Empresa::findOrFail(1);
    $RznSoc = strtoupper(sanear_string(substr(trim($empresa->razonsocial),0,100)));
    $GiroEmis = strtoupper(sanear_string(substr(trim($empresa->giro),0,80)));
    $Acteco = substr(trim($empresa->acteco),0,6);
    $DirOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->direccion),0,60)));
    $CmnaOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->comuna->nombre),0,20)));
    $CiudadOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->comuna->provincia->nombre),0,20)));
    $contacto = strtoupper(sanear_string(substr(trim($dte->dteguiadesp->notaventa->contacto . " Telf:" . $dte->dteguiadesp->notaventa->contactotelf),0,80)));
    $CorreoRecep = strtoupper(substr(trim($dte->cliente->contactoemail),0,80));
    $RznSocRecep = strtoupper(sanear_string(substr(trim($dte->cliente->razonsocial),0,100)));
    $GiroRecep = strtoupper(sanear_string(substr(trim($dte->cliente->giro),0,42)));
    $DirRecep = strtoupper(sanear_string(substr(trim($dte->cliente->direccion),0,70)));
    $CmnaRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->nombre),0,20)));
    $CiudadRecep = strtoupper(sanear_string(substr(trim($dte->cliente->provincia->nombre),0,20)));

    //$FolioRef = substr(trim($dte->oc_id),0,20);
    $FolioRef = $dte->dteguiadesp->notaventa->oc_id;

    $contenido = "";

    if($tipoArch == "TXT"){
        $fchemisDMY = date("d-m-Y_His",strtotime($dte->fchemis));
        $fchemis = date("d-m-Y",strtotime($dte->fchemis)); // date("Y-m-d");
        $contenido = "ENC|52||$Folio|$fchemis||$dte->tipodespacho|$dte->indtraslado|||||||||||$fchemis|" . 
        "$empresa->rut|$RznSoc|$GiroEmis|||$DirOrigen|$CmnaOrigen|$CiudadOrigen|||$rutrecep||" . 
        "$RznSocRecep|$GiroRecep|$contacto|$CorreoRecep|$DirRecep|$CmnaRecep|$CiudadRecep||||||||||" .
        "$dte->mntneto|||$dte->tasaiva|$dte->iva||||||$dte->mnttotal||$dte->mnttotal|\r\n" . 
        "ACT|$Acteco|\r\n";
        foreach ($dte->dtedets as $dtedet) {
            $contenido .= "DET|$dtedet->nrolindet||$dtedet->nmbitem|$dtedet->dscitem||||$dtedet->qtyitem|||$dtedet->unmditem|$dtedet->prcitem|||||||||$dtedet->montoitem|\r\n" . 
                        "ITEM|INTERNO|$dtedet->vlrcodigo|\r\n";
        }
        $TpoDocRef = (empty($dte->dteguiadesp->despachoord_id) ? "" : "OD:" . $dte->dteguiadesp->despachoord_id . " ") . (empty($dte->dteguiadesp->ot) ? "" : "OT:" . $dte->ot . " ")  . (empty($dte->obs) ? "" : $dte->obs . " ") . (empty($dte->lugarentrega) ? "" : $dte->lugarentrega . " ")  . (empty($dte->comunaentrega_id) ? "" : $dte->comunaentrega->nombre . " ");
        $TpoDocRef = substr(trim($TpoDocRef),0,90);
        $contenido .= "REF|1|801||$dte->oc_id||$fchemis||$TpoDocRef|";
    
    }
    

    if($tipoArch == "XML"){
        $FchEmis = $dte->fchemis;
        //$FchEmis = date("d-m-Y",strtotime($dte->fchemis));
        /*$contenido = "<![CDATA[<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .*/
        //"<DTE version=\"1.0\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns=\"http://www.sii.cl/SiiDte\">" .
        /*$contenido = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .
        "<DTE version=\"1.0\">" .
        */
        $contenido = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .
        "<DTE version=\"1.0\">" .
        "<Documento ID=\"R" .$empresa->rut . "T52F" . $Folio . "\">" .
        "<Encabezado>" .
        "<IdDoc>" .
        "<TipoDTE>52</TipoDTE>" .
        "<Folio>$Folio</Folio>" .
        "<FchEmis>$FchEmis</FchEmis>" .
        "<TipoDespacho>$dte->tipodespacho</TipoDespacho>" .
        "<IndTraslado>$dte->indtraslado</IndTraslado>" .
        "<TpoImpresion>N</TpoImpresion>" .
        "</IdDoc>" .
        "<Emisor>" .
        "<RUTEmisor>$empresa->rut</RUTEmisor>" .
        "<RznSoc>$RznSoc</RznSoc>" .
        "<GiroEmis>$GiroEmis</GiroEmis>" .
        "<Acteco>$Acteco</Acteco>" .
        "<FolioAut>sdfsdf</FolioAut>" .
        "<FchAut>$FchEmis</FchAut>" .
        "<DirOrigen>$DirOrigen</DirOrigen>" .
        "<CmnaOrigen>$CmnaOrigen</CmnaOrigen>" .
        "<CiudadOrigen>$CiudadOrigen</CiudadOrigen>" .
        "</Emisor>" .
        "<Receptor>" .
        "<RUTRecep>$rutrecep</RUTRecep>" .
        "<RznSocRecep>$RznSocRecep</RznSocRecep>" .
        "<GiroRecep>$GiroRecep</GiroRecep>" .
        "<Contacto>$contacto</Contacto>" .
        "<CorreoRecep>$CorreoRecep</CorreoRecep>" .
        "<DirRecep>$DirRecep</DirRecep>" .
        "<CmnaRecep>$CmnaRecep</CmnaRecep>" .
        "<CiudadRecep>$CiudadRecep</CiudadRecep>" .
        "</Receptor>" .
        "<Totales>" .
        "<MntNeto>$dte->mntneto</MntNeto>" .
        "<TasaIVA>$dte->tasaiva</TasaIVA>" .
        "<IVA>$dte->iva</IVA>" .
        "<MntTotal>$dte->mnttotal</MntTotal>" .
        "</Totales>" .
        "</Encabezado>";
    
        $aux_totalqtyitem = 0;
    
        foreach ($dte->dtedets as $dtedet) {
            $VlrCodigo = substr(trim($dtedet->vlrcodigo),0,35);
            $NmbItem = strtoupper(sanear_string(substr(trim($dtedet->nmbitem),0,80)));
            $DscItem = strtoupper(sanear_string(trim($dtedet->dscitem)));
            $UnmdItem = substr(trim($dtedet->unmditem),0,4);
            $contenido .= "<Detalle>" .
            "<NroLinDet>$dtedet->nrolindet</NroLinDet>" .
            "<CdgItem>" .
            "<TpoCodigo>INTERNO</TpoCodigo>" .
            "<VlrCodigo>" . $VlrCodigo . "</VlrCodigo>" .
            "</CdgItem>" .
            "<NmbItem>" . $VlrCodigo . "</NmbItem>" .
            "<DscItem>" . $NmbItem . "</DscItem>" .
            "<QtyItem>" . $dtedet->qtyitem . "</QtyItem>" .
            "<UnmdItem>" . $UnmdItem . "</UnmdItem>" .
            "<PrcItem>$dtedet->prcitem</PrcItem>" .
            "<MontoItem>$dtedet->montoitem</MontoItem>" .
            "</Detalle>";
            $aux_totalqtyitem += $dtedet->qtyitem;
        }
    
        $TpoDocRef = (empty($dte->dteguiadesp->despachoord_id) ? "" : "OD:" . $dte->dteguiadesp->despachoord_id . " ") . (empty($dte->dteguiadesp->ot) ? "" : "OT:" . $dte->dteguiadesp->ot . " ")  . (empty($dte->obs) ? "" : $dte->obs . " ") . (empty($dte->lugarentrega) ? "" : $dte->dteguiadesp->lugarentrega . " ")  . (empty($dte->dteguiadesp->comunaentrega_id) ? "" : $dte->dteguiadesp->comunaentrega->nombre . " ");
        $TpoDocRef = strtoupper(sanear_string(substr(trim($TpoDocRef),0,90)));
    
        $contenido .= "<Referencia>" .
        "<NroLinRef>1</NroLinRef>" .
        "<TpoDocRef>TPR</TpoDocRef>" .
        "<FolioRef>00001000</FolioRef>" .
        "<FchRef>$FchEmis</FchRef>" .
        "<RazonRef>TOTAL UNIDADES:$aux_totalqtyitem</RazonRef>" .
        "</Referencia>" .
        "<Referencia>" .
        "<NroLinRef>2</NroLinRef>" .
        "<TpoDocRef>SRD</TpoDocRef>" .
        "<FolioRef>00001000</FolioRef>" .
        "<FchRef>$FchEmis</FchRef>" .
        "<RazonRef>DESPACHO: $DirRecep</RazonRef>" .
        "</Referencia>";
        if($FolioRef){
            $contenido .= "<Referencia>" .
            "<NroLinRef>3</NroLinRef>" .
            "<TpoDocRef>801</TpoDocRef>" .
            "<FolioRef>$FolioRef</FolioRef>" .
            "<FchRef>$FchEmis</FchRef>" .
            "<RazonRef>$TpoDocRef</RazonRef>" .
            "</Referencia>";
    
        }
        $contenido .= "</Documento>" .
        "</DTE>";

        //"</DTE>]]>";

        //dd($contenido);
    
    }

    return $contenido;


    //Storage::disk('public')->put('/facturacion/dte/procesados/ge' . $dte->nrodocto . '_' . $fchemisDMY . '.txt', $contenido);

}

function validarSiExisteBodega($despachoord,$invmoduloBod){
    //ANTES DE PROCESAR LA ORDEN VALIDO QUE LOS PRODUCTOS INVOLUCRADOS TENGAN BODEGA DE DESPACHO CORRESPONDIENTE A LA SUCURSAL DE CADA PRODUCTO
    //ESTO DEBE IR EN EL PROYECTO FINAL
    foreach ($despachoord->despachoorddets as $despachoorddet) {
        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
            $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
            $aux_bodegadespacho_id = 0;
            foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                //BUSCAR BODEGA DESPACHO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                    $aux_bodegadespacho_id = $invmovmodulobodent->id;
                }
            }
            if($aux_bodegadespacho_id == 0){
                return response()->json([
                    'mensaje' => 'No existe Bodega Despacho en Sucursal: ' . $despachoord->notaventa->sucursal->nombre
                ]);
            }
        }
    }
    //ANTES DE PROCESAR LA ORDEN VALIDO QUE LOS PRODUCTOS INVOLUCRADOS TENGAN BODEGA DE DESPACHO CORRESPONDIENTE A LA SUCURSAL DE CADA PRODUCTO
    //ESTO DEBE IR EN EL PROYECTO FINAL
}

function guardarDTE($request,$aux_indtraslado,$cont_producto){
    //dd($request);
    $despachoord = DespachoOrd::findOrFail($request->despachoord_id);
    $empresa = Empresa::findOrFail(1);
    $hoy = date("Y-m-d H:i:s");
    $request->request->add(['fechahora' => $hoy]);
    $date = str_replace('/', '-', $request->fchemis);
    $request->request->add(['fchemis' => date('Y-m-d', strtotime($date))]);

    $mntneto = 0;
    $kgtotal = 0;
    foreach($despachoord->despachoorddets as $despachoorddet){
        $NVDet = $despachoorddet->notaventadetalle;
        $mntneto += (($NVDet->subtotal/$NVDet->cant) * $despachoorddet->cantdesp);
        $kgtotal += (($NVDet->totalkilos/$NVDet->cant) * $despachoorddet->cantdesp);
    }
    $request->request->add(['mntneto' => $mntneto]);
    $request->request->add(['tasaiva' => $empresa->iva]);
    $request->request->add(['iva' => round($empresa->iva * $mntneto/100)]);
    $request->request->add(['mnttotal' => round($mntneto + $empresa->iva)]);
    $request->request->add(['kgtotal' => $kgtotal]);
    $request->request->add(['sucursal_id' => $despachoord->notaventa->sucursal_id]);
    $request->request->add(['cliente_id' => $despachoord->notaventa->cliente_id]);
    $request->request->add(['comuna_id' => $despachoord->notaventa->comuna_id]);
    $request->request->add(['vendedor_id' => $despachoord->notaventa->vendedor_id]);
    $request->request->add(['foliocontrol_id' => 2]);

    $dte = new Dte();

    $Tmntneto = 0;
    $Tiva = 0;
    $Tmnttotal = 0;
    $Tkgtotal = 0;

    if(!empty($request->despachoord_id)){
        if($cont_producto>0){
            for ($i=0; $i < $cont_producto ; $i++){
                if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                    $producto = Producto::findOrFail($request->producto_id[$i]);
                    $dtedet = new DteDet();
                    $dtedet->dte_id = null;
                    $dtedet->producto_id = $request->producto_id[$i];
                    $dtedet->nrolindet = $request->nrolindet[$i];
                    $dtedet->vlrcodigo = $request->producto_id[$i];
                    $dtedet->nmbitem = $request->nmbitem[$i];
                    $dtedet->dscitem = $request->dscitem[$i];
                    $dtedet->qtyitem = $request->qtyitem[$i];
                    $dtedet->unmditem = $request->unmditem[$i];
                    $dtedet->unidadmedida_id = $request->unidadmedida_id[$i];
                    if($request->moneda_id == 1){
                        $dtedet->prcitem = $request->montoitem[$i]/$request->qtyitem[$i]; //$request->prcitem[$i];
                        $dtedet->montoitem = round($request->montoitem[$i],0);
                    }else{
                        $notaventadetalle = NotaVentaDetalle::findOrFail($request->notaventadetalle_id[$i]);
                        $dtedet->prcitem = round($notaventadetalle->preciounit * $request->dolarobser,4);
                        $dtedet->montoitem = round($dtedet->prcitem * $request->qtyitem[$i],0);
                    }
                    //$dtedet->montoitem = $request->montoitem[$i];
                    if($aux_indtraslado == 6){
                        $dtedet->prcitem = 1; //$request->prcitem[$i];
                        $dtedet->montoitem = round($request->qtyitem[$i],0);
                    }
                    $dtedet->obsdet = $request->obsdet[$i];
                    $dtedet->itemkg = $request->itemkg[$i];
                    
                    $Tmntneto += $dtedet->montoitem;
                    $Tkgtotal += $request->itemkg[$i];
                    $dtedet_id = $dtedet->id;
                    $dtedet->despachoorddet_id = $request->despachoorddet_id[$i];
                    $dtedet->notaventadetalle_id = $request->notaventadetalle_id[$i];

                    $dte->dtedets[] = $dtedet;
                    //$dtedet->save();
                    /*
                    $dtedet_id = $dtedet->id;
                    $dtedet_despachoorddet = new DteDet_DespachoOrdDet();
                    $dtedet_despachoorddet->dtedet_id = $dtedet_id;
                    $dtedet_despachoorddet->despachoorddet_id = $request->despachoorddet_id[$i];
                    $dtedet_despachoorddet->notaventadetalle_id = $request->notaventadetalle_id[$i];
                    $dtedet_despachoorddet->save();
                    */
                }
            }
        }
    }
    if($Tmntneto>0){
        $Tiva = round(($empresa->iva/100) * $Tmntneto);
        $Tmnttotal = round((($empresa->iva/100) + 1) * $Tmntneto);    
    }

    $dte->foliocontrol_id = 2;
    $dte->nrodocto = "";
    $dte->fchemis = $request->fchemis;
    $dte->fchemisgen = $hoy;
    $dte->fechahora = $hoy;
    $dte->sucursal_id = $despachoord->notaventa->sucursal_id;
    $dte->cliente_id = $despachoord->notaventa->cliente_id;
    $dte->comuna_id = $despachoord->notaventa->comuna_id;
    $dte->ciudad_id = $despachoord->notaventa->cliente->ciudad_id;
    $dte->vendedor_id = $despachoord->notaventa->vendedor_id;
    $dte->obs = $request->obs;
    if(isset($despachoord->despachosol->despachosoldte)){
        $dte->obs = "Guia: " . $despachoord->despachosol->despachosoldte->dte->nrodocto . " " . $request->obs;
    }
    $dte->tipodespacho = $request->tipodespacho;
    $dte->indtraslado = $aux_indtraslado;
    $dte->mntneto = $Tmntneto;
    $dte->tasaiva = $empresa->iva;
    $dte->iva = $Tiva;
    $dte->mnttotal = $Tmnttotal;
    $dte->kgtotal = $Tkgtotal;
    $dte->centroeconomico_id = $request->centroeconomico_id;
    //$dte->statusgen = 
    //$dte->aprobstatus = 
    //$dte->aprobusu_id = 
    //$dte->aprobfechahora = 
    $dte->usuario_id = $request->usuario_id;
    //$dte->usuariodel_id = 

    //dd($dte->foliocontrol->tipodocto);
    $dteguiadesp = new DteGuiaDesp();
    $dteguiadesp->despachoord_id = $despachoord->id;
    $dteguiadesp->notaventa_id = $despachoord->notaventa_id;
    $dteguiadesp->tipoentrega_id = $request->tipoentrega_id;
    $dteguiadesp->comunaentrega_id = $request->comunaentrega_id;
    $dteguiadesp->lugarentrega = $request->lugarentrega;
    $dteguiadesp->ot = $request->ot;
    /*
    if($dte->cliente_id == 1659){
        $respuesta = response()->json([
            'id' => 1
        ]);    
    }else{
        $respuesta = Dte::generardteprueba($dte);
    }
    */
    $dte->dteguiadesp = $dteguiadesp;
    $respuesta = Dte::dteSolicitarFolio($dte);
    //$respuesta = Dte::generardteprueba($dte);
    /*
    $respuesta = response()->json([
        'id' => 1
    ]);
    */
    $foliocontrol = Foliocontrol::findOrFail($dte->foliocontrol_id);
    if($respuesta["id"] == 1){
        $dte->fchemisgen = date("Y-m-d H:i:s");
        $dte->nrodocto = $respuesta["aux_folio"];
        $dte->stasubcob = 1;
        $dteNew = Dte::create($dte->toArray());
        foreach ($dte->dtedets as $dtedet) {
            $dtedet->dte_id = $dteNew->id;
            $despachoorddet_id = $dtedet->despachoorddet_id;
            $notaventadetalle_id = $dtedet->notaventadetalle_id;
            $aux_dtedet = $dtedet->toArray();
            unset($aux_dtedet["despachoorddet_id"]); //ELIMINO PARA EVITAR EL ERROR AL INTERTAR A DteDet
            unset($aux_dtedet["notaventadetalle_id"]); //ELIMINO PARA EVITAR EL ERROR AL INTERTAR A DteDet

            $dtedetNew = DteDet::create($aux_dtedet);
            $dtedet_id = $dtedetNew->id;
            $dtedet_despachoorddet = new DteDet_DespachoOrdDet();
            $dtedet_despachoorddet->dtedet_id = $dtedet_id;
            $dtedet_despachoorddet->despachoorddet_id = $despachoorddet_id;
            $dtedet_despachoorddet->notaventadetalle_id = $notaventadetalle_id;
            $dtedet_despachoorddet->save();
        }
        $dteguiadesp->dte_id = $dteNew->id;
        $dteguiadesp->save();
        //GUARDO EN DespachoOrd EL nrodocto DE GUI DE DESPACHO SII
        $despachoord = DespachoOrd::findOrFail($dteguiadesp->despachoord_id);
        $despachoord->guiadespacho = $dteNew->nrodocto;
        $despachoord->guiadespachofec = date("Y-m-d H:i:s");
        //CREAR REGISTRO DE ORDEN DE COMPRA
        $dteoc = new DteOC();
        $dteoc->dte_id = $dteNew->id;
        $dteoc->oc_id = $despachoord->notaventa->oc_id;
        $dteoc->oc_folder = "notaventa";
        $dteoc->oc_file = $despachoord->notaventa->oc_file;
        $dteoc->save();

        if(isset($despachoord->despachosol->despachosoldte)){
            $dtedte = new DteDte();
            $dtedte->dte_id = $dteNew->id;
            $dtedte->dter_id = $despachoord->despachosol->despachosoldte->dte_id;
            //$dtedte->dtefac_id = "";
            $dtedte->save();
        }
        if ($despachoord->save()) {
            Event(new GuardarGuiaDespacho($despachoord,$dteNew));
        }

        $foliocontrol->bloqueo = 0;
        $foliocontrol->ultfoliouti = $dteNew->nrodocto;
        $foliocontrol->save();

        $dte = Dte::findOrFail($dteNew->id);
        $respuesta = Dte::subirDteSii($dte);
        if($respuesta["id"] == 1){
            Dte::guardarPdfXmlSii($dte->nrodocto,$foliocontrol,$respuesta["Carga_TXTDTE"]);
        }


    }else{
        /* $foliocontrol->bloqueo = 0;
        $foliocontrol->save(); */
        //dd($respuesta["id"]);
        
        return redirect('dteguiadesp/listarorddesp')->with([
            'mensaje'=>$respuesta["mensaje"] ,
            'tipo_alert' => 'alert-error'
        ]);
       
    }
    return $respuesta;
}