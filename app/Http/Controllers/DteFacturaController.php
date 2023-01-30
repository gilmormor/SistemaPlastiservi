<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarDTE;
use App\Http\Requests\ValidarDTEFac;
use App\Models\AreaProduccion;
use App\Models\CentroEconomico;
use App\Models\Cliente;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoOrd;
use App\Models\Dte;
use App\Models\DteAnul;
use App\Models\DteDet;
use App\Models\DteDet_DespachoOrdDet;
use App\Models\DteDte;
use App\Models\DteFac;
use App\Models\DteGuiaUsada;
use App\Models\Empresa;
use App\Models\Foliocontrol;
use App\Models\Giro;
use App\Models\NotaVentaCerrada;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class DteFacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-factura-gd');
        return view('dtefactura.index');
    }

    public function dtefacturapage($dte_id = ""){
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
        can('crear-dte-factura-gd');
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        $tablas['empresa'] = Empresa::findOrFail(1);
        $centroeconomicos = CentroEconomico::orderBy('id')->get();

        //dd($tablas);
        return view('dtefactura.crear',compact('tablas','centroeconomicos'));

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarDTEFac $request)
    {        
        can('guardar-dte-factura-gd');
        $aux_arrayselgd = explode(",", $request->selectguiadesp);
        //dd($request);
        //BUSCO SI HUBO MODIFICACION EN LAS GUIAS DE DESPACHO 
        //SI LA FECHA UPDATE_AT ES DIFERENTE A LA QUE VIENE DE LAS GUIAS SELECCIONADAS DETIENE LA EJECUCION Y RETORNA AL INDEX dtefactura
        foreach ($aux_arrayselgd as &$valor) {
            $indice = array_search($valor,$request->dte_idGD,false);
            $dteguiadesp = Dte::findOrFail($valor);
            if($request->updated_atGD[$indice] != $dteguiadesp->updated_at){
                return redirect('dtefactura')->with([
                    'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                    'tipo_alert' => 'alert-error'
                ]);
            }
            $dteguiadesp->updated_at = date("Y-m-d H:i:s");
            $dteguiadesp->save();
        }
        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            /*
            return redirect('dtefactura')->with([
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'alert-error'
            ]);*/

            return redirect('dtefactura')->with([
                'mensaje'=>'No hay items, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        
        $centroeconomico = CentroEconomico::findOrFail($request->centroeconomico_id);
        $cliente = Cliente::findOrFail($request->cliente_id);
        $date = str_replace('/', '-', $request->fchemis);
        $request->request->add(['fchemis' => date('Y-m-d')]);


        $hoy = date("Y-m-d H:i:s");
        $request->request->add(['tipodespacho' => 2]);
        $request->request->add(['fechahora' => $hoy]);
        $request->request->add(['tasaiva' => $dteguiadesp->iva]);
        $request->request->add(['sucursal_id' => $centroeconomico->sucursal_id]);
        $request->request->add(['comuna_id' => $cliente->comunap_id]);
        $request->request->add(['foliocontrol_id' => 1]); //CODIGO DE TIPO DE DTE 1=FACTURA

        $dte = Dte::create($request->all());
        $dte_id = $dte->id;
        //GUARDAR DATOS FACTURA
        $dtefac = new DteFac();
        $dtefac->dte_id = $dte_id;
        $dtefac->hep = $request->hep;
        $dtefac->formapago_id = $cliente->formapago_id;
        $dtefac->fchvenc =  date('Y-m-d', strtotime(date('Y-m-d') ."+ " . $cliente->plazopago->dias . " days"));
        $dtefac->save();

        $Tmntneto = 0;
        $Tiva = 0;
        $Tmnttotal = 0;
        $Tkgtotal = 0;

        $aux_nrolindet = 0;
        foreach ($aux_arrayselgd as &$dter_id){
            $dtedte = new DteDte();
            $dtedte->dte_id = $dte_id;
            $dtedte->dter_id = $dter_id;
            $dtedte->dtefac_id = $dte_id; 
            $dtedte->save();

            $dteguiadesp = Dte::findOrFail($dter_id); //BUSCO LA GUIA ORIGEN
            foreach($dteguiadesp->dtedets as $dtedetguia){ //RECORRO EL DETALLE DE LA GUIA ORIGEN
                $aux_nrolindet++;
                $dtedet = new DteDet();
                $dtedet->dte_id = $dte_id;
                $dtedet->dtedet_id = $dtedetguia->id;
                $dtedet->producto_id = $dtedetguia->producto_id;
                $dtedet->nrolindet = $aux_nrolindet;
                $dtedet->vlrcodigo = $dtedetguia->vlrcodigo;
                $dtedet->nmbitem = $dtedetguia->nmbitem;
                $dtedet->dscitem = $dtedetguia->dscitem;
                $dtedet->qtyitem = $dtedetguia->qtyitem;
                $dtedet->unmditem = $dtedetguia->unmditem;
                $dtedet->unidadmedida_id = $dtedetguia->unidadmedida_id;
                $dtedet->prcitem = $dtedetguia->prcitem;
                $dtedet->montoitem = $dtedetguia->montoitem;
                $dtedet->obsdet = $dtedetguia->obsdet;
                $dtedet->itemkg = $dtedetguia->itemkg;
                $dtedet->save();

                $Tmntneto += $dtedetguia->montoitem;
                $Tkgtotal += $dtedetguia->itemkg;

                $dtedet_id = $dtedet->id;
                $dtedet_despachoorddet = new DteDet_DespachoOrdDet();
                $dtedet_despachoorddet->dtedet_id = $dtedet_id;
                $dtedet_despachoorddet->despachoorddet_id = $dtedetguia->dtedet_despachoorddet->despachoorddet_id;
                $dtedet_despachoorddet->notaventadetalle_id = $dtedetguia->dtedet_despachoorddet->notaventadetalle_id;
                $dtedet_despachoorddet->save();
            }
            $dteguiausada = new DteGuiaUsada();
            $dteguiausada->dte_id = $dteguiadesp->id;
            $dteguiausada->usuario_id = auth()->id();
            $dteguiausada->save();
        }
        $empresa = Empresa::findOrFail(1);
        if($Tmntneto>0){
            $Tiva = round(($empresa->iva/100) * $Tmntneto);
            $Tmnttotal = round((($empresa->iva/100) + 1) * $Tmntneto);    
        }

        $dte = Dte::findOrFail($dte_id);
        $dte->mntneto = $Tmntneto;
        $dte->tasaiva = $empresa->iva;
        $dte->iva = $Tiva;
        $dte->mnttotal = $Tmnttotal;
        $dte->kgtotal = $Tkgtotal;
        $dte->save();

        return redirect('dtefactura')->with([
            'mensaje'=>'Factura creada con exito.',
            'tipo_alert' => 'alert-success'
        ]);
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
        can('editar-dte-factura-gd');
        $data = Dte::findOrFail($id);
        //dd(date("d/m/Y", strtotime($data->fchemis)));
        if($data->statusgen == 1){
            return redirect('data')->with([
                'mensaje'=>'Factura ya fue Generada! Nro: ' . $data->nrodocto ,
                'tipo_alert' => 'alert-error'
            ]);
        }

        if($data->guiadespanul){
            return redirect('dtefactura')->with([
                'mensaje'=>'Registro fué anulado!',
                'tipo_alert' => 'alert-error'
            ]);
        }
        //dd($data->notaventa->cliente->clientedirecs);
        $comunas = Comuna::orderBy('id')->get();
        $centroeconomicos = CentroEconomico::orderBy('id')->get();
        $tablas['empresa'] = Empresa::findOrFail(1);
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];

        return view('dtefactura.editar', compact('data','comunas','tablas','centroeconomicos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarDTEFac $request, $id)
    {
        //dd($request);
        can('guardar-dte-factura-gd');
        $aux_arrayselgd = explode(",", $request->selectguiadesp);
        //dd($request);
        //BUSCO SI HUBO MODIFICACION EN LAS GUIAS DE DESPACHO 
        //SI LA FECHA UPDATE_AT ES DIFERENTE A LA QUE VIENE DE LAS GUIAS SELECCIONADAS DETIENE LA EJECUCION Y RETORNA AL INDEX dtefactura
        //dd($request->dte_idGD);
        if($request->dte_idGD){
            for ($i=0; $i < count($request->dte_idGD); $i++) { 
                $valor = $request->dte_idGD[$i];
                $indice = array_search($valor,$aux_arrayselgd,false);
                if($indice){
                    $dteguiadesp = Dte::findOrFail($valor);
                    if($request->updated_atGD[$i] != $dteguiadesp->updated_at){
                        return redirect('dtefactura')->with([
                            'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                            'tipo_alert' => 'alert-error'
                        ]);
                    }
        
                }
            }    
        }
        $dte = Dte::findOrFail($id);
        //dd($dte->updated_at);
        if($dte->updated_at != $request->updated_at){
            return redirect('dtefactura')->with([
                'mensaje'=>'Registro fué modificado por otro usuario.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        if(!isset($request->producto_id)){
            return redirect('dtefactura')->with([
                'mensaje'=>'Factura sin items, no se actualizó.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $cont_det = count($request->producto_id);
        if($cont_det <=0 ){
            return redirect('dtefactura')->with([
                'mensaje'=>'Factura sin items, no se actualizó.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        //dd($request->dtedet_id[0]);
        $det_id_array = $request->det_id;
        //dd($det_id_array);
        for ($i=0; $i < count($request->dtedet_id) ; $i++){
            $sql = "SELECT *
                FROM dtedet
                WHERE dte_id = '$id' AND dtedet_id = '" . $request->dtedet_id[$i] . "'" .
                " AND isnull(dtedet.deleted_at);";
            $datadtedet = DB::select($sql);
            if(count($datadtedet) > 0){
                $var = $datadtedet[0]->id;
                $det_id_array[$i] = (string)$var;
            }
        }
        //$auxDet=DteDet::where('dte_id',$id)->whereNotIn('id', $request->det_id)->pluck('id')->toArray(); //->destroy();
        $auxDet=DteDet::where('dte_id',$id)->whereNotIn('id', $det_id_array)->pluck('id')->toArray(); //->destroy();
        for ($i=0; $i < count($auxDet) ; $i++){
            $dtedet = DteDet::findOrFail($auxDet[$i]);
            $aux_dte_id = $dtedet->dtedet->dte_id;
            DteDte::where("dte_id","=",$id)
                    ->where("dter_id","=",$aux_dte_id)->delete(); //Elimino Guias asociadas a la factura
            DteGuiaUsada::where("dte_id","=",$aux_dte_id)->delete(); //Libero las guias de la factura
            DteDet_DespachoOrdDet::where("dtedet_id","=",$auxDet[$i])->delete(); //Elimino la relacion con la orden de despacho y nota de Venta
            DteDet::destroy($auxDet[$i]); //elimino los registros de detalle de factura
        }
        //return 0;
        
        $aux_nrolindet = 0;
        $aux_arrayselgd = explode(",", $request->selectguiadesp);

        foreach ($aux_arrayselgd as &$dter_id){
            //SI no existe en DteDte agrego
            /*
                SI NO EXISTE EN DTEDET AGREGO EL REGISTRO
            */
            $sql = "SELECT *
                FROM dtedte
                WHERE dte_id = '$id' AND dter_id = '$dter_id'
                AND isnull(dtedte.deleted_at);";
            $datadtedte = DB::select($sql);
            if(count($datadtedte) <=0 ){
                $dtedte = new DteDte();
                $dtedte->dte_id = $id;
                $dtedte->dter_id = $dter_id;
                $dtedte->dtefac_id = $id; 
                $dtedte->save();

                $dteguiadesp = Dte::findOrFail($dter_id); //BUSCO LA GUIA ORIGEN
                foreach($dteguiadesp->dtedets as $dtedetguia){ //RECORRO EL DETALLE DE LA GUIA ORIGEN
                    /*
                        SI NO EXISTE EN DTEDET AGREGO EL REGISTRO
                    */
                    $aux_nrolindet++;
                    $dtedet = new DteDet();
                    $dtedet->dte_id = $id;
                    $dtedet->dtedet_id = $dtedetguia->id;
                    $dtedet->producto_id = $dtedetguia->producto_id;
                    $dtedet->nrolindet = $aux_nrolindet;
                    $dtedet->vlrcodigo = $dtedetguia->vlrcodigo;
                    $dtedet->nmbitem = $dtedetguia->nmbitem;
                    $dtedet->dscitem = $dtedetguia->dscitem;
                    $dtedet->qtyitem = $dtedetguia->qtyitem;
                    $dtedet->unmditem = $dtedetguia->unmditem;
                    $dtedet->unidadmedida_id = $dtedetguia->unidadmedida_id;
                    $dtedet->prcitem = $dtedetguia->prcitem;
                    $dtedet->montoitem = $dtedetguia->montoitem;
                    $dtedet->obsdet = $dtedetguia->obsdet;
                    $dtedet->itemkg = $dtedetguia->itemkg;
                    $dtedet->save();
                    $dtedet_id = $dtedet->id;
                    $dtedet_despachoorddet = new DteDet_DespachoOrdDet();
                    $dtedet_despachoorddet->dtedet_id = $dtedet_id;
                    $dtedet_despachoorddet->despachoorddet_id = $dtedetguia->dtedet_despachoorddet->despachoorddet_id;
                    $dtedet_despachoorddet->notaventadetalle_id = $dtedetguia->dtedet_despachoorddet->notaventadetalle_id;
                    $dtedet_despachoorddet->save();
                }
                $dteguiausada = new DteGuiaUsada();
                $dteguiausada->dte_id = $dteguiadesp->id;
                $dteguiausada->usuario_id = auth()->id();
                $dteguiausada->save();
            }
        }
        $Tmntneto = 0;
        $Tiva = 0;
        $Tmnttotal = 0;
        $Tkgtotal = 0;
        $empresa = Empresa::findOrFail(1);

        $sql = "SELECT *
        FROM dtedet
        WHERE dte_id = '$id'
        AND isnull(dtedet.deleted_at);";
        $datadtedet = DB::select($sql);
        for ($i=0; $i < count($datadtedet) ; $i++){
            $Tmntneto += $datadtedet[$i]->montoitem;
            $Tkgtotal += $datadtedet[$i]->itemkg;
            $dtedet = DteDet::findOrFail($datadtedet[$i]->id);
            $dtedet->nrolindet = $i + 1;
            $dtedet->save();
        }
        if($Tmntneto>0){
            $Tiva = round(($empresa->iva/100) * $Tmntneto);
            $Tmnttotal = round((($empresa->iva/100) + 1) * $Tmntneto);
        }

        $centroeconomico = CentroEconomico::findOrFail($request->centroeconomico_id);
        $cliente = Cliente::findOrFail($request->cliente_id);
        $date = str_replace('/', '-', $request->fchemis);

        $request->request->add(['fchemis' => date('Y-m-d')]);

        $hoy = date("Y-m-d H:i:s");

        $dte = Dte::findOrFail($id);
        $dte->tipodespacho = $request->tipodespacho;
        $dte->indtraslado = $request->indtraslado;
        $dte->fechahora = $hoy;
        $dte->mntneto = $Tmntneto;
        $dte->tasaiva = $empresa->iva;
        $dte->iva = $Tiva;
        $dte->mnttotal = $Tmnttotal;
        $dte->kgtotal = $Tkgtotal;
        $dte->obs = $request->obs;
        $dte->vendedor_id = $request->vendedor_id;
        $dte->centroeconomico_id = $request->centroeconomico_id;
        $dte->fchemis = date('Y-m-d');
        $dte->save();

        $dtefac = DteFac::findOrFail($dte->dtefac->id);
        $dtefac->hep = $request->hep;
        $dtefac->formapago_id = $cliente->formapago_id;
        $dtefac->fchvenc =  date('Y-m-d', strtotime(date('Y-m-d') ."+ " . $cliente->plazopago->dias . " days"));
        $dtefac->save();
        
        return redirect('dtefactura')->with('mensaje','Factura actualizada con exito!');
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

    public function listarguiadesp()
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

        return view('dtefactura.listarguiadesp', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }

    public function listarguiadesppage(Request $request){
        $datas = Dte::consultalistarguiadesppage($request);
        //dd($datas);
        return datatables($datas)->toJson();
    }

    public function listardtedet(Request $request){
        $datas = Dte::consultadtedet($request);
        return $datas;
    }

    public function generarfactsii(Request $request)
    {
        if ($request->ajax()) {
            $dte = Dte::findOrFail($request->dte_id);
            if(is_null($dte->cliente->giro) or empty($dte->cliente->giro) or $dte->cliente->giro ==""){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Giro de Cliente no puede estar vacio.',
                    'tipo_alert' => 'error'
                ]);
            }
            if($dte->updated_at != $request->updated_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro fué creado o modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            if(!is_null($dte->statusgen)){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'DTE ya fue Generada! Nro: ' . $dte->nrodocto,
                    'tipo_alert' => 'error'
                ]);
            }
            $foliocontrol = Foliocontrol::where("doc","=","FAVE")->get();
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
            /*
            $ArchivoTXT = dtefactura($dte->id,"12345","XML");
            return $ArchivoTXT;
            */
            //dd("entro");
            $dte->fchemis = date('Y-m-d');
            $dte->save();
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
                            'mensaje' => 'Existe un DTE pendiente por Generar: ' . $aux_guidesp[0]->nrodocto,
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
            $empresa = Empresa::findOrFail(1);
            $soap = new SoapController();
            $aux_folio = $dte->nrodocto;
            //dd($dte->nrodocto);
            if(!is_null($dte->nrodocto)){
                $Estado_DTE = $soap->Estado_DTE($empresa->rut,"33",$aux_folio);
                if($Estado_DTE->Estatus == 3){
                    $bandNoExisteFolio = false;
                    $aux_folio = null;
                    $dte->nrodocto = null;
                    $foliocontrol->bloqueo = 0;
                    $foliocontrol->save();
                }
            }
            //dd($dte->nrodocto);
            if(is_null($dte->nrodocto)){
                $bandNoExisteFolio = true;
                do {
                    $Solicitar_Folio = $soap->Solicitar_Folio($empresa->rut,"33");
                    if(isset($Solicitar_Folio->Estatus)){
                        if($Solicitar_Folio->Estatus == 0){
                            $Estado_DTE = $soap->Estado_DTE($empresa->rut,"33",$Solicitar_Folio->Folio);
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
            $ArchivoTXT = dtefactura($dte->id,$aux_folio,$tipoArch);
            $Carga_TXTDTE = $soap->Carga_TXTDTE($ArchivoTXT,$tipoArch);
            //$Carga_TXTDTE = $soap->Carga_TXTDTE($ArchivoTXT,"XML");
            //dd($Carga_TXTDTE->Estatus);
            if(isset($Carga_TXTDTE->Estatus)){
                //ACTUALIZO EL CAMPO nrodocto
                //SI OCURRIO ALGUN ERROR SE QUE TENGO EL FOLIO, 
                //SE QUE NO LO PUEDO VOLVER A PEDIR PORQUE POR ALGUNA RAZON SE GENERO UN ERROR EN EL ULTIMO FOLIO SOLICITADO
                /*$aux_giadesp = Dte::where('id', $dte->id)
                        ->update(['nrodocto' => $aux_folio]);*/
                if($Carga_TXTDTE->Estatus == 0){
                    $dte->fchemisgen = date("Y-m-d H:i:s");
                    //$date = str_replace('/', '-', $request->fchemis);
                    //$dte->fchemis = date('Y-m-d', strtotime($date));
                    //$dte->fchemis = date("Y-m-d H:i:s");
                    /*
                    $dte->pdf = $Carga_TXTDTE->PDF;
                    $dte->pdfcedible = $Carga_TXTDTE->PDFCedible;
                    $dte->xml = $Carga_TXTDTE->XML;
                    */
                    $dte->nrodocto = $aux_folio;
                    $dte->statusgen = 1;
                    $dte->aprobstatus = 1;
                    $dte->aprobusu_id = auth()->id();
                    $dte->aprobfechahora = date("Y-m-d H:i:s");
        
                    $dte->save();
                    //$fchemisDMY = date("d-m-Y_His",strtotime($dte->fchemis));

                    Storage::disk('public')->put('/facturacion/dte/procesados/fc' . $aux_folio . '.xml', $Carga_TXTDTE->XML);
                    Storage::disk('public')->put('/facturacion/dte/procesados/fc' . $aux_folio . '.pdf', $Carga_TXTDTE->PDF);
                    Storage::disk('public')->put('/facturacion/dte/procesados/fc' . $aux_folio . '_cedible.pdf', $Carga_TXTDTE->PDFCedible);

                    $pdf = new Fpdi();
                    $files = array("storage/facturacion/dte/procesados/fc" . $aux_folio . ".pdf","storage/facturacion/dte/procesados/fc" . $aux_folio . "_cedible.pdf");
                    foreach ($files as $file) {
                        $pageCount = $pdf->setSourceFile($file);
                        for ($pagNo=1; $pagNo <= $pageCount; $pagNo++) { 
                            $template = $pdf->importPage($pagNo);
                            $size = $pdf->getTemplateSize($template);
                            $pdf->AddPage($size['orientation'], $size);
                            $pdf->useTemplate($template);
                        }
                    }
                    $pdf->Output("F","storage/facturacion/dte/procesados/fc" . $aux_folio . "_U.pdf");

                    //dd($Carga_TXTDTE);
                }else{
                    /*
                    $foliocontrol->bloqueo = 0;
                    $foliocontrol->save();
                    */
                    return response()->json([
                        'id' => 0,
                        'mensaje'=>'Error: #' . $Carga_TXTDTE->Estatus . " " . $Carga_TXTDTE->MsgEstatus,
                        'tipo_alert' => 'error'                
                    ]);    
                }
            }else{
                /*
                $foliocontrol->bloqueo = 0;
                $foliocontrol->save();
                */
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Error: ' . $Solicitar_Folio,
                    'tipo_alert' => 'error'                
                ]);
            }

            $dte = Dte::findOrFail($request->dte_id);
            return updatenumfact($dte,$foliocontrol,$request);
        } else {
            abort(404);
        }    
    }

    public function anular(Request $request)
    {
        $request->request->add(['obs' => "Factura anulada."]);
        $request->request->add(['motanul_id' => 5]);
        $request->request->add(['moddevgiadesp_id' => "FC"]);
        return Dte::anulardte($request);

        //PROCESO DE ANULAR DTE SIN HABER ASIGNADO O GENERADO UN NUMERO DE DTE (DOCUMENTO TRIBUTARIO LELECTRONICO)
        //dd($request);
        $dte = Dte::findOrFail($request->dte_id);
        if($request->updated_at != $dte->updated_at){
            return response()->json([
                'id' => 0,
                'mensaje'=>'Registro no puede editado, fué modificado por otro usuario.',
                'tipo_alert' => 'error'
            ]);
        }
        $dte->updated_at = date("Y-m-d H:i:s"); //ACTUALIZO LA FECHA PARA EVITAR QUE OTRO USUARIO HAGA ALGO SOBRE EL REGISTRO MODIFICADO

        //INSERTO UN REGISTRO EN DTE ANULADAS
        $dteanul = new DteAnul();
        $dteanul->dte_id = $request->dte_id;
        if(isset($request->obs)){
            $dteanul->obs = $request->obs;
            $dteanul->motanul_id = $request->motanul_id;
            $dteanul->moddevgiadesp_id = $request->moddevgiadesp_id;

        }else{
            $dteanul->obs = "Factura anulada.";
            $dteanul->motanul_id = 5;
            $dteanul->moddevgiadesp_id = "FC";    
        }
        $dteanul->usuario_id = auth()->id();

        //SI ES FACTURA ELIMINO LOS REGISTROS EN DTEGUIAUSADA
        if($dteanul->moddevgiadesp_id == "FC"){
            foreach ($dte->dtedtes as $dtedte) {
                //DteDte::destroy($dtedte->id); //ELIMINO LAS GUIAS ASOCIADAS A LA FACTURA
                DteGuiaUsada::destroy($dtedte->dter->dteguiausada->id); //ELIMINO LAS GUIAS USADAS POR LA FACTURA
            }    
        }
        if($dte->save() and $dteanul->save()){
            return response()->json([
                'id' => 1,
                'mensaje'=>'Registro procesado con exito.',
                'tipo_alert' => 'success'
            ]);
        }else{
            return response()->json([
                'id' => 0,
                'mensaje'=>'Ocurrio un error al intentan Guardar el registro.',
                'tipo_alert' => 'error'
            ]);
        }
    }

    public function buscarfactura(Request $request)
    {  
        if($request->ajax()){
            $respuesta = array();
            $sql = "SELECT dte.id as dte_id,dte.fchemis,dte.fechahora,dte.centroeconomico_id,dte.vendedor_id,
            dte.obs,dte.indtraslado,dte.updated_at,dtefac.hep,dtefac.formapago_id,dtefac.fchvenc,
            cliente.id as cliente_id,cliente.rut,cliente.razonsocial,
            cliente.telefono,cliente.email,cliente.direccion,cliente.contactonombre,
            cliente.formapago_id,cliente.plazopago_id,cliente.giro_id,cliente.giro,cliente.regionp_id,
            cliente.provinciap_id,cliente.comunap_id,
            clientebloqueado.descripcion,comuna.nombre as comuna_nombre,provincia.nombre as provincia_nombre,
            formapago.descripcion as formapago_desc,plazopago.dias as plazopago_dias
            FROM dte INNER JOIN cliente
            ON dte.cliente_id  = cliente.id AND ISNULL(dte.deleted_at) AND ISNULL(cliente.deleted_at)
            INNER JOIN dtefac
            on dte.id = dtefac.dte_id
            LEFT JOIN clientebloqueado
            ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
            left join comuna
            ON cliente.comunap_id=comuna.id and isnull(comuna.deleted_at)
            left join provincia
            ON cliente.provinciap_id=provincia.id and isnull(provincia.deleted_at)
            INNER JOIN formapago
            ON  cliente.formapago_id = formapago.id and isnull(formapago.deleted_at)
            INNER JOIN plazopago
            ON  cliente.plazopago_id = plazopago.id and isnull(plazopago.deleted_at)
            WHERE dte.foliocontrol_id=1 
            AND dte.nrodocto = $request->nrodocto
            AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
            AND dte.statusgen = 1
            ORDER BY dte.id desc;";
            $dte = DB::select($sql);
            $respuesta['dte'] = $dte;
            //dd($dte[0]->id);
            if(count($dte) > 0){
                $sql = "SELECT dtedet.id,dtedet.dte_id,dtedet.nrolindet,dtedet.producto_id,dtedet.nmbitem,
                dtedet.qtyitem,dtedet.unmditem,dtedet.unidadmedida_id,dtedet.prcitem,dtedet.montoitem,dtedet.obsdet,
                dtedet.itemkg
                FROM dtedet
                WHERE dtedet.dte_id = " . $dte[0]->dte_id .
                " ORDER BY dtedet.nrolindet;";


                $dtedetfact = DB::select($sql);
                $respuesta['dtedetfact'] = $dtedetfact;
                $dtefact = Dte::findOrFail($dte[0]->dte_id);
                $dtefactdets = $dtefact->dtedets;
                foreach ($dtefact->dtedtefacasosiadas as $dtedtefacasosiada) {
                    //BUSCO TODOS LOS DTE RELACIONADOS A LA FACTURA EXCLUYENDO LA FACTURA ORIGINAL
                    if($dtedtefacasosiada->dte_id != $dtefact->id){ //EXCLUYO LA MISMA FACTURA
                        //ME UBICO EN EL DTE RELACIONADO
                        $dteNCND = Dte::findOrFail($dtedtefacasosiada->dte_id);
                        if(is_null($dteNCND->dteanul)){
                            $operador = 1;
                            if($dteNCND->foliocontrol_id == 5){
                                $operador = -1;
                            }
                            foreach ($dteNCND->dtedets as $dteNCNDdet) {
                                for ($i=0; $i < count($dtefactdets); $i++) { 
                                    if($dteNCNDdet->producto_id == $dtefactdets[$i]->producto_id){
                                        $dtefactdets[$i]->qtyitem += ($dteNCNDdet->qtyitem * $operador);
                                        $dtefactdets[$i]->montoitem += ($dteNCNDdet->montoitem * $operador);
                                        if($dtefactdets[$i]->montoitem <= 0){ //SI EL REGISTRO QUEDO EN CERO 0 LO ELIMINO DE LO QUE ENVIO
                                            unset($dtefactdets[$i]);
                                        }else{
                                            if($dtefactdets[$i]->qtyitem <= 0){
                                                $dtefactdets[$i]->qtyitem = 1;
                                                $dtefactdets[$i]->prcitem = $dtefactdets[$i]->montoitem;
                                            }    
                                        }
                                    }
                                }
                                //dd($i);
                            }
                        }
                    }
                }
                $respuesta['dtefacdet'] = $dtefactdets->toArray();
            }
            return $respuesta;
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


    $sql = "SELECT dte.id,dte.fechahora,cliente.rut,cliente.razonsocial,comuna.nombre as nombre_comuna,
    clientebloqueado.descripcion as clientebloqueado_descripcion,
    GROUP_CONCAT(DISTINCT dtedte.dter_id) AS dter_id,
    GROUP_CONCAT(DISTINCT notaventa.cotizacion_id) AS cotizacion_id,
    GROUP_CONCAT(DISTINCT notaventa.oc_id) AS oc_id,
    GROUP_CONCAT(DISTINCT notaventa.oc_file) AS oc_file,
    GROUP_CONCAT(DISTINCT dteguiadesp.notaventa_id) AS notaventa_id,
    GROUP_CONCAT(DISTINCT despachoord.despachosol_id) AS despachosol_id,
    GROUP_CONCAT(DISTINCT dteguiadesp.despachoord_id) AS despachoord_id,
    (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) 
    FROM dte AS dte1 INNER JOIN dtedte AS dtedte1
    ON dte1.id = dtedte1.dter_id AND ISNULL(dte1.deleted_at) and isnull(dtedte1.deleted_at)
    WHERE dtedte1.dte_id = dte.id
    GROUP BY dtedte1.dte_id) AS nrodocto_guiadesp,
    dte.updated_at
    FROM dte INNER JOIN dtedte
    ON dte.id = dtedte.dte_id AND ISNULL(dte.deleted_at) and isnull(dtedte.deleted_at)
    INNER JOIN dteguiadesp
    ON dtedte.dter_id = dteguiadesp.dte_id
    INNER JOIN despachoord
    ON despachoord.id = dteguiadesp.despachoord_id
    INNER JOIN notaventa
    ON notaventa.id = despachoord.notaventa_id
    INNER JOIN cliente
    ON dte.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = cliente.comunap_id
    LEFT JOIN clientebloqueado
    ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    WHERE dte.foliocontrol_id=1 
    AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
    AND dte.sucursal_id IN ($sucurcadena)
    AND ISNULL(dte.statusgen)
    AND $aux_conddte_id
    GROUP BY dte.id
    ORDER BY dte.id desc;";

    return DB::select($sql);
}


function dtefactura($id,$Folio,$tipoArch){
    $aux_dte = consultaindex($id);
    $Folio = str_pad($Folio, 10, "0", STR_PAD_LEFT);
    $dte = Dte::findOrFail($id);
    $rutrecep = $dte->cliente->rut;
    $rutrecep = number_format( substr ( $rutrecep, 0 , -1 ) , 0, "", "") . '-' . substr ( $rutrecep, strlen($rutrecep) -1 , 1 );

    $empresa = Empresa::findOrFail(1);
    $RznSoc = strtoupper(substr(trim($empresa->razonsocial),0,100));
    $GiroEmis = strtoupper(substr(trim($empresa->giro),0,80));
    $Acteco = substr(trim($empresa->acteco),0,6);
    $DirOrigen = strtoupper(substr(trim($empresa->sucursal->direccion),0,60));
    $CmnaOrigen = strtoupper(substr(trim($empresa->sucursal->comuna->nombre),0,20));
    $CiudadOrigen = strtoupper(substr(trim($empresa->sucursal->comuna->provincia->nombre),0,20));
    $contacto = strtoupper(substr(trim($dte->cliente->contactonombre . " Telf:" . $dte->cliente->contactotelef),0,80));
    $CorreoRecep = strtoupper(substr(trim($dte->cliente->contactoemail),0,80));
    $RznSocRecep = strtoupper(substr(trim($dte->cliente->razonsocial),0,100));
    $GiroRecep = strtoupper(substr(trim($dte->cliente->giro),0,42));
    $DirRecep = strtoupper(substr(trim($dte->cliente->direccion),0,70));
    $CmnaRecep = strtoupper(substr(trim($dte->cliente->comuna->nombre),0,20));
    $CiudadRecep = strtoupper(substr(trim($dte->cliente->provincia->nombre),0,20));
    $formapago_desc = $dte->dtefac->formapago->descripcion;
    $FchVenc = $dte->dtefac->fchvenc;
    if($dte->dtefac->formapago_id == 2 or $dte->dtefac->formapago_id == 3){
        $formapago_desc .= " " . $dte->cliente->plazopago->descripcion;
    }
    $FolioRef = substr(trim($dte->oc_id),0,20);
    $contenido = "";
/*
    if($tipoArch == "TXT"){
        $fchemisDMY = date("d-m-Y_His",strtotime($dte->fchemis));
        $fchemis = date("d-m-Y",strtotime($dte->fchemis)); // date("Y-m-d");
        $contenido = "ENC|33||$Folio|$fchemis||$dte->tipodespacho|$dte->indtraslado|||||||||||$fchemis|" . 
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
    */

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
        "<TipoDTE>33</TipoDTE>" .
        "<Folio>$Folio</Folio>" .
        "<FchEmis>$FchEmis</FchEmis>" .
        "<TipoDespacho>$dte->tipodespacho</TipoDespacho>" .
        "<TpoImpresion>N</TpoImpresion>" .
        "<TermPagoGlosa>$formapago_desc</TermPagoGlosa>" .
        "<FchVenc>$FchVenc</FchVenc>" .
        "</IdDoc>" .
        "<Emisor>" .
        "<RUTEmisor>$empresa->rut</RUTEmisor>" .
        "<RznSoc>$RznSoc</RznSoc>" .
        "<GiroEmis>$GiroEmis</GiroEmis>" .
        "<Acteco>$Acteco</Acteco>" .
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
            $NmbItem = strtoupper(substr(trim($dtedet->nmbitem),0,80));
            $DscItem = strtoupper(trim($dtedet->dscitem));
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
    
        $TpoDocRef = (empty($dte->dteguiadesp->despachoord_id) ? "" : "OD:" . $dte->dteguiadesp->despachoord_id . " ") . (empty($dte->ot) ? "" : "OT:" . $dte->ot . " ")  . (empty($dte->obs) ? "" : $dte->obs . " ") . (empty($dte->lugarentrega) ? "" : $dte->lugarentrega . " ")  . (empty($dte->comunaentrega_id) ? "" : $dte->comunaentrega->nombre . " ");
        $TpoDocRef = sanear_string(strtoupper(substr(trim($TpoDocRef),0,90)));

        //dd($aux_dte[0]->oc_id);


    
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

        $array_ocs = explode(",", $aux_dte[0]->oc_id);
        $i = 2;
        $aux_RazonRef = $dte->dtefac->hep ? ("Hep: " . $dte->dtefac->hep) : "";;
        $aux_RazonRefImp = false;
        foreach ($array_ocs as $oc_id) {
            $i++;
            $contenido .= "<Referencia>" .
            "<NroLinRef>$i</NroLinRef>" .
            "<TpoDocRef>801</TpoDocRef>" .
            "<FolioRef>$oc_id</FolioRef>" .
            "<FchRef>$FchEmis</FchRef>";
            if($aux_RazonRefImp == false){
                $aux_RazonRefImp = true;
                $contenido .= "<RazonRef>$aux_RazonRef</RazonRef>";
            }
            $contenido .= "</Referencia>";
        }

        $array_dter_id = explode(",", $aux_dte[0]->dter_id);
        foreach ($array_dter_id as $dter_id) {
            $i++;
            $dtedg = Dte::findOrFail($dter_id);
            $contenido .= "<Referencia>" .
            "<NroLinRef>$i</NroLinRef>" .
            "<TpoDocRef>52</TpoDocRef>" .
            "<FolioRef>$dtedg->nrodocto</FolioRef>" .
            "<FchRef>$dtedg->fchemis</FchRef>";
            if($aux_RazonRefImp == false){
                $aux_RazonRefImp = true;
                $contenido .= "<RazonRef>$aux_RazonRef</RazonRef>";
            }
            $contenido .= "</Referencia>";
        }
        $contenido .= "</Documento>" .
        "</DTE>";
    }
    /*
    echo $contenido;
    dd('e');
    */
    return $contenido;
}

function updatenumfact($dte,$foliocontrol,$request){
    $foliocontrol->ultfoliouti = $dte->nrodocto;
    $foliocontrol->bloqueo = 0;    
    if ($dte->save() and $foliocontrol->save()) {
        return response()->json([
                                'mensaje' => 'ok',
                                'status' => '0',
                                'id' => $request->id,
                                'nfila' => $request->nfila,
                                'nrodocto' => $dte->nrodocto
                                ]);
    } else {
        return response()->json(['mensaje' => 'ng']);
    }
}
/*
function updatenumfact($despachoord,$dte,$foliocontrol,$request){
    $foliocontrol->ultfoliouti = $dte->nrodocto;
    $foliocontrol->bloqueo = 0;
    
    $despachoord->guiadespacho = $dte->nrodocto;
    $despachoord->guiadespachofec = ($dte->fchemis . " 00:00:00");
    if ($despachoord->save() and $dte->save() and $foliocontrol->save()) {
        //dteguiadesp($dte->id);
        Event(new GuardarGuiaDespacho($despachoord));
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
*/