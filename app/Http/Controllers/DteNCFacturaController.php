<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarDTE;
use App\Http\Requests\ValidarDTEFac;
use App\Http\Requests\ValidarDTENC;
use App\Models\CentroEconomico;
use App\Models\Cliente;
use App\Models\Dte;
use App\Models\DteDet;
use App\Models\DteDte;
use App\Models\DteNcNd;
use App\Models\Empresa;
use App\Models\Foliocontrol;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\UnidadMedida;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DteNCFacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-nota-credito-factura');
        return view('dtencfactura.index');
    }

    public function dtencfacturapage($dte_id = ""){
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
        can('crear-nota-credito-factura');
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedidas'] = UnidadMedida::orderBy('id')->get();
        $centroeconomicos = CentroEconomico::orderBy('id')->get();

        //dd($tablas);
        return view('dtencfactura.crear',compact('tablas','centroeconomicos'));

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarDTENC $request)
    {
        can('guardar-nota-credito-factura');
        //dd($request);
        $dtefac = Dte::findOrFail($request->dte_id);
        if($dtefac->updated_at != $request->updated_at){
            return redirect('dtencfactura')->with([
                'mensaje'=>'Registro fué modificado por otro usuario.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        //Actualizo updated_at
        $dtefac->updated_at = date("Y-m-d H:i:s");
        $dtefac->save();

        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            return redirect('dtencfactura')->with([
                'mensaje'=>'Sin items para proceras, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $dte = new Dte();
        $Tmntneto = 0;
        $Tiva = 0;
        $Tmnttotal = 0;
        $Tkgtotal = 0;
        //dd($request);
        for ($i=0; $i < $cont_producto ; $i++){
            if(is_null($request->producto_id[$i])==false AND (is_null($request->qtyitem[$i])==false) OR $request->codref == 2){
                //$producto = Producto::findOrFail($request->producto_id[$i]);
                $dtedet = new DteDet();
                $dtedet->dtedet_id = $request->dtedetorigen_id[$i];
                $dtedet->producto_id = $request->vlrcodigo[$i];
                $dtedet->nrolindet = ($i + 1);
                $dtedet->vlrcodigo = $request->vlrcodigo[$i];
                $dtedet->nmbitem = $request->nmbitem[$i];
                //$dtedet->dscitem = $request->dscitem[$i]; este valor aun no lo uso
                $dtedet->qtyitem = $request->qtyitem[$i];
                $dtedet->unidadmedida_id = $request->unmditemselect[$i];
                $dtedet->unmditem = substr($dtedet->unidadmedida->nombre, 0, 4) ; //$request->unmditem[$i];
                $dtedet->prcitem = $request->prcitem[$i]; //$request->montoitem[$i]/$request->qtyitem[$i]; //$request->prcitem[$i];
                $dtedet->montoitem = round($dtedet->qtyitem * $dtedet->prcitem,0); //$request->montoitem[$i];
                //$dtedet->obsdet = $request->obsdet[$i];
                $dtedetorigen = DteDet::findOrFail($request->dtedetorigen_id[$i]);
                if(is_numeric($dtedetorigen->itemkg) and $dtedetorigen->itemkg > 0){
                    $aux_itemkg = ($dtedetorigen->itemkg / $dtedetorigen->qtyitem) * $request->qtyitem[$i];
                }else{
                    $aux_itemkg = 0;    
                }
                //$aux_itemkg = is_numeric($request->itemkg[$i]) ? $request->itemkg[$i] : 0;
                $dtedet->itemkg = $aux_itemkg;
                //$dtedet->save();
                $dte->dtedets[] = $dtedet;
                $dtedet_id = $dtedet->id;

                $Tmntneto += $dtedet->montoitem; //$request->montoitem[$i];
                $Tkgtotal += $aux_itemkg;
            }
        }
        //dd($dte);
        $empresa = Empresa::findOrFail(1);
        if($Tmntneto>0){
            $Tiva = round(($empresa->iva/100) * $Tmntneto);
            $Tmnttotal = round((($empresa->iva/100) + 1) * $Tmntneto);    
        }

        $centroeconomico = CentroEconomico::findOrFail($request->centroeconomico_id);

        $hoy = date("Y-m-d H:i:s");
        $dte->foliocontrol_id = 5;
        $dte->nrodocto = "";
        $dte->fchemis = date("Y-m-d");
        $dte->fchemisgen = $hoy;
        $dte->fechahora = $hoy;
        $dte->sucursal_id = $centroeconomico->sucursal_id;
        $dte->cliente_id = $dtefac->cliente_id;
        $dte->comuna_id = $dtefac->comuna_id;
        $dte->vendedor_id = $dtefac->vendedor_id;
        $dte->obs = $request->obs;
        $dte->tipodespacho = $dtefac->tipodespacho;
        $dte->indtraslado = $dtefac->indtraslado;
        $dte->mntneto = $Tmntneto;
        $dte->tasaiva = $dtefac->tasaiva;
        $dte->iva = $Tiva;
        $dte->mnttotal = $Tmnttotal;
        $dte->kgtotal = $Tkgtotal;
        $dte->centroeconomico_id = $request->centroeconomico_id;
        $dte->usuario_id = auth()->id();

        $arrayDTE = Dte::busDTEOrig($request->dte_id);
        $dtedte = new DteDte();
        $dtedte->dte_id = "";
        $dtedte->dter_id = $request->dte_id;
        $dtedte->dtefac_id = $arrayDTE["dtefac_id"];
        $dte->dtedte = $dtedte;

        $dtencnd = new DteNcNd();
        $dtencnd->dte_id = "";
        $dtencnd->codref = $request->codref;
        $dte->dtencnd = $dtencnd;

        $respuesta = Dte::generardteprueba($dte);
        /*
        $respuesta = response()->json([
            'id' => 1
        ]);
        */
        //dd("");
        $foliocontrol = Foliocontrol::findOrFail($dte->foliocontrol_id);
        if($respuesta->original["id"] == 1){
            $dteNew = Dte::create($dte->toArray());
            foreach ($dte->dtedets as $dtedet) {
                //dd($dtedet->toArray());
                $dtedet->dte_id = $dteNew->id;
                DteDet::create($dtedet->toArray());
            }
            $dtedte->dte_id = $dteNew->id;
            $dtedte->save();
            $dtencnd->dte_id = $dteNew->id;
            $dtencnd->save();
            $foliocontrol->bloqueo = 0;
            $foliocontrol->ultfoliouti = $dteNew->nrodocto;
            $foliocontrol->save();
            return redirect('dtencfactura')->with([
                'mensaje'=>'Nota de Debito creada con exito.',
                'tipo_alert' => 'alert-success'
            ]);    
        }else{
            $foliocontrol->bloqueo = 0;
            $foliocontrol->save();
            return redirect('dtencfactura')->with([
                'mensaje'=>$respuesta->original["mensaje"] ,
                'tipo_alert' => 'alert-error'
            ]);
        }

        $dte = Dte::create($request->all());
        $dte_id = $dte->id;

        $arrayDTE = Dte::busDTEOrig($request->dte_id);
        $dtedte = new DteDte();
        $dtedte->dte_id = $dte_id;
        $dtedte->dter_id = $request->dte_id;
        $dtedte->dtefac_id = $arrayDTE["dtefac_id"];
        $dtedte->save();

        $dtencnd = new DteNcNd();
        $dtencnd->dte_id = $dte_id;
        $dtencnd->codref = $request->codref;
        $dtencnd->save();

        $Tmntneto = 0;
        $Tiva = 0;
        $Tmnttotal = 0;
        $Tkgtotal = 0;

        for ($i=0; $i < $cont_producto ; $i++){
            if(is_null($request->producto_id[$i])==false AND (is_null($request->qtyitem[$i])==false) OR $request->codref == 2){
                //$producto = Producto::findOrFail($request->producto_id[$i]);
                $dtedet = new DteDet();
                $dtedet->dte_id = $dte_id;
                $dtedet->dtedet_id = $request->dtedetorigen_id[$i];
                $dtedet->producto_id = $request->producto_id[$i];
                $dtedet->nrolindet = ($i + 1);
                $dtedet->vlrcodigo = $request->producto_id[$i];
                $dtedet->nmbitem = $request->nmbitem[$i];
                //$dtedet->dscitem = $request->dscitem[$i]; este valor aun no lo uso
                $dtedet->qtyitem = $request->qtyitem[$i];
                $dtedet->unmditem = $request->unmditem[$i];
                $dtedet->unidadmedida_id = $request->unidadmedida_id[$i];
                $dtedet->prcitem = $request->prcitem[$i]; //$request->montoitem[$i]/$request->qtyitem[$i]; //$request->prcitem[$i];
                $dtedet->montoitem = round($request->montoitem[$i],0);
                //$dtedet->obsdet = $request->obsdet[$i];
                $dtedetorigen = DteDet::findOrFail($request->dtedetorigen_id[$i]);
                if(is_numeric($dtedetorigen->itemkg) and $dtedetorigen->itemkg > 0){
                    $aux_itemkg = ($dtedetorigen->itemkg / $dtedetorigen->qtyitem) * $request->qtyitem[$i];
                }else{
                    $aux_itemkg = 0;    
                }
                //$dtedet->itemkg = $request->itemkg[$i];
                $dtedet->itemkg = $aux_itemkg;
                $dtedet->save();
                $dtedet_id = $dtedet->id;

                $Tmntneto += $request->montoitem[$i];
                $Tkgtotal += $request->itemkg[$i];
            }
        }
        $empresa = Empresa::findOrFail(1);
        if($Tmntneto>0){
            $Tiva = round(($empresa->iva/100) * $Tmntneto);
            $Tmnttotal = round((($empresa->iva/100) + 1) * $Tmntneto);    
        }
        $dte = Dte::findOrFail($dte_id);
        $dte->tipodespacho = $dtefac->tipodespacho;
        $dte->indtraslado = $dtefac->indtraslado;
        $dte->mntneto = $Tmntneto;
        $dte->tasaiva = $empresa->iva;
        $dte->iva = $Tiva;
        $dte->mnttotal = $Tmnttotal;
        $dte->kgtotal = $Tkgtotal;
        $dte->save();

        $aux_foliosdisp = $foliocontrol->ultfoliohab - $foliocontrol->ultfoliouti;
        if($aux_foliosdisp <=20){
            return redirect('dtencfactura')->with([
                'mensaje'=>"Nota de Credito creada con exito. Quedan $aux_foliosdisp folios disponibles!" ,
                'tipo_alert' => 'alert-error'
            ]);
        }else{
            return redirect('dtencfactura')->with([
                'mensaje'=>'Nota de Credito creada con exito.',
                'tipo_alert' => 'alert-success'
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

    public function consdte_dtedet(Request $request){
        $request->request->add(['foliocontrol_id' => "5"]);
        $request->request->add(['condFoliocontrol' => $request->tdfoliocontrol_id]);
        $request->request->add(['TipoDTE' => "NC"]);
        $respuesta = Dte::consdte_dtedet($request);
        return $respuesta;
    }

    public function procesar(Request $request){
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

    //ANULAR DTE
    public function anular(Request $request)
    {
        $request->request->add(['obs' => "Nota Crédito anulada."]);
        $request->request->add(['motanul_id' => 5]);
        $request->request->add(['moddevgiadesp_id' => "NC"]);
        return Dte::anulardte($request);
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

    $sql = "SELECT dte.id,dte.nrodocto,dte.fechahora,cliente.rut,cliente.razonsocial,comuna.nombre as nombre_comuna,
    clientebloqueado.descripcion as clientebloqueado_descripcion,
    dte.updated_at,0 as dtefac_id
    FROM dte INNER JOIN cliente
    ON dte.cliente_id  = cliente.id AND ISNULL(dte.deleted_at) AND ISNULL(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = cliente.comunap_id AND ISNULL(comuna.deleted_at)
    LEFT JOIN clientebloqueado
    ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    WHERE dte.foliocontrol_id = 5 
    AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
    AND ISNULL(dte.statusgen)
    AND dte.sucursal_id IN ($sucurcadena)
    AND $aux_conddte_id
    AND !ISNULL(dte.nrodocto)
    GROUP BY dte.id
    ORDER BY dte.id desc;";

    $dteDocs = DB::select($sql);
    //dd($dteDocs);
    for ($i = 0; $i < count($dteDocs);$i++) {
        $aux_dteId = $dteDocs[$i]->id;
        do{
            $dte = Dte::findOrFail($aux_dteId);
            $aux_dteId = $dte->dtedte ? $dte->dtedte->dter_id : "";
            if($dte->foliocontrol_id == 1 or $dte->foliocontrol_id == 7){
                //dd($dte->foliocontrol_id);
                $dteDocs[$i]->dtefac_id = $dte->id;
                $dteDocs[$i]->dte_id = $dte->id;
                $dteDocs[$i]->dter_id = $aux_dteId;
                $dteDocs[$i]->nrodocto_factura = $dte->nrodocto;
                break;
            }
        }while ($dte->dtedte and ($dte->foliocontrol_id != 1 or $dte->foliocontrol_id != 7));
        //dd($dteDocs);
        if($dteDocs[$i]->dte_id){
            //dd($dte->id);
            $sql = "SELECT dte.id,dte.nrodocto AS nrodocto_fac,
                        GROUP_CONCAT(DISTINCT notaventa.cotizacion_id) AS cotizacion_id,
                        GROUP_CONCAT(DISTINCT notaventa.oc_id) AS oc_id,
                        GROUP_CONCAT(DISTINCT notaventa.oc_file) AS oc_file,
                        GROUP_CONCAT(DISTINCT dteguiadesp.notaventa_id) AS notaventa_id,
                        GROUP_CONCAT(DISTINCT despachoord.despachosol_id) AS despachosol_id,
                        GROUP_CONCAT(DISTINCT dteguiadesp.despachoord_id) AS despachoord_id,
                        GROUP_CONCAT(DISTINCT dtedte.dter_id) AS dter_id,
                        GROUP_CONCAT(DISTINCT dter.nrodocto) AS nrodocto_guiadesp,
                        foliocontrol.nombrepdf,foliocontrol.tipodocto
                        FROM dte LEFT JOIN dtedte
                        ON dte.id = dtedte.dte_id AND ISNULL(dte.deleted_at) and isnull(dtedte.deleted_at)
                        LEFT JOIN dte AS dter
                        ON dter.id = dtedte.dter_id AND ISNULL(dter.deleted_at)
                        LEFT JOIN dteguiadesp
                        ON dteguiadesp.dte_id = dter.id AND ISNULL(dteguiadesp.deleted_at)
                        LEFT JOIN despachoord
                        ON despachoord.id = dteguiadesp.despachoord_id AND ISNULL(despachoord.deleted_at)
                        LEFT JOIN notaventa
                        ON notaventa.id = despachoord.notaventa_id AND ISNULL(notaventa.deleted_at)
                        INNER JOIN foliocontrol
                        ON foliocontrol.id = dte.foliocontrol_id AND ISNULL(foliocontrol.deleted_at)
                        WHERE dte.id = $dte->id
                        GROUP BY dte.id;";
            $guia = DB::select($sql);
            if(count($guia) > 0){
                $dteDocs[$i]->nrodocto_fac = $guia[0]->nrodocto_fac;
                $dteDocs[$i]->cotizacion_id = $guia[0]->cotizacion_id ? $guia[0]->cotizacion_id : "";
                $dteDocs[$i]->oc_id = $guia[0]->oc_id;
                $dteDocs[$i]->oc_file = $guia[0]->oc_file;
                $dteDocs[$i]->notaventa_id = $guia[0]->notaventa_id ? $guia[0]->notaventa_id : "";
                $dteDocs[$i]->despachosol_id = $guia[0]->despachosol_id ? $guia[0]->despachosol_id : "";
                $dteDocs[$i]->despachoord_id = $guia[0]->despachoord_id ? $guia[0]->despachoord_id : "";
                $dteDocs[$i]->dter_id = $guia[0]->dter_id ? $guia[0]->dter_id : "";
                $dteDocs[$i]->nombrepdf = $guia[0]->nombrepdf;
                $dteDocs[$i]->nrodocto_guiadesp = $guia[0]->nrodocto_guiadesp ? $guia[0]->nrodocto_guiadesp : "";
                //dd($dteDocs[$i]);    
            }else{
                $dteDocs[$i]->nrodocto_fac = "";
                $dteDocs[$i]->cotizacion_id = "";
                $dteDocs[$i]->oc_id = "";
                $dteDocs[$i]->oc_file = "";
                $dteDocs[$i]->notaventa_id = "";
                $dteDocs[$i]->despachosol_id = "";
                $dteDocs[$i]->despachoord_id = "";
                $dteDocs[$i]->dter_id = "";
                $dteDocs[$i]->nombrepdf = "";
                $dteDocs[$i]->nrodocto_guiadesp = "";
            }
            $nrodocto_guiadesp = 0;
        }
    }
    return $dteDocs;

    $sql = "SELECT dte.id,dte.fechahora,cliente.rut,cliente.razonsocial,comuna.nombre as nombre_comuna,
    clientebloqueado.descripcion as clientebloqueado_descripcion,
    GROUP_CONCAT(DISTINCT dtedtenc.dter_id) AS dter_id,
    GROUP_CONCAT(DISTINCT notaventa.cotizacion_id) AS cotizacion_id,
    GROUP_CONCAT(DISTINCT notaventa.oc_id) AS oc_id,
    GROUP_CONCAT(DISTINCT notaventa.oc_file) AS oc_file,
    GROUP_CONCAT(DISTINCT dteguiadesp.notaventa_id) AS notaventa_id,
    GROUP_CONCAT(DISTINCT despachoord.despachosol_id) AS despachosol_id,
    GROUP_CONCAT(DISTINCT dteguiadesp.despachoord_id) AS despachoord_id,
    (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) 
    FROM dte AS dte1
    where dte1.id = dtedtefac.dter_id
    GROUP BY dte1.id) AS nrodocto_guiadesp,
    (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) AS nrodocto
    FROM dte AS dte1
    where dte1.id = dtedtenc.dtefac_id
    GROUP BY dte1.id) AS nrodocto_factura,
    dte.updated_at
    FROM dte INNER JOIN dtedte as dtedtenc
    ON dte.id = dtedtenc.dte_id AND ISNULL(dte.deleted_at) and isnull(dtedtenc.deleted_at)
    INNER JOIN dtefac
    ON dtedtenc.dtefac_id = dtefac.dte_id
    INNER JOIN dtedte as dtedtefac
    on dtedtefac.dte_id = dtedtenc.dtefac_id
    INNER JOIN dteguiadesp
    ON dtedtefac.dter_id = dteguiadesp.dte_id
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
    WHERE dte.foliocontrol_id=5 
    AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
    AND ISNULL(dte.statusgen)
    AND dte.sucursal_id IN ($sucurcadena)
    AND $aux_conddte_id
    GROUP BY dte.id
    ORDER BY dte.id desc;";
    //dd($sql);
    return DB::select($sql);
}