<?php

namespace App\Http\Controllers;

use App\Models\CentroEconomico;
use App\Models\Cliente;
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
use App\Models\UnidadMedida;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DteFacturaDirAntiguaController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-factura-directa-antigua');
        return view('dtefacturadirantigua.index');
    }

    public function dtefacturadirantiguapage($dte_id = ""){
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
        can('crear-dte-factura-directa-antigua');
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedidas'] = UnidadMedida::orderBy('id')->get();
        $tablas['foliocontrol'] = Foliocontrol::orderBy('id')->get();
        $centroeconomicos = CentroEconomico::orderBy('id')->get();

        //dd($tablas);
        return view('dtefacturadirantigua.crear',compact('tablas','centroeconomicos'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        can('guardar-dte-factura-directa-antigua');
        //dd($request);
        $dtebusq = Dte::where("nrodocto",$request->nrodocto)->get();
        if(count($dtebusq) > 0){
            return redirect('dtefacturadirantigua')->with([
                "mensaje"=>"Número de factura $request->nrodocto ya existe.",
                "tipo_alert" => "alert-error"
            ]);
        }        
        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            return redirect('dtefacturadirantigua')->with([
                'mensaje'=>'No hay items, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $cliente = Cliente::findOrFail($request->cliente_id);
        foreach ($cliente->clientebloqueados as $clientebloqueado) {
            return redirect('dtefacturadirantigua')->with([
                'id' => 0,
                'mensaje'=>'No es posible hacer Factura, Cliente Bloqueado: ' . $clientebloqueado->descripcion,
                'tipo_alert' => 'alert-error'
            ]);
        }
        $dte = new Dte();
        $dtefac = new DteFac();
        //$dtefac->dte_id = $dte_id;
        $dtefac->hep = $request->hep;
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
                $dtedet->montoitem = $request->montoitem[$i];
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
            return redirect('dtefacturadirantigua')->with([
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
        $dte->nrodocto = $request->nrodocto;
        $dateInput = explode('/',$request->fchemis);
        $fchemis = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        $dte->fchemis = $fchemis;
        $dte->fchemisgen = $hoy;
        $dte->fechahora = $hoy;
        $dte->sucursal_id = $centroeconomico->sucursal_id;
        $dte->cliente_id = $cliente->id;
        $dte->comuna_id = $cliente->comunap_id;
        $dte->vendedor_id = $request->vendedor_id;
        $dte->obs = $request->obs;
        $dte->tipodespacho = 2;
        $dte->indtraslado =  1;
        $dte->mntneto = $Tmntneto;
        $dte->kgtotal = $Tkgtotal;
        $dte->centroeconomico_id = $request->centroeconomico_id;
        $dte->usuario_id = $request->usuario_id;

        //$respuesta = Dte::generardteprueba($dte);
        $respuesta = response()->json([
            'id' => 1
        ]);
        $foliocontrol = Foliocontrol::findOrFail($dte->foliocontrol_id);
        if($respuesta->original["id"] == 1){
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

            $foliocontrol->bloqueo = 0;
            $foliocontrol->ultfoliouti = $dteNew->nrodocto;
            $foliocontrol->save();
            return redirect('dtefacturadirantigua')->with([
                'mensaje'=>'Factura creada con exito.',
                'tipo_alert' => 'alert-success'
            ]);
        }else{
            $foliocontrol->bloqueo = 0;
            $foliocontrol->save();
            return redirect('dtefacturadirantigua')->with([
                'mensaje'=>$respuesta->original["mensaje"] ,
                'tipo_alert' => 'alert-error'
            ]);
        }
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

    $sql = "SELECT dte.id,dte.fchemis,dte.nrodocto,dte.fechahora,cliente.rut,cliente.razonsocial,
    comuna.nombre as nombre_comuna,
    clientebloqueado.descripcion as clientebloqueado_descripcion,
    dteoc.oc_id,dteoc.oc_folder,dteoc.oc_file,foliocontrol.tipodocto,foliocontrol.nombrepdf,dte.updated_at
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
    WHERE (dte.foliocontrol_id=1 OR dte.foliocontrol_id=7)
    AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
    AND dte.id NOT IN (SELECT dtedte.dte_id FROM dtedte WHERE ISNULL(dtedte.deleted_at))
    AND dte.sucursal_id IN ($sucurcadena)
    AND ISNULL(dte.statusgen)
    AND !ISNULL(dte.nrodocto)
    AND $aux_conddte_id
    AND ISNULL(dte.deleted_at)
    GROUP BY dte.id
    ORDER BY dte.id desc;";

    return DB::select($sql);
}