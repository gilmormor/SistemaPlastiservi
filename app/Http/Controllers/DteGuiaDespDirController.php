<?php

namespace App\Http\Controllers;

use App\Models\CentroEconomico;
use App\Models\Cliente;
use App\Models\ClienteDesBloqueado;
use App\Models\Comuna;
use App\Models\DataCobranza;
use App\Models\Dte;
use App\Models\DteDet;
use App\Models\DteGuiaDesp;
use App\Models\DteOC;
use App\Models\Empresa;
use App\Models\Foliocontrol;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\TipoTraslado;
use App\Models\UnidadMedida;
use App\Models\Vendedor;
use App\Services\DocInsumoDetService;
use App\Services\DteTransService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DteGuiaDespDirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-guia-desp-directa');
        return view('dteguiadespdir.index');
    }

    public function dteguiadespdirpage($dte_id = ""){
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
        can('crear-dte-guia-desp-directa');
        $vendedor = Vendedor::vendedores("A");
        $tablas['vendedores'] = $vendedor['vendedores'];
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedidas'] = UnidadMedida::orderBy('id')->get();
        $tablas['foliocontrol'] = Foliocontrol::orderBy('id')->get();
        $tablas['comunas'] = Comuna::orderBy('id')->get();
        $tablas['centroeconomicos'] = CentroEconomico::orderBy('id')->get();
        $tablas['tipoentregas'] = TipoEntrega::orderBy('id')->get();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablas['centroeconomicos'] = CentroEconomico::orderBy('id')->get();
        $tablas['tipotraslado'] = TipoTraslado::orderBy('id')
                                ->where('activo', '=', 1)
                                ->get();
        //dd($tablas);
        return view('dteguiadespdir.crear',compact('tablas'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        //dd(can('guardar-dte-guia-desp-directa'));
        if(can('guardar-dte-guia-desp-directa') === true){
            $cont_producto = count($request->producto_id);
            if($cont_producto <=0 ){
                return redirect('dteguiadespdir')->with([
                    'mensaje'=>'No hay items, no se guardó.',
                    'tipo_alert' => 'alert-error'
                ]);
            }
            $foliocontrol = Foliocontrol::findOrFail(2);
            if($cont_producto > $foliocontrol->maxitemxdoc ){
                return redirect('dteguiadespdir')->with([
                    'mensaje' => 'Total items documento: ' . $cont_producto . '. Maximo items permitido por documento: ' . $foliocontrol->maxitemxdoc,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            $cliente = Cliente::findOrFail($request->cliente_id);
            foreach ($cliente->clientebloqueados as $clientebloqueado) {
                return redirect('dteguiadespdir')->with([
                    'id' => 0,
                    'mensaje'=>'No es posible hacer Guia Despacho, Condición financiera en revisión: ' . $clientebloqueado->descripcion,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            $request1 = new Request();
            $request1->merge(['modulo_id' => 13]);
            $request1->request->set('modulo_id', 13);
            $request1->merge(['deldesbloqueo' => 1]);
            $request1->request->set('deldesbloqueo', 1);
            $clibloq = clienteBloqueado($request->cliente_id,0,$request1);
            if(!is_null($clibloq["bloqueo"])){
                /* $request1 = new Request();
                $request1->merge(['cliente_id' => $request->cliente_id]);
                $request1->request->set('cliente_id', $request->cliente_id);
                $respuesta = DataCobranza::llenartabla($request1); */
    
                return redirect('dteguiadespdir')->with([
                    "mensaje" => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                    "tipo_alert" => "alert-error"
                ]);
            }
            $dte = new Dte();
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
    
                    $Tmntneto += $request->montoitem[$i];
                    $Tkgtotal += $aux_itemkg;
                }
            }
            if($Tmntneto <= 0){
                return redirect('dteguiadespdir')->with([
                    'mensaje'=> "Neto total de Guia debe ser mayor a cero" ,
                    'tipo_alert' => 'alert-error'
                ]);
            }
    
            $empresa = Empresa::findOrFail(1);
            if($request->foliocontrol_id == 2){
                $Tiva = round(($empresa->iva/100) * $Tmntneto);
                $Tmnttotal = round((($empresa->iva/100) + 1) * $Tmntneto);
                $dte->tasaiva = $empresa->iva;
                $dte->iva = $Tiva;
                $dte->mnttotal = $Tmnttotal;        
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
            $dte->tipodespacho = $request->tipodespacho;
            /* El tipo de traslado es obligatorio en toda guia de despacho: el Formato
               DTE 2.5 lo exige siempre (obligatoriedad 1) y el SII rechaza el documento
               con "'' no es un valor valido para ...IndTraslado" si llega vacio. Se
               valida aqui, en el servidor, porque hasta ahora solo lo garantizaba el
               atributo required de la vista: si el JS no carga o el POST llega por otra
               via, el folio se consume y el DTE queda rechazado. */
            $request->validate(
                ['indtraslado' => 'required|exists:tipotraslado,indtraslado'],
                ['indtraslado.required' => 'Debe seleccionar el tipo de traslado.',
                 'indtraslado.exists'   => 'El tipo de traslado seleccionado no es valido.']
            );
            $dte->indtraslado =  $request->indtraslado;
            $dte->mntneto = $Tmntneto;
            $dte->kgtotal = $Tkgtotal;
            $dte->centroeconomico_id = $request->centroeconomico_id;
            $dte->usuario_id = $request->usuario_id;
    
            $dteguiadesp = new DteGuiaDesp();
            $dteguiadesp->tipoentrega_id = $request->tipoentrega_id;
            $dteguiadesp->comunaentrega_id = $request->comunaentrega_id;
            $dteguiadesp->lugarentrega = $request->lugarentrega;
            $dteguiadesp->ot = $request->ot;
    
            $dte->dteguiadesp = $dteguiadesp;
    
    
            //$respuesta = Dte::generardteprueba($dte);
            $respuesta = Dte::dteSolicitarFolio($dte);
            /* $respuesta = [
                        'id' => 1,
                        'aux_folio' => 1234,
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
                    $dte->stasubcob = 1;
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
                        $aux_dtedet = $dtedet->toArray();
                        $dtedetNew = DteDet::create($aux_dtedet);
                        $dtedetCreados[] = $dtedetNew;
                    }

                    $dteguiadesp->dte_id = $dteNew->id;
                    $dteguiadesp->save();

                    /* Se registra dtetrans siempre que el documento ampare un traslado,
                       tambien cuando retira el cliente: la fila guarda las fechas de salida
                       y llegada y la direccion y comuna de destino, que son de impresion
                       obligatoria y no dependen del tipo de despacho. El servicio deja en
                       nulo el chofer y la patente cuando el tipo de despacho es 1, y el XML
                       omite esos tags al estar vacios. */
                    $dteTransService = new DteTransService();
                    $dteTransService->crearDesdeRequest($request, $dteNew->id);

                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    DteDet::setEventDispatcher($dispatcher);
                    Log::error('Error al guardar DTE Guia Despacho Dir', ['error' => $e->getMessage()]);
                    return redirect()->back()->with([
                        'mensaje' => 'Error: ' . $e->getMessage(),
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
                $dte = Dte::findOrFail($dteNew->id);
                $respuesta = Dte::subirDteSii($dte);
                /* if($respuesta["id"] == 1){
                    Dte::guardarPdfXmlSii($dte->nrodocto,$foliocontrol,$respuesta["Carga_TXTDTE"]);
                }   */  
                if($aux_foliosdisp <= $foliocontrol->folmindisp){
                    return redirect('dteguiadespdir')->with([
                        'mensaje'=>"Guia Despacho creada con exito. Quedan $aux_foliosdisp folios disponibles!" ,
                        'tipo_alert' => 'alert-error'
                    ]);
                }else{
                    return redirect('dteguiadespdir')->with([
                        'mensaje'=>'Guia Despacho creada con exito.',
                        'tipo_alert' => 'alert-success'
                    ]);    
                }
            }else{
                /* $foliocontrol->bloqueo = 0;
                $foliocontrol->save(); */
                return redirect('dteguiadespdir')->with([
                    'mensaje'=>$respuesta["mensaje"] ,
                    'tipo_alert' => 'alert-error'
                ]);
            }
        };

    }

    /**
     * Prueba el guardado completo de una guia de despacho directa sin consumir
     * folios ni dejar registros: usa un folio ficticio (9999) y termina siempre
     * con rollback. Replica los pasos de guardar() para que un fallo salga aqui
     * y no al emitir, cuando ya se gasto un folio real.
     *
     * Mismo patron que DteGuiaDespNVController@diagnosticar. Solo se expone en el
     * entorno local, igual que el boton que la invoca.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function diagnosticar(Request $request)
    {
        can('guardar-dte-guia-desp-directa');

        $pasos = [];
        /* Los eventos de DteDet se desactivan igual que en guardar(), para que el
           diagnostico no sea mas estricto que la emision real y de falsos errores. */
        $dispatcher = DteDet::getEventDispatcher();
        DteDet::unsetEventDispatcher();
        DB::beginTransaction();
        try {
            // Paso 1: Items
            $cont_producto = count($request->producto_id ?? []);
            $pasos[] = ['paso' => 'Items en documento', 'ok' => $cont_producto > 0, 'detalle' => "Total items: $cont_producto"];
            if ($cont_producto <= 0) {
                return $this->diagFallo($pasos, 'Sin items.', $dispatcher);
            }

            // Paso 2: Foliocontrol
            $foliocontrol = Foliocontrol::findOrFail(2);
            $overMax = $cont_producto > $foliocontrol->maxitemxdoc;
            $pasos[] = ['paso' => 'Foliocontrol', 'ok' => !$overMax, 'detalle' => "foliocontrol_id=2, maxitemxdoc={$foliocontrol->maxitemxdoc}, items=$cont_producto"];
            if ($overMax) {
                return $this->diagFallo($pasos, '', $dispatcher);
            }

            // Paso 3: Tipo documento
            $tipoOk = ($request->foliocontrol_id == 2);
            $pasos[] = ['paso' => 'Tipo documento', 'ok' => $tipoOk, 'detalle' => "foliocontrol_id enviado: {$request->foliocontrol_id} (debe ser 2, Guia de Despacho)"];
            if (!$tipoOk) {
                return $this->diagFallo($pasos, '', $dispatcher);
            }

            // Paso 4: Cliente
            $cliente = Cliente::findOrFail($request->cliente_id);
            $pasos[] = ['paso' => 'Cliente', 'ok' => true, 'detalle' => "ID={$cliente->id}, {$cliente->razonsocial}"];

            // Paso 5: Condicion financiera (las mismas dos validaciones de guardar())
            $aux_bloqueo = null;
            foreach ($cliente->clientebloqueados as $clientebloqueado) {
                $aux_bloqueo = $clientebloqueado->descripcion;
                break;
            }
            if (is_null($aux_bloqueo)) {
                $request1 = new Request();
                $request1->merge(['modulo_id' => 13, 'deldesbloqueo' => 1]);
                $request1->request->set('modulo_id', 13);
                $request1->request->set('deldesbloqueo', 1);
                $clibloq = clienteBloqueado($request->cliente_id, 0, $request1);
                $aux_bloqueo = $clibloq["bloqueo"];
            }
            $pasos[] = ['paso' => 'Condicion financiera', 'ok' => is_null($aux_bloqueo),
                        'detalle' => is_null($aux_bloqueo) ? 'Cliente sin bloqueos' : "En revision: $aux_bloqueo"];
            if (!is_null($aux_bloqueo)) {
                return $this->diagFallo($pasos, 'El cliente esta bloqueado.', $dispatcher);
            }

            // Paso 6: CentroEconomico
            $centroeconomico = CentroEconomico::findOrFail($request->centroeconomico_id);
            $pasos[] = ['paso' => 'Centro Economico', 'ok' => true, 'detalle' => "ID={$centroeconomico->id}"];

            /* Paso 7: un tipo de traslado vacio hace que el SII rechace el documento,
               asi que el diagnostico tiene que detectarlo igual que la emision real. */
            $pasos[] = ['paso' => 'Tipo de traslado', 'ok' => !empty($request->indtraslado),
                        'detalle' => empty($request->indtraslado) ? 'VACIO: el SII rechazaria el documento' : "indtraslado={$request->indtraslado}"];
            if (empty($request->indtraslado)) {
                return $this->diagFallo($pasos, 'Debe seleccionar el tipo de traslado.', $dispatcher);
            }

            // Paso 8: Totales
            $Tmntneto = 0;
            $Tkgtotal = 0;
            for ($i = 0; $i < $cont_producto; $i++) {
                if (!is_null($request->producto_id[$i]) && !is_null($request->qtyitem[$i]) && $request->qtyitem[$i] > 0) {
                    $Tmntneto += $request->montoitem[$i];
                    $Tkgtotal += is_numeric($request->itemkg[$i]) ? $request->itemkg[$i] : 0;
                }
            }
            $empresa = Empresa::findOrFail(1);
            $Tiva      = round(($empresa->iva / 100) * $Tmntneto);
            $Tmnttotal = round((($empresa->iva / 100) + 1) * $Tmntneto);
            $pasos[] = ['paso' => 'Totales', 'ok' => $Tmntneto > 0, 'detalle' => "Neto=$Tmntneto, IVA=$Tiva, Total=$Tmnttotal, Kg=$Tkgtotal"];
            if ($Tmntneto <= 0) {
                return $this->diagFallo($pasos, 'Neto total de Guia debe ser mayor a cero.', $dispatcher);
            }

            // Paso 9: Folio simulado
            $pasos[] = ['paso' => 'Solicitar folio (simulado)', 'ok' => true, 'detalle' => 'Folio ficticio 9999, no consume folios reales'];

            // Paso 10: Crear DTE
            $hoy = date("Y-m-d H:i:s");
            $dteNew = Dte::create([
                'foliocontrol_id'    => 2,
                'nrodocto'           => 9999,
                'fchemis'            => date('Y-m-d'),
                'fchemisgen'         => $hoy,
                'fechahora'          => $hoy,
                'sucursal_id'        => $centroeconomico->sucursal_id,
                'cliente_id'         => $cliente->id,
                'comuna_id'          => $cliente->comunap_id,
                'ciudad_id'          => $cliente->ciudad_id,
                'vendedor_id'        => $request->vendedor_id,
                'obs'                => $request->obs,
                'tipodespacho'       => $request->tipodespacho,
                'indtraslado'        => $request->indtraslado,
                'mntneto'            => $Tmntneto,
                'tasaiva'            => $empresa->iva,
                'iva'                => $Tiva,
                'mnttotal'           => $Tmnttotal,
                'kgtotal'            => $Tkgtotal,
                'centroeconomico_id' => $request->centroeconomico_id,
                'usuario_id'         => $request->usuario_id,
                'stasubcob'          => 1,
            ]);
            $pasos[] = ['paso' => 'Crear DTE', 'ok' => true, 'detalle' => "DTE ID={$dteNew->id} (ficticio, rollback al final)"];

            // Paso 11: DteDet
            for ($i = 0; $i < $cont_producto; $i++) {
                if (!is_null($request->producto_id[$i]) && !is_null($request->qtyitem[$i]) && $request->qtyitem[$i] > 0) {
                    $unidadmedida = UnidadMedida::findOrFail($request->unmditem[$i]);
                    $aux_itemkg = is_numeric($request->itemkg[$i]) ? $request->itemkg[$i] : 0;
                    if ($aux_itemkg == 0 and $request->unmditem[$i] == 7) {
                        $aux_itemkg = $request->qtyitem[$i];
                    }
                    DteDet::create([
                        'dte_id'          => $dteNew->id,
                        'dtedet_id'       => $request->dtedetorigen_id[$i] ?? null,
                        'producto_id'     => $request->producto_id[$i],
                        'nrolindet'       => ($i + 1),
                        'vlrcodigo'       => $request->producto_id[$i],
                        'nmbitem'         => $request->nmbitem[$i],
                        'qtyitem'         => $request->qtyitem[$i],
                        'unmditem'        => substr($unidadmedida->nombre, 0, 4),
                        'unidadmedida_id' => $request->unmditem[$i],
                        'prcitem'         => $request->prcitem[$i],
                        'montoitem'       => round($request->qtyitem[$i] * $request->prcitem[$i], 0),
                        'itemkg'          => $aux_itemkg,
                    ]);
                }
            }
            $pasos[] = ['paso' => 'Crear DteDet', 'ok' => true, 'detalle' => "$cont_producto item(s) procesados"];

            // Paso 12: DteGuiaDesp
            $dteguiadesp = DteGuiaDesp::create([
                'dte_id'           => $dteNew->id,
                'tipoentrega_id'   => $request->tipoentrega_id,
                'comunaentrega_id' => $request->comunaentrega_id,
                'lugarentrega'     => $request->lugarentrega,
                'ot'               => $request->ot,
            ]);
            $pasos[] = ['paso' => 'Crear DteGuiaDesp', 'ok' => true, 'detalle' => "ID={$dteguiadesp->id}"];

            // Paso 13: DteTransService
            /* Se registra siempre, igual que en guardar(): la fila guarda las fechas
               y el destino, que no dependen del tipo de despacho. Con el tipo 1 el
               servicio deja el chofer y la patente en nulo. */
            $dteTransService = new DteTransService();
            $dteTransService->crearDesdeRequest($request, $dteNew->id);
            $pasos[] = ['paso' => 'DteTransService', 'ok' => true,
                        'detalle' => ($request->tipodespacho == 1)
                            ? 'Registrado sin chofer ni patente (retira el cliente)'
                            : 'Datos de transporte procesados OK'];

        } catch (\Throwable $e) {
            DB::rollBack();
            DteDet::setEventDispatcher($dispatcher);
            $pasos[] = ['paso' => 'Excepcion', 'ok' => false, 'detalle' => $e->getMessage()];
            return response()->json([
                'ok'        => false,
                'resultado' => 'Error en diagnostico',
                'nota'      => 'Se hizo rollback. No se guardo nada.',
                'pasos'     => $pasos,
            ]);
        }

        DB::rollBack();
        DteDet::setEventDispatcher($dispatcher);
        return response()->json([
            'ok'        => true,
            'resultado' => 'Diagnostico exitoso',
            'nota'      => 'Todos los pasos OK. Rollback aplicado, no se guardo nada ni se consumieron folios reales.',
            'pasos'     => $pasos,
        ]);
    }

    /**
     * Corta el diagnostico devolviendo los pasos recorridos. Centraliza el
     * rollback y la reposicion del dispatcher de eventos para no repetirlos en
     * cada salida temprana.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function diagFallo(array $pasos, $nota, $dispatcher)
    {
        DB::rollBack();
        DteDet::setEventDispatcher($dispatcher);
        return response()->json([
            'ok'        => false,
            'resultado' => 'Diagnostico fallido',
            'nota'      => $nota,
            'pasos'     => $pasos,
        ]);
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
            /*EN COMENTARIO: A PETICION DE ERIKA BUSTOS PARA EVITAR RETRASOS EN ENVIO DE GUIAS DE DESPACHO
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
            */
            return Dte::updateStatusGen($dte,$request);
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

    $aux_verguias = can('ver-guias-de-despacho-de-todos-los-usuarios',false);
    $aux_condFiltrarxUsuario = " true ";
    if(!$aux_verguias){
        $aux_condFiltrarxUsuario = " dte.usuario_id = $user->id ";
    }
    $sql = "SELECT dte.id,dte.nrodocto,dte.fechahora,cliente.rut,cliente.razonsocial,dte.stasubsii,
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
    INNER JOIN dteguiadesp
    ON dteguiadesp.dte_id = dte.id AND ISNULL(dteguiadesp.deleted_at)
    INNER JOIN cliente
    ON dte.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = cliente.comunap_id
    LEFT JOIN clientebloqueado
    ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    INNER JOIN foliocontrol
    ON foliocontrol.id = dte.foliocontrol_id
    LEFT JOIN vista_datacobranza
    ON vista_datacobranza.cliente_id = dte.cliente_id
    LEFT JOIN clientedesbloqueado
    ON clientedesbloqueado.cliente_id = dte.cliente_id and isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
    LEFT JOIN clientedesbloqueadomodulo
    ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 14
    LEFT JOIN modulo
    ON modulo.id = clientedesbloqueadomodulo.modulo_id
    LEFT JOIN clientedesbloqueadopro
    ON clientedesbloqueadopro.cliente_id = dte.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
    WHERE dte.foliocontrol_id=2
    AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
    AND dte.id NOT IN (SELECT dtedte.dte_id FROM dtedte WHERE ISNULL(dtedte.deleted_at))
    AND ISNULL(dteguiadesp.despachoord_id) and ISNULL(dteguiadesp.notaventa_id)
    AND dte.sucursal_id IN ($sucurcadena)
    AND ISNULL(dte.statusgen)
    AND !ISNULL(dte.nrodocto)
    AND $aux_conddte_id
    AND $aux_condFiltrarxUsuario
    GROUP BY dte.id
    ORDER BY dte.id desc;";

    return DB::select($sql);
}

function mensajeRespuesta($mensaje){
    return redirect('dteguiadespdir')->with([
        'mensaje' => $mensaje,
        'tipo_alert' => 'alert-error'
    ]);
}