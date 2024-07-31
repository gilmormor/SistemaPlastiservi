<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CentroEconomico;
use App\Models\Cliente;
use App\Models\ClienteDesBloqueado;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DataCobranza;
use App\Models\Dte;
use App\Models\DteDet;
use App\Models\DteGuiaDesp;
use App\Models\DteGuiaDespNV;
use App\Models\DteOC;
use App\Models\Empresa;
use App\Models\Foliocontrol;
use App\Models\Giro;
use App\Models\InvMovModulo;
use App\Models\NotaVenta;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\UnidadMedida;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DteGuiaDespNVController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-guia-desp-nv');
        $empresa = Empresa::findOrFail(1);
        $tablashtml['stabloxdeusiscob'] = $empresa->stabloxdeusiscob;
        return view('dteguiadespnv.index',compact('tablashtml'));
    }

    public function dteguiadespnvpage($dte_id = ""){
        $datas = consultaindex($dte_id);
        return datatables($datas)->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear($id)
    {
        can('crear-dte-guia-desp-nv');
        $data = NotaVenta::findOrFail($id);

        $request = new Request();
        $request->merge(['modulo_id' => 19]);
        $request->request->set('modulo_id', 19);
        $request->merge(['notaventa_id' => $data->id]);
        $request->request->set('notaventa_id', $data->id);
        $clibloq = clienteBloqueado($data->cliente_id,0,$request);
        if(!is_null($clibloq["bloqueo"])){   
            return redirect('dteguiadespnv/listarnv')->with([
                "mensaje" => "Cliente Bloqueado: " . $clibloq["bloqueo"],
                "tipo_alert" => "alert-error"
            ]);
        }

        $detalles = $data->notaventadetalles;
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedidas'] = UnidadMedida::orderBy('id')->get();
        $tablas['foliocontrol'] = Foliocontrol::orderBy('id')->get();
        $tablas['comunas'] = Comuna::orderBy('id')->get();
        $tablas['centroeconomicos'] = CentroEconomico::orderBy('id')->get();
        $tablas['tipoentregas'] = TipoEntrega::orderBy('id')->get();
        $invmovmodulo = InvMovModulo::where("cod","=","SOLDESP")->get();
        $array_bodegasmodulo = $invmovmodulo[0]->invmovmodulobodsals->pluck('id')->toArray();
        $empresa = Empresa::findOrFail(1);
        //dd($tablas);
        return view('dteguiadespnv.crear',compact('data','detalles','tablas','array_bodegasmodulo','empresa'));

    }

    public function listarnv()
    {
        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();
        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $user = Usuario::findOrFail(auth()->id());
        $tablashtml['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablashtml['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablashtml['sucurArray'])->get();
        $empresa = Empresa::findOrFail(1);
        $tablashtml['stabloxdeusiscob'] = $empresa->stabloxdeusiscob;
        return view('dteguiadespnv.listarnotaventa', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        can('guardar-dte-guia-desp-nv');
        //dd($request);
        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            return redirect('dteguiadespnv')->with([
                'mensaje'=>'No hay items, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $foliocontrol = Foliocontrol::findOrFail(2);
        if($cont_producto > $foliocontrol->maxitemxdoc ){
            return redirect('dteguiadespnv')->with([
                'mensaje' => 'Total items documento: ' . $cont_producto . '. Maximo items permitido por documento: ' . $foliocontrol->maxitemxdoc,
                'tipo_alert' => 'alert-error'
            ]);
        }

        $cliente = Cliente::findOrFail($request->cliente_id);
        foreach ($cliente->clientebloqueados as $clientebloqueado) {
            return redirect('dteguiadespnv')->with([
                'id' => 0,
                'mensaje'=>'No es posible hacer Guia Despacho, Cliente Bloqueado: ' . $clientebloqueado->descripcion,
                'tipo_alert' => 'alert-error'
            ]);
        }

        $notaventa = NotaVenta::findOrFail($request->notaventa_id);

        $request1 = new Request();
        $request1->merge(['modulo_id' => 19]);
        $request1->request->set('modulo_id', 19);
        $request1->merge(['notaventa_id' => $request->notaventa_id]);
        $request1->request->set('notaventa_id', $request->notaventa_id);
        $request1->merge(['deldesbloqueo' => 1]);
        $request1->request->set('deldesbloqueo', 1);
        $clibloq = clienteBloqueado($notaventa->cliente_id,0,$request1);
        if(!is_null($clibloq["bloqueo"])){
            $request1 = new Request();
            $request1->merge(['cliente_id' => $notaventa->cliente_id]);
            $request1->request->set('cliente_id', $notaventa->cliente_id);
            $respuesta = DataCobranza::llenartabla($request1);

            return redirect('dteguiadespnv/listarnv')->with([
                "mensaje" => "Cliente Bloqueado: " . $clibloq["bloqueo"],
                "tipo_alert" => "alert-error"
            ]);
        }

        $dte = new Dte();
        //CREAR REGISTRO DE ORDEN DE COMPRA
        //dd($request->oc_id);
        if(!is_null($notaventa->oc_id)){
            $dteoc = new DteOC();
            $dteoc->dte_id = "";
            $dteoc->oc_id = $notaventa->oc_id;
            $dteoc->oc_folder = "notaventa";
            $dteoc->oc_file = $notaventa->oc_file;
            $dte->dteocs[] = $dteoc;
        }else{
            if(!is_null($request->oc_id)){
                $dteoc = new DteOC();
                $dteoc->dte_id = "";
                $dteoc->oc_id = $request->oc_id;
                $dteoc->oc_folder = "oc";
                $dteoc->oc_file = $request->oc_file;
                $dte->dteocs[] = $dteoc;
                //$dteoc->save();
            }    
        }
        if(!is_null($request->notaventa_id)){
            $dteguiadespnv = new DteGuiaDespNV();
            $dteguiadespnv->dte_id = "";
            $dteguiadespnv->notaventa_id = $request->notaventa_id;
            $dte->dteguiadespnv = $dteguiadespnv;
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
            if((is_null($request->producto_id[$i])==false AND (is_null($request->qtyitem[$i])==false) AND $request->qtyitem[$i] > 0) OR $request->codref == 2){
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
                $unidadmedida = UnidadMedida::findOrFail($request->unidadmedida_id[$i]);
                $dtedet = new DteDet();
                $dtedet->dtedet_id = $request->dtedetorigen_id[$i];
                $dtedet->producto_id = $request->producto_id[$i];
                $dtedet->nrolindet = ($i + 1);
                $dtedet->vlrcodigo = $request->producto_id[$i];
                $dtedet->nmbitem = $request->nmbitem[$i];
                //$dtedet->dscitem = $request->dscitem[$i]; este valor aun no lo uso
                $dtedet->qtyitem = $request->qtyitem[$i];
                $dtedet->unmditem = substr($unidadmedida->nombre, 0, 4);
                $dtedet->unidadmedida_id = $request->unidadmedida_id[$i];
                $dtedet->prcitem = $request->prcitem[$i]; //$request->montoitem[$i]/$request->qtyitem[$i]; //$request->prcitem[$i];
                //$dtedet->montoitem = $request->montoitem[$i];
                $dtedet->montoitem = round($dtedet->qtyitem * $dtedet->prcitem,0); //$request->montoitem[$i];
                //$dtedet->obsdet = $request->obsdet[$i];
                $aux_itemkg = is_numeric($request->itemkg[$i]) ? $request->itemkg[$i] : 0;
                $dtedet->itemkg = $aux_itemkg;
                //$dtedet->save();
                $dte->dtedets[] = $dtedet;
                $dtedet_id = $dtedet->id;

                $Tmntneto += $request->montoitem[$i];
                $Tkgtotal += $aux_itemkg;
            }
        }
        if($Tmntneto <= 0){
            return redirect('dteguiadespnv')->with([
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
            if(!is_null($notaventa->oc_id)){
                $dteoc->dte_id = $dteNew->id;
                $dteoc->save();
            }else{
                if(isset($dteoc)){
                    if ($foto = Dte::setFoto($request->oc_file,$dteNew->id,$request,"DTE",$dteoc->oc_folder)){ //2 ultimos parametros son origen de orden de compra FC Factura y la carpeta donde se guarda la OC
                        $dteoc->dte_id = $dteNew->id;
                        $dteoc->oc_file = $foto;
                        $dteoc->save();
                    }    
                }    
            }
            foreach ($dte->dtedets as $dtedet) {
                $dtedet->dte_id = $dteNew->id;
                $aux_dtedet = $dtedet->toArray();
                $dtedetNew = DteDet::create($aux_dtedet);
            }

            $dteguiadesp->dte_id = $dteNew->id;
            $dteguiadesp->save();

            if(!is_null($request->notaventa_id)){
                $dteguiadespnv->dte_id = $dteNew->id;
                $dteguiadespnv->save();
            }    

            $foliocontrol->bloqueo = 0;
            $foliocontrol->ultfoliouti = $dteNew->nrodocto;
            $foliocontrol->save();
            $notaventa->updated_at = date("Y-m-d H:i:s");//ACTUALIZO LA FECHA DE MODIFICACION PARA VALIDAR EN OTRAS PANTALLAS, CON ESTE CAMPO VALIDO SI EL REGISTRO FUE MODIFICADO POR OTRO USUARIO
            $notaventa->save();
            $aux_foliosdisp = $foliocontrol->ultfoliohab - $foliocontrol->ultfoliouti;

            $dte = Dte::findOrFail($dteNew->id);
            $respuesta = Dte::subirDteSii($dte);
            if($respuesta["id"] == 1){
                Dte::guardarPdfXmlSii($dte->nrodocto,$foliocontrol,$respuesta["Carga_TXTDTE"]);
            }
            if($aux_foliosdisp <= $foliocontrol->folmindisp){
                return redirect('dteguiadespnv')->with([
                    'mensaje'=>"Guia Despacho creada con exito. Quedan $aux_foliosdisp folios disponibles!" ,
                    'tipo_alert' => 'alert-error'
                ]);
            }else{
                return redirect('dteguiadespnv')->with([
                    'mensaje'=>'Guia Despacho creada con exito.',
                    'tipo_alert' => 'alert-success'
                ]);    
            }
/*
            return redirect('dteguiadespnv')->with([
                'mensaje'=>'Factura creada con exito.',
                'tipo_alert' => 'alert-success'
            ]);
*/
        }else{
            $foliocontrol->bloqueo = 0;
            $foliocontrol->save();
            return redirect('dteguiadespnv')->with([
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    $sql = "SELECT dte.id,dte.nrodocto,dte.fechahora,cliente.rut,cliente.razonsocial,dte.stasubsii,
    comuna.nombre as nombre_comuna,
    clientebloqueado.descripcion as clientebloqueado_descripcion,
    dteoc.oc_id,dteoc.oc_folder,dteoc.oc_file,foliocontrol.tipodocto,foliocontrol.nombrepdf,dte.updated_at,
    cliente.limitecredito,
    clientebloqueado.descripcion as clientebloqueado_desc,
    IFNULL(datacobranza.tfac,0) AS datacobranza_tfac,
    IFNULL(datacobranza.tdeuda,0) AS datacobranza_tdeuda,
    IFNULL(datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
    IFNULL(datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
    modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
    dteguiadespnv.notaventa_id,dte.cliente_id,
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
    INNER JOIN dteguiadespnv
    ON dte.id = dteguiadespnv.dte_id
    LEFT JOIN datacobranza
    ON datacobranza.cliente_id = dte.cliente_id
    LEFT JOIN clientedesbloqueado
    ON clientedesbloqueado.cliente_id = dte.cliente_id and clientedesbloqueado.notaventa_id = dteguiadespnv.notaventa_id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
    LEFT JOIN clientedesbloqueadomodulo
    ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 20
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