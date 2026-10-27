<?php

namespace App\Http\Controllers;

use App\Models\CentroEconomico;
use App\Models\Cliente;
use App\Models\ClienteDesBloqueado;
use App\Models\Comuna;
use App\Models\DataCobranza;
use App\Models\DespachoOrd;
use App\Models\Dte;
use App\Models\DteDet;
use App\Models\DteDet_DespachoOrdDet;
use App\Models\DteDte;
use App\Models\DteFac;
use App\Models\DteGuiaUsada;
use App\Models\DteOC;
use App\Models\Empresa;
use App\Models\Foliocontrol;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoTraslado;
use App\Models\UnidadMedida;
use App\Models\Vendedor;
use App\Services\DocInsumoDetService;
use App\Services\DteTransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DteFacturaDirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-factura-directa');
        return view('dtefacturadir.index');
    }

    public function dtefacturadirpage($dte_id = ""){
        $datas = consultaindex($dte_id);
        return datatables($datas)->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-dte-factura-directa');
        $vendedor = Vendedor::vendedores("A");
        $tablas['vendedores'] = $vendedor['vendedores'];
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedidas'] = UnidadMedida::orderBy('id')->get();
        $tablas['foliocontrol'] = Foliocontrol::orderBy('id')->get();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablas['centroeconomicos'] = CentroEconomico::orderBy('id')->get();
        $centroeconomicos = CentroEconomico::orderBy('id')->get();
        $tablas['comunas'] = Comuna::orderBy('id')->get();
        $tablas['tipotraslado'] = TipoTraslado::orderBy('id')
                                ->where('activo', '=', 1)
                                ->get();

        //dd($tablas);
        return view('dtefacturadir.crear',compact('tablas','centroeconomicos'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        can('guardar-dte-factura-directa');
        //dd($request);
        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            return redirect('dtefacturadir')->with([
                'mensaje'=>'No hay items, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $foliocontrol = Foliocontrol::findOrFail(1);
        if($cont_producto > $foliocontrol->maxitemxdoc ){
            return redirect('dtefacturadir')->with([
                'mensaje' => 'Total items documento: ' . $cont_producto . '. Maximo items permitido por documento: ' . $foliocontrol->maxitemxdoc,
                'tipo_alert' => 'alert-error'
            ]);
        }

        $cliente = Cliente::findOrFail($request->cliente_id);
        foreach ($cliente->clientebloqueados as $clientebloqueado) {
            return redirect('dtefacturadir')->with([
                'id' => 0,
                'mensaje'=>'No es posible hacer Factura, Condición financiera en revisión: ' . $clientebloqueado->descripcion,
                'tipo_alert' => 'alert-error'
            ]);
        }
        $request1 = new Request();
        $request1->merge(['modulo_id' => 15]);
        $request1->request->set('modulo_id', 15);
        $request1->merge(['deldesbloqueo' => 1]);
        $request1->request->set('deldesbloqueo', 1);
        $clibloq = clienteBloqueado($request->cliente_id,0,$request1);
        if(!is_null($clibloq["bloqueo"])){
            /* $request1 = new Request();
            $request1->merge(['cliente_id' => $request->cliente_id]);
            $request1->request->set('cliente_id', $request->cliente_id);
            $respuesta = DataCobranza::llenartabla($request1); */

            return redirect('dtefacturadir')->with([
                "mensaje" => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                "tipo_alert" => "alert-error"
            ]);
        }
        
        $dte = new Dte();
        $dtefac = new DteFac();
        //$dtefac->dte_id = $dte_id;
        $dtefac->hep = $request->hep;
        $dtefac->notped = $request->notped;
        $dtefac->formapago_id = $cliente->formapago_id;
        $dtefac->fchvenc =  date('Y-m-d', strtotime(date('Y-m-d') ."+ " . $cliente->plazopago->dias . " days"));
        $dte->dtefac = $dtefac;
        //$dtefac->save();
        //CREAR REGISTRO DE ORDEN DE COMPRA
        if(!is_null($request->oc_id)){
            $dteoc = new DteOC();
            $dteoc->dte_id = "";
            $dteoc->oc_id = $request->oc_id;
            $dteoc->oc_folder = "oc";
            $dteoc->oc_file = $request->oc_file;
            $dte->dteocs[] = $dteoc;
            //$dteoc->save();
        }
    
        $Tmntneto = 0;
        $Tiva = 0;
        $Tmnttotal = 0;
        $Tkgtotal = 0;
        //$dtedtes = [];
        $dteguiausadas = [];
        $aux_nrolindet = 0;
        for ($i=0; $i < $cont_producto ; $i++){
            if(is_null($request->producto_id[$i])==false AND (is_null($request->qtyitem[$i])==false) OR $request->codref == 2){
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
                if(cadVacia($request->nmbitem[$i])){
                    mensajeRespuesta([
                        'mensaje' => "Campo nmbitem no puede quedar Vacio, Item: " . strval($i + 1),
                        'tipo_alert' => 'alert-error'
                    ]);                    
                }
                if(cadVacia($request->qtyitem[$i])){
                    mensajeRespuesta([
                        'mensaje' => "Campo qtyitem no puede quedar Vacio, Item: " . strval($i + 1),
                        'tipo_alert' => 'alert-error'
                    ]);                    
                }
                if(cadVacia($request->unmditem[$i])){
                    mensajeRespuesta([
                        'mensaje' => "Campo unmditem no puede quedar Vacio, Item: " . strval($i + 1),
                        'tipo_alert' => 'alert-error'
                    ]);                    
                }
                if(cadVacia($request->prcitem[$i])){
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
                }
                //$producto = Producto::findOrFail($request->producto_id[$i]);
                $unidadmedida = UnidadMedida::findOrFail($request->unmditem[$i]);
                $dtedet = new DteDet();
                $dtedet->dtedet_id = $request->dtedetorigen_id[$i];
                $dtedet->producto_id = $request->producto_id[$i];
                $dtedet->nrolindet = ($i + 1);
                $dtedet->vlrcodigo = $request->producto_id[$i];
                $dtedet->nmbitem = $request->nmbitem[$i];
                //$dtedet->dscitem = $request->dscitem[$i]; este valor aun no lo uso
                $dtedet->qtyitem = $request->qtyitem[$i];
                $dtedet->unmditem = substr($unidadmedida->nombre, 0, 4);
                $dtedet->unidadmedida_id = $request->unmditem[$i];
                $dtedet->prcitem = $request->prcitem[$i]; //$request->montoitem[$i]/$request->qtyitem[$i]; //$request->prcitem[$i];
                //$dtedet->montoitem = $request->montoitem[$i];
                $dtedet->montoitem = round($dtedet->qtyitem * $dtedet->prcitem,0); //$request->montoitem[$i];
                //$dtedet->obsdet = $request->obsdet[$i];
                $aux_itemkg = is_numeric($request->itemkg[$i]) ? $request->itemkg[$i] : 0;
                if($aux_itemkg == 0 and $dtedet->unidadmedida_id == 7){ //SI LA UNIDAD DE MEDIDA ES = 7 (KG) Y NO TIENE PESO, SE ASIGNA $aux_itemkg = $dtedet->qtyitem;
                    $aux_itemkg = $dtedet->qtyitem;
                }
                $dtedet->itemkg = $aux_itemkg;
                //$dtedet->save();
                $dte->dtedets[] = $dtedet;
                $dtedet_id = $dtedet->id;

                $Tmntneto += $dtedet->montoitem; //$request->montoitem[$i];
                $Tkgtotal += $aux_itemkg;
            }
        }
        if($Tmntneto <= 0){
            return redirect('dtefacturadir')->with([
                'mensaje'=> "Neto total de factura debe ser mayor a cero" ,
                'tipo_alert' => 'alert-error'
            ]);
        }

        $empresa = Empresa::findOrFail(1);
        if($request->foliocontrol_id == 1){
            $Tiva = round(($empresa->iva/100) * $Tmntneto);
            $Tmnttotal = round((($empresa->iva/100) + 1) * $Tmntneto);
            $dte->tasaiva = $empresa->iva;
            $dte->iva = $Tiva;
            $dte->mnttotal = $Tmnttotal;        
        }
        if($request->foliocontrol_id == 7){
            $dte->tasaiva = 0;
            $dte->iva = 0;
            $dte->mnttotal = $Tmntneto;
        }

        $centroeconomico = CentroEconomico::findOrFail($request->centroeconomico_id);
        $hoy = date("Y-m-d H:i:s");
        $dte->foliocontrol_id = $request->foliocontrol_id;
        $dte->nrodocto = "";
        $dte->fchemis = date('Y-m-d');
        $dte->fchemisgen = $hoy;
        $dte->fechahora = $hoy;
        $dte->sucursal_id = $centroeconomico->sucursal_id;
        $dte->cliente_id = $cliente->id;
        $dte->comuna_id = $cliente->comunap_id;
        $dte->ciudad_id = $cliente->ciudad_id;
        $dte->vendedor_id = $request->vendedor_id;
        $dte->obs = $request->obs;
        /* Res. Ex. SII N°144, resolutivo 1°: los datos de despacho y traslado solo
           corresponden cuando el documento ampara el traslado de bienes. En una
           factura de servicio o arriendo (tipooperacion = 2) no hay traslado, de
           modo que se dejan en null y el XML omite <TipoDespacho>; el Formato DTE
           2.5 lo indica expresamente en ese campo ("No se incluye si el documento
           no acompaña bienes o se trata de una Factura o Nota correspondiente a la
           prestacion de servicios").
           Cualquier valor distinto de 2 -incluso que el campo no llegue, si el JS
           no cargo- se trata como venta con traslado, que es el comportamiento que
           tenia antes y el que exige los datos. */
        $aux_contraslado = ($request->tipooperacion != 2);
        $dte->tipodespacho = $aux_contraslado ? 2 : null;
        $dte->indtraslado = $aux_contraslado ? 1 : null;
        $dte->mntneto = $Tmntneto;
        $dte->kgtotal = $Tkgtotal;
        $dte->centroeconomico_id = $request->centroeconomico_id;
        $dte->usuario_id = $request->usuario_id;

        $respuesta = Dte::dteSolicitarFolio($dte);
        //$respuesta = Dte::generardteprueba($dte);
        /* $respuesta = [
            'id' => 1,
            'aux_folio' => 723,
        ]; */
        $foliocontrol = Foliocontrol::findOrFail($dte->foliocontrol_id);
        if($respuesta["id"] == 1){
            $dispatcher = DteDet::getEventDispatcher();
            DteDet::unsetEventDispatcher();
            $dtedetCreados = [];
            DB::beginTransaction();
            try {

                $dte->fchemisgen = date("Y-m-d H:i:s");
                $dte->nrodocto = $respuesta["aux_folio"];
                $dteNew = Dte::create($dte->toArray());
                if(isset($dteoc)){
                    if ($foto = Dte::setFoto($request->oc_file,$dteNew->id,$request,"DTE",$dteoc->oc_folder)){ //2 ultimos parametros son origen de orden de compra FC Factura y la carpeta donde se guarda la OC
                        $dteoc->dte_id = $dteNew->id;
                        $dteoc->oc_file = $foto;
                        $dteoc->save();
                    }
                }
                foreach ($dte->dtedets as $dtedet) {
                    $dtedet->dte_id = $dteNew->id;
                    $despachoorddet_id = $dtedet->despachoorddet_id;
                    $notaventadetalle_id = $dtedet->notaventadetalle_id;
                    $aux_dtedet = $dtedet->toArray();
                    unset($aux_dtedet["despachoorddet_id"]); //ELIMINO PARA EVITAR EL ERROR AL INTERTAR A DteDet
                    unset($aux_dtedet["notaventadetalle_id"]); //ELIMINO PARA EVITAR EL ERROR AL INTERTAR A DteDet
                    $dtedetNew = DteDet::create($aux_dtedet);
                    $dtedetCreados[] = $dtedetNew;
                    $dtedet_id = $dtedetNew->id;
                    $dtedet_despachoorddet = new DteDet_DespachoOrdDet();
                    $dtedet_despachoorddet->dtedet_id = $dtedet_id;
                    $dtedet_despachoorddet->despachoorddet_id = $despachoorddet_id;
                    $dtedet_despachoorddet->notaventadetalle_id = $notaventadetalle_id;
                    $dtedet_despachoorddet->save();
                }

                $dtefac->dte_id = $dteNew->id;
                $dtefac->save();

                foreach ($dte->dtedtes as $dtedte) {
                    $dtedteNew = new DteDte();
                    $dtedteNew->dte_id = $dteNew->id;
                    $dtedteNew->dter_id = $dtedte->dter_id;
                    $dtedteNew->dtefac_id = $dteNew->id;
                    $dtedteNew->save();
                    //RECORRO TODAS LAS GUIAS DE DESPACHO INVOLUCRADAS
                    if($dtedte->dter->foliocontrol_id == 2){ //ASEGURO QUE EL DTE SEA GUIA DE DESPACHO foliocontrol_id==2
                        //FUNCION QUE ASIGNA A CADA ORDEN DE DESPACHO EL NUMERO, FECHA Y FECHAHORA DE EMISION DE FACTURA
                        DespachoOrd::guardarfactdesp($dtedteNew);
                    }
                }

                foreach ($dte->dteguiausadas as $dteguiausada) {
                    $dteguiausadaNew = new DteGuiaUsada();
                    $dteguiausadaNew->dte_id = $dteguiausada->dte_id;
                    $dteguiausadaNew->usuario_id = auth()->id();
                    $dteguiausadaNew->save();
                }

                /* Sin traslado no se registra dtetrans: el XML arma la seccion
                   <Transporte> solo si existe ese registro, asi que omitirlo basta
                   para que la factura de servicio salga sin datos de transporte. */
                if($aux_contraslado){
                    $dteTransService = new DteTransService();
                    $dteTransService->crearDesdeRequest($request, $dteNew->id);
                }

                DB::commit();

            } catch (\Throwable $e) {
                DB::rollBack();
                DteDet::setEventDispatcher($dispatcher);
                Log::error('DteFacturaDir guardar error: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                return redirect()->back()->with([
                    'mensaje' => 'Error al guardar factura: ' . $e->getMessage(),
                    'tipo_alert' => 'alert-error'
                ])->withInput();
            }
            DteDet::setEventDispatcher($dispatcher);
            foreach ($dtedetCreados as $det) {
                DocInsumoDetService::syncFromDetalle($det, 'DTE');
            }
            $foliocontrol->bloqueo = 0;
            $foliocontrol->ultfoliouti = $dteNew->nrodocto;
            $foliocontrol->save();
            $aux_foliosdisp = $foliocontrol->ultfoliohab - $foliocontrol->ultfoliouti;
            // Llamadas a servicios externos FUERA de la transacción
            // El DTE ya está guardado en BD; si falla la subida al SII queda pendiente
            $dte = Dte::findOrFail($dteNew->id);
            $respuesta = Dte::subirDteSii($dte);
            /* if($respuesta["id"] == 1){
                Dte::guardarPdfXmlSii($dte->nrodocto,$foliocontrol,$respuesta["Carga_TXTDTE"]);
            } */
            Dte::subirSisCobranza($dte);

            if($aux_foliosdisp <= $foliocontrol->folmindisp){
                $aux_mensaje = "Factura creada con exito. Quedan $aux_foliosdisp folios disponibles!";
                $aux_tipo_alert = 'alert-error';
            }else{
                $aux_mensaje = 'Factura creada con exito.';
                $aux_tipo_alert = 'alert-success';
            }
            return redirect('dtefacturadir')->with([
                'mensaje'=> $aux_mensaje,
                'tipo_alert' => $aux_tipo_alert
            ]);
        }else{
            /* $foliocontrol->bloqueo = 0;
            $foliocontrol->save(); */
            return redirect('dtefacturadir')->with([
                'mensaje'=>$respuesta["mensaje"] ,
                'tipo_alert' => 'alert-error'
            ]);
        }
    }

    /**
     * Diagnóstico del formulario de factura directa.
     * Ejecuta TODA la lógica de guardar() con un folio ficticio (9999)
     * pero siempre hace ROLLBACK al final — no guarda nada, no consume folios reales.
     * Devuelve JSON con el detalle de cada paso.
     */
    public function diagnosticar(Request $request)
    {
        can('guardar-dte-factura-directa');

        $pasos = [];
        $ok = true;

        try {
            // PASO 1: Validar cantidad de productos
            $cont_producto = count($request->producto_id ?? []);
            if ($cont_producto <= 0) {
                return response()->json(['ok' => false, 'pasos' => [['paso' => 'Productos', 'ok' => false, 'detalle' => 'No hay ítems en el formulario']]]);
            }
            $pasos[] = ['paso' => 'Productos', 'ok' => true, 'detalle' => "Cantidad de ítems: $cont_producto"];

            // PASO 2: Validar foliocontrol maxitem
            $foliocontrol = Foliocontrol::findOrFail(1);
            if ($cont_producto > $foliocontrol->maxitemxdoc) {
                $pasos[] = ['paso' => 'Max ítems', 'ok' => false, 'detalle' => "Items: $cont_producto, Máximo: {$foliocontrol->maxitemxdoc}"];
                return response()->json(['ok' => false, 'pasos' => $pasos]);
            }
            $pasos[] = ['paso' => 'Max ítems', 'ok' => true, 'detalle' => "OK (max: {$foliocontrol->maxitemxdoc})"];

            // PASO 3: Validar cliente
            $cliente = Cliente::findOrFail($request->cliente_id);
            $pasos[] = ['paso' => 'Cliente', 'ok' => true, 'detalle' => "ID: {$cliente->id} | {$cliente->razonsocial}"];

            // PASO 4: Cliente bloqueado
            foreach ($cliente->clientebloqueados as $bloqueo) {
                $pasos[] = ['paso' => 'Bloqueo cliente', 'ok' => false, 'detalle' => $bloqueo->descripcion];
                return response()->json(['ok' => false, 'pasos' => $pasos]);
            }
            $pasos[] = ['paso' => 'Bloqueo cliente', 'ok' => true, 'detalle' => 'Sin bloqueos'];

            // PASO 5: Armar objeto DTE
            $dte = new Dte();
            $dtefac = new DteFac();
            $dtefac->hep = $request->hep;
            $dtefac->notped = $request->notped;
            $dtefac->formapago_id = $cliente->formapago_id;
            $dtefac->fchvenc = date('Y-m-d', strtotime(date('Y-m-d') . "+ " . $cliente->plazopago->dias . " days"));
            $dte->dtefac = $dtefac;
            $pasos[] = ['paso' => 'Armar DTE', 'ok' => true, 'detalle' => "formapago_id: {$dtefac->formapago_id} | fchvenc: {$dtefac->fchvenc}"];

            // PASO 6: OC (si aplica)
            if (!is_null($request->oc_id)) {
                $dteoc = new DteOC();
                $dteoc->dte_id = "";
                $dteoc->oc_id = $request->oc_id;
                $dteoc->oc_folder = "oc";
                $dteoc->oc_file = $request->oc_file;
                $dte->dteocs[] = $dteoc;
                $pasos[] = ['paso' => 'OC', 'ok' => true, 'detalle' => "oc_id: {$request->oc_id}"];
            } else {
                $pasos[] = ['paso' => 'OC', 'ok' => true, 'detalle' => 'Sin OC'];
            }

            // PASO 7: Ítems / DteDet
            $Tmntneto = 0;
            $Tkgtotal = 0;
            for ($i = 0; $i < $cont_producto; $i++) {
                if (is_null($request->producto_id[$i]) == false && is_null($request->qtyitem[$i]) == false) {
                    $unidadmedida = UnidadMedida::findOrFail($request->unmditem[$i]);
                    $dtedet = new DteDet();
                    $dtedet->producto_id = $request->producto_id[$i];
                    $dtedet->nrolindet = ($i + 1);
                    $dtedet->vlrcodigo = $request->producto_id[$i];
                    $dtedet->nmbitem = $request->nmbitem[$i];
                    $dtedet->qtyitem = $request->qtyitem[$i];
                    $dtedet->unmditem = substr($unidadmedida->nombre, 0, 4);
                    $dtedet->unidadmedida_id = $request->unmditem[$i];
                    $dtedet->prcitem = $request->prcitem[$i];
                    $dtedet->montoitem = round($dtedet->qtyitem * $dtedet->prcitem, 0);
                    $aux_itemkg = is_numeric($request->itemkg[$i]) ? $request->itemkg[$i] : 0;
                    if ($aux_itemkg == 0 && $dtedet->unidadmedida_id == 7) {
                        $aux_itemkg = $dtedet->qtyitem;
                    }
                    $dtedet->itemkg = $aux_itemkg;
                    $dte->dtedets[] = $dtedet;
                    $Tmntneto += $dtedet->montoitem;
                    $Tkgtotal += $aux_itemkg;
                    $pasos[] = ['paso' => "Ítem " . ($i + 1), 'ok' => true, 'detalle' => "{$dtedet->nmbitem} | qty: {$dtedet->qtyitem} | precio: {$dtedet->prcitem} | monto: {$dtedet->montoitem}"];
                }
            }

            // PASO 8: Neto > 0
            if ($Tmntneto <= 0) {
                $pasos[] = ['paso' => 'Neto total', 'ok' => false, 'detalle' => "Neto: $Tmntneto — debe ser mayor a cero"];
                return response()->json(['ok' => false, 'pasos' => $pasos]);
            }
            $pasos[] = ['paso' => 'Neto total', 'ok' => true, 'detalle' => "Neto: $Tmntneto | KG: $Tkgtotal"];

            // PASO 9: IVA y totales
            $empresa = Empresa::findOrFail(1);
            if ($request->foliocontrol_id == 1) {
                $Tiva = round(($empresa->iva / 100) * $Tmntneto);
                $Tmnttotal = round((($empresa->iva / 100) + 1) * $Tmntneto);
                $dte->tasaiva = $empresa->iva;
                $dte->iva = $Tiva;
                $dte->mnttotal = $Tmnttotal;
            }
            if ($request->foliocontrol_id == 7) {
                $dte->tasaiva = 0;
                $dte->iva = 0;
                $dte->mnttotal = $Tmntneto;
            }
            $pasos[] = ['paso' => 'Totales', 'ok' => true, 'detalle' => "IVA: {$dte->iva} | Total: {$dte->mnttotal}"];

            // PASO 10: Campos DTE cabecera
            $centroeconomico = CentroEconomico::findOrFail($request->centroeconomico_id);
            $hoy = date("Y-m-d H:i:s");
            $dte->foliocontrol_id = $request->foliocontrol_id;
            $dte->nrodocto = "";
            $dte->fchemis = date('Y-m-d');
            $dte->fchemisgen = $hoy;
            $dte->fechahora = $hoy;
            $dte->sucursal_id = $centroeconomico->sucursal_id;
            $dte->cliente_id = $cliente->id;
            $dte->comuna_id = $cliente->comunap_id;
            $dte->ciudad_id = $cliente->ciudad_id;
            $dte->vendedor_id = $request->vendedor_id;
            $dte->obs = $request->obs;
            /* Misma regla que en guardar(): sin traslado no se declara despacho.
               Se replica aqui para que el diagnostico refleje lo que realmente
               ocurre al emitir. */
            $aux_contraslado = ($request->tipooperacion != 2);
            $dte->tipodespacho = $aux_contraslado ? 2 : null;
            $dte->indtraslado = $aux_contraslado ? 1 : null;
            $dte->mntneto = $Tmntneto;
            $dte->kgtotal = $Tkgtotal;
            $dte->centroeconomico_id = $request->centroeconomico_id;
            $dte->usuario_id = $request->usuario_id;
            $pasos[] = ['paso' => 'Cabecera DTE', 'ok' => true, 'detalle' => "sucursal_id: {$dte->sucursal_id} | foliocontrol_id: {$dte->foliocontrol_id} | cliente_id: {$dte->cliente_id}"];

            // PASO 11: Folio (SIMULADO — no llama al webservice real)
            $respuestaFolio = ['id' => 1, 'aux_folio' => 9999];
            $pasos[] = ['paso' => 'Solicitar folio (SIMULADO)', 'ok' => true, 'detalle' => "Folio ficticio: {$respuestaFolio['aux_folio']} — NO se consumió folio real"];

            // PASO 12: BEGIN TRANSACTION — insertar en BD (se hace rollback al final)
            DB::beginTransaction();

            $dte->fchemisgen = date("Y-m-d H:i:s");
            $dte->nrodocto = $respuestaFolio["aux_folio"];
            $dteNew = Dte::create($dte->toArray());
            $pasos[] = ['paso' => 'Crear DTE en BD', 'ok' => true, 'detalle' => "ID temporal (rollback): {$dteNew->id} | nrodocto: {$dteNew->nrodocto}"];

            // PASO 13: Insertar DteDet
            foreach ($dte->dtedets as $dtedet) {
                $dtedet->dte_id = $dteNew->id;
                $aux_dtedet = $dtedet->toArray();
                unset($aux_dtedet["despachoorddet_id"]);
                unset($aux_dtedet["notaventadetalle_id"]);
                $dtedetNew = DteDet::create($aux_dtedet);
                $dtedet_despachoorddet = new DteDet_DespachoOrdDet();
                $dtedet_despachoorddet->dtedet_id = $dtedetNew->id;
                $dtedet_despachoorddet->despachoorddet_id = $dtedet->despachoorddet_id ?? null;
                $dtedet_despachoorddet->notaventadetalle_id = $dtedet->notaventadetalle_id ?? null;
                $dtedet_despachoorddet->save();
                $pasos[] = ['paso' => "Crear DteDet ítem {$dtedet->nrolindet}", 'ok' => true, 'detalle' => "ID temporal: {$dtedetNew->id} | {$dtedet->nmbitem}"];
            }

            // PASO 14: DteFac
            $dtefac->dte_id = $dteNew->id;
            $dtefac->save();
            $pasos[] = ['paso' => 'Crear DteFac', 'ok' => true, 'detalle' => "ID temporal: {$dtefac->id}"];

            // PASO 15: DteTransService
            if($aux_contraslado){
                $dteTransService = new DteTransService();
                $dteTransService->crearDesdeRequest($request, $dteNew->id);
                $pasos[] = ['paso' => 'DteTransService', 'ok' => true, 'detalle' => 'Datos de transporte procesados'];
            }else{
                $pasos[] = ['paso' => 'DteTransService', 'ok' => true, 'detalle' => 'Omitido: operacion sin traslado de bienes'];
            }

            // PASO 16: foliocontrol update (simulado)
            $pasos[] = ['paso' => 'Actualizar foliocontrol', 'ok' => true, 'detalle' => "ultfoliouti sería: {$respuestaFolio['aux_folio']} (NO se actualizó — rollback)"];

        } catch (\Exception $e) {
            $pasos[] = ['paso' => 'EXCEPCIÓN', 'ok' => false, 'detalle' => $e->getMessage() . ' | Archivo: ' . $e->getFile() . ':' . $e->getLine()];
            $ok = false;
        } finally {
            // Siempre rollback — nunca guardamos nada real
            DB::rollBack();
        }

        $resultado = $ok ? '✓ Todos los pasos OK — el formulario guardaría correctamente' : '✗ Se detectó un error (ver paso marcado)';
        return response()->json([
            'ok' => $ok,
            'resultado' => $resultado,
            'pasos' => $pasos,
            'nota' => 'ROLLBACK ejecutado — no se guardó nada en BD, no se consumió folio real'
        ], 200, ['Content-Type' => 'application/json'], JSON_PRETTY_PRINT);
    }

    public function procesar(Request $request)
    {
        if ($request->ajax()) {
            $dte = Dte::findOrFail($request->dte_id);
            if($dte->updated_at != $request->updated_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            $empresa = Empresa::findOrFail(1);
            $soap = new SoapController();
            $Estado_DTE = $soap->Estado_DTE($empresa->rut,$dte->foliocontrol->tipodocto,$dte->nrodocto);
            //$Estado_DTE = $soap->Estado_DTE($empresa->rut,$dte->foliocontrol->tipodocto,"200");
            //dd($Estado_DTE);
            if($Estado_DTE->Estatus == 3){
                return response()->json([
                    'id' => 0,
                    'mensaje' => $Estado_DTE->MsgEstatus . " Nro: " . $dte->nrodocto,
                    'tipo_alert' => 'error'
                ]);
            }
            if($Estado_DTE->EstadoDTE == 16){
                return Dte::updateStatusGen($dte,$request);
            }else{
                return response()->json([
                    'id' => 0,
                    'mensaje' => $Estado_DTE->DescEstado . " Nro: " . $dte->nrodocto,
                    'tipo_alert' => 'error'
                ]);
            }
        }
    }
}

function consultaindex($dte_id){
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    if(empty($dte_id)){
        $aux_conddte_id = " true";
    }else{
        $aux_conddte_id = "dte.id = $dte_id";
    }

    $aux_verFacturas = can('ver-facturas-de-todos-los-usuarios',false);
    $aux_condFiltrarxUsuario = " true ";
    if(!$aux_verFacturas){
        $aux_condFiltrarxUsuario = " dte.usuario_id = $user->id ";
    }

    $sql = "SELECT dte.id,dte.nrodocto,dte.fechahora,cliente.rut,cliente.razonsocial,dte.stasubsii,dte.stasubcob,
    dte.cliente_id,
    comuna.nombre as nombre_comuna,
    clientebloqueado.descripcion as clientebloqueado_descripcion,
    dteoc.oc_id,dteoc.oc_folder,dteoc.oc_file,foliocontrol.tipodocto,foliocontrol.nombrepdf,dte.updated_at,
    if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
    cliente.limitecredito,
    IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
    IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
    IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
    IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
    modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
    IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs
    FROM dte LEFT JOIN dteoc
    ON dteoc.dte_id = dte.id AND ISNULL(dte.deleted_at) AND ISNULL(dteoc.deleted_at)
    INNER JOIN cliente
    ON dte.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = cliente.comunap_id
    LEFT JOIN clientebloqueado
    ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    INNER JOIN foliocontrol
    ON  foliocontrol.id = dte.foliocontrol_id
    LEFT JOIN vista_datacobranza
    ON vista_datacobranza.cliente_id = dte.cliente_id
    LEFT JOIN clientedesbloqueado
    ON clientedesbloqueado.cliente_id = dte.cliente_id and isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
    LEFT JOIN clientedesbloqueadomodulo
    ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 16
    LEFT JOIN modulo
    ON modulo.id = clientedesbloqueadomodulo.modulo_id
    LEFT JOIN clientedesbloqueadopro
    ON clientedesbloqueadopro.cliente_id = dte.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
    WHERE (dte.foliocontrol_id=1 OR dte.foliocontrol_id=7)
    AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
    AND dte.id NOT IN (SELECT dtedte.dte_id FROM dtedte WHERE ISNULL(dtedte.deleted_at))
    AND dte.sucursal_id IN ($sucurcadena)
    AND ISNULL(dte.statusgen)
    AND !ISNULL(dte.nrodocto)
    AND $aux_conddte_id
    AND $aux_condFiltrarxUsuario
    AND ISNULL(dte.deleted_at)
    GROUP BY dte.id
    ORDER BY dte.id desc;";

    return DB::select($sql);
}

function mensajeRespuesta($mensaje){
    return redirect('dtefacturadir')->with([
        'mensaje' => $mensaje,
        'tipo_alert' => 'alert-error'
    ]);
}