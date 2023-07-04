<?php

namespace App\Http\Controllers;

use App\Events\GuardarGuiaDespacho;
use App\Http\Requests\ValidarDTE;
use App\Models\AreaProduccion;
use App\Models\CentroEconomico;
use App\Models\Cliente;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoOrd;
use App\Models\Dte;
use App\Models\DteAnul;
use App\Models\DteDespachoOrd;
use App\Models\DteDet;
use App\Models\DteDet_DespachoOrdDet;
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
        return view('dteguiadesp.index');
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
        $centroeconomicos = CentroEconomico::orderBy('id')->get();
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
        //dd($request);
        $despachoord = DespachoOrd::findOrFail($request->despachoord_id);
        if($request->updated_at != $despachoord->updated_at){
            return redirect('dteguiadesp/listarorddesp')->with([
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
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
        if(count($despachoord->despachoorddets) != $cont_producto){
            return redirect('dteguiadesp/listarorddesp')->with([
                'mensaje'=>'Cantidad de item diferentes a la Orden de Despacho Original.',
                'tipo_alert' => 'alert-error'
            ]);
        }

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
                        $dtedet->prcitem = $request->montoitem[$i]/$request->qtyitem[$i]; //$request->prcitem[$i];
                        $dtedet->montoitem = $request->montoitem[$i];
                        $dtedet->obsdet = $request->obsdet[$i];
                        $dtedet->itemkg = $request->itemkg[$i];
                        
                        $Tmntneto += $request->montoitem[$i];
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
        $dte->vendedor_id = $despachoord->notaventa->vendedor_id;
        $dte->obs = $request->obs;
        $dte->tipodespacho = $request->tipodespacho;
        $dte->indtraslado =  $request->indtraslado;
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

        $dte->dteguiadesp = $dteguiadesp;
        $respuesta = Dte::generardteprueba($dte);
        /*
        $respuesta = response()->json([
            'id' => 1
        ]);
        */
        $foliocontrol = Foliocontrol::findOrFail($dte->foliocontrol_id);
        if($respuesta->original["id"] == 1){
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
            if ($despachoord->save()) {
                Event(new GuardarGuiaDespacho($despachoord));
            }
            //CREAR REGISTRO DE ORDEN DE COMPRA
            $dteoc = new DteOC();
            $dteoc->dte_id = $dteNew->id;
            $dteoc->oc_id = $despachoord->notaventa->oc_id;
            $dteoc->oc_folder = "notaventa";
            $dteoc->oc_file = $despachoord->notaventa->oc_file;
            $dteoc->save();

            $foliocontrol->bloqueo = 0;
            $foliocontrol->ultfoliouti = $dteNew->nrodocto;
            $foliocontrol->save();
            return redirect('dteguiadesp')->with([
                'mensaje'=>'Guia Despacho creada con exito.',
                'tipo_alert' => 'alert-success'
            ]);    
        }else{
            $foliocontrol->bloqueo = 0;
            $foliocontrol->save();
            return redirect('dteguiadesp/listarorddesp')->with([
                'mensaje'=>$respuesta->original["mensaje"] ,
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
                        $dtedet->montoitem = $request->montoitem[$i];
                        $dtedet->obsdet = $request->obsdet[$i];
                        $dtedet->itemkg = $request->itemkg[$i];

                        $Tmntneto += $request->montoitem[$i];
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
            //VALIDAR QUE DOCUMENTO FUE ACEPTAFO POR SII
            //SI NO EXISTE DOCUMENTO EN SII
            /*
            //EN COMENTARIO: A PETICION DE ERIKA BUSTOS PARA EVITAR RETRASOS EN ENVIO DE GUIAS DE DESPACHO
            if($Estado_DTE->Estatus == 3){
                return response()->json([
                    'id' => 0,
                    'mensaje' => $Estado_DTE->MsgEstatus . " Nro: " . $dte->nrodocto,
                    'tipo_alert' => 'error'
                ]);
            }
            //SI NO FUE ACEPTADO POR SII
            if($Estado_DTE->EstadoDTE != 16){
                return response()->json([
                    'id' => 0,
                    'mensaje' => $Estado_DTE->DescEstado . " Nro: " . $dte->nrodocto,
                    'tipo_alert' => 'error'
                ]);
            }
            //FIN EN COMENTARIO: A PETICION DE ERIKA BUSTOS PARA EVITAR RETRASOS EN ENVIO DE GUIAS DE DESPACHO
            */

            /*
            if(!is_null($dte->statusgen)){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Guia de Despacho ya fue Generada! Nro: ' . $dte->nrodocto,
                    'tipo_alert' => 'error'
                ]);
            }
            $foliocontrol = Foliocontrol::where("doc","=","GDVE")->get();
            if(count($foliocontrol) == 0){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Numero de folio no encontrado.',
                    'tipo_alert' => 'error'
                ]);
            }
            if($foliocontrol[0]->ultfoliouti >= $foliocontrol[0]->ultfoliohab ){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Se agotaron los folios. Se deben de pedir nuevos folios',
                    'tipo_alert' => 'error'
                ]);
            }
            $foliocontrol = Foliocontrol::findOrFail($foliocontrol[0]->id);
            if($foliocontrol->bloqueo == 1){
                $aux_guidesp = Dte::whereNotNull("nrodocto")
                            ->whereNull("statusgen")
                            ->whereNull("deleted_at")
                            ->get();
                //dd($aux_guidesp);
                if(count($aux_guidesp) == 0){
                    return response()->json([
                        'id' => 0,
                        'mensaje'=>'Folio bloqueado, vuelva a intentar. Folio: ' . $foliocontrol->ultfoliouti,
                        'tipo_alert' => 'error'
                    ]);
                }else{
                    if(is_null($dte->nrodocto)){
                        return response()->json([
                            'id' => 0,
                            'mensaje' => 'Existe una Guia de Despacho pendiente por Generar: ' . $aux_guidesp[0]->nrodocto,
                            'tipo_alert' => 'error'
                        ]);        
                    }
                }
            }else{
                //Si $foliocontrol->bloqueo = 0;
                //Bloqueo el registro para que no pueda ser modificado por otro usuario
                //Al procesar el registro desbloqueo 
                $foliocontrol->bloqueo = 1;
                $foliocontrol->save();
            }
            */
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
        //dd($request);
        $dte = Dte::findOrFail($request->guiadesp_id);
        if($request->updated_at != $dte->updated_at){
            return redirect('dteguiadesp')->with([
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'alert-error'
            ]);
        }
        //dd($request);
        $request->request->add(['usuario_id' => auth()->id()]);
        $dteanul = DteAnul::create($request->all());
        $dte->updated_at = date("Y-m-d H:i:s");
        if($dte->save()){
            $despachoord = DespachoOrd::findOrFail($dte->dteguiadesp->despachoord_id);
            $despachoord->updated_at = date("Y-m-d H:i:s");
            if($despachoord->save()){
                return response()->json(['mensaje' => 'ok']);
            }
        }
        //SI NO EJECUTA EL RETURN ANTERIOR, EJECUTA ESTE RETURN
        return redirect('dteguiadesp')->with([
            'mensaje'=>'No se actualizaron los datos, ocurrio un error al intentar guardar!',
            'tipo_alert' => 'alert-error'
        ]);
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
        /*
        $respuesta = response()->json([
            'id' => 1
        ]);
        */
        $foliocontrol = Foliocontrol::findOrFail($dte->foliocontrol_id);
        $foliocontrol->bloqueo = 0;
        $foliocontrol->save();
        if($respuesta->original["id"] == 1){
            if(empty($dteini->nrodocto)){
                $dteini->nrodocto = $dte->nrodocto;
                if(!$dteini->save()){
                    return response()->json([
                        'id' => 0,
                        'titulo' => "",
                        'mensaje'=> "Error al Guardar en dte",
                        'tipo_alert' => 'error'
                    ]);                            
                }
            }
            return response()->json([
                'id' => 1,
                'mensaje'=>'DTE Generado con exito: ' . $dte->nrodocto,
                'tipo_alert' => 'success'
            ]);
        }else{
            return response()->json([
                'id' => 0,
                'titulo' => $respuesta->original["titulo"],
                'mensaje'=> $respuesta->original["mensaje"],
                'tipo_alert' => 'error'
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
            $despachoord->save(); //SALVO PARA QUE SE ACTUALICE LA FECHA UPDATE_AT
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
        despachoord.notaventa_id,
        despachoord.despachosol_id,dteguiadesp.despachoord_id,despachoord.fechaestdesp,comuna.nombre as cmnarecep,dte.kgtotal,
        dteguiadesp.tipoentrega_id,tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,
        clientebloqueado.descripcion as clientebloqueado_descripcion,dte.updated_at,despachoord.updated_at as despordupdated_at,
        dtedev.obs as dtedev_obs,usuario.nombre as dtedevusuario_nombre
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
        WHERE despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
        AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
        AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
        AND notaventa.sucursal_id in ($sucurcadena)
        AND ISNULL(dte.statusgen)
        AND dte.foliocontrol_id=2
        ORDER BY dte.id desc;";

    return DB::select($sql);

}

function consultalistarorddesppage($request){

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

    $sql = "SELECT despachoord.id,notaventa.cotizacion_id,despachoord.despachosol_id,despachoord.fechahora,despachoord.fechaestdesp,
    cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
    '' as notaventaxk,comuna.nombre as comuna_nombre,
    tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
    SUM(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
    sum(round((despachoorddet.cantdesp * notaventadetalle.preciounit) * ((notaventa.piva+100)/100))) as subtotal,
    despachoord.updated_at,'' as rutacrear
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
    AND notaventa.sucursal_id in ($sucurcadena)
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