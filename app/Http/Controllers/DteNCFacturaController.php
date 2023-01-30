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
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
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

        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            return redirect('dtencfactura')->with([
                'mensaje'=>'Sin items para proceras, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $centroeconomico = CentroEconomico::findOrFail($request->centroeconomico_id);
        //$cliente = Cliente::findOrFail($request->cliente_id);
        $request->request->add(['fchemis' => date('Y-m-d')]);

        $request->request->add(['cliente_id' => $dtefac->cliente_id]);
        $request->request->add(['tipodespacho' => $dtefac->tipodespacho]);
        $request->request->add(['fechahora' => date("Y-m-d H:i:s")]);
        //$request->request->add(['tasaiva' => $dteguiadesp->iva]);
        $request->request->add(['sucursal_id' => $centroeconomico->sucursal_id]);
        $request->request->add(['comuna_id' => $dtefac->comuna_id]);
        $request->request->add(['foliocontrol_id' => 5]); //CODIGO DE TIPO DE DTE 5=NOTA CREDITO
        $request->request->add(['usuario_id' => auth()->id()]);
        

        $dte = Dte::create($request->all());
        $dte_id = $dte->id;

        $dtedte = new DteDte();
        $dtedte->dte_id = $dte_id;
        $dtedte->dter_id = $request->dte_id;
        $dtedte->dtefac_id = $request->dte_id; 
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
            if(is_null($request->producto_id[$i])==false && is_null($request->qtyitem[$i])==false){
                //$producto = Producto::findOrFail($request->producto_id[$i]);
                $dtedet = new DteDet();
                $dtedet->dte_id = $dte_id;
                $dtedet->dtedet_id = $request->dtedetorigen_id[$i];
                $dtedet->producto_id = $request->producto_id[$i];
                $dtedet->nrolindet = ($i + 1);
                $dtedet->vlrcodigo = $request->producto_id[$i];
                $dtedet->nmbitem = $request->nmbitem[$i];
                $dtedet->dscitem = $request->dscitem[$i];
                $dtedet->qtyitem = $request->qtyitem[$i];
                $dtedet->unmditem = $request->unmditem[$i];
                $dtedet->unidadmedida_id = $request->unidadmedida_id[$i];
                $dtedet->prcitem = $request->prcitem[$i]; //$request->montoitem[$i]/$request->qtyitem[$i]; //$request->prcitem[$i];
                $dtedet->montoitem = $request->montoitem[$i];
                $dtedet->obsdet = $request->obsdet[$i];
                $dtedet->itemkg = $request->itemkg[$i];
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

        return redirect('dtencfactura')->with([
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

    public function generardtesii(Request $request){
        $request->request->add(['foliocontrol_id' => "5"]);
        $aux_respuesta = Dte::generardte($request);
        return $aux_respuesta;
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

    $sql = "SELECT dte.id,dte.fechahora,cliente.rut,cliente.razonsocial,comuna.nombre as nombre_comuna,
    clientebloqueado.descripcion as clientebloqueado_descripcion,
    dte.updated_at,0 as dtefac_id
    FROM dte INNER JOIN cliente
    ON dte.cliente_id  = cliente.id AND ISNULL(dte.deleted_at) AND ISNULL(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = cliente.comunap_id AND ISNULL(comuna.deleted_at)
    LEFT JOIN clientebloqueado
    ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    WHERE dte.foliocontrol_id=5 
    AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
    AND ISNULL(dte.statusgen)
    AND dte.sucursal_id IN ($sucurcadena)
    AND $aux_conddte_id
    GROUP BY dte.id
    ORDER BY dte.id desc;";

    $dteDocs = DB::select($sql);
    for ($i = 0; $i < count($dteDocs);$i++) {
        $aux_dteId = $dteDocs[$i]->id;
        do{
            $dte = Dte::findOrFail($aux_dteId);
            $aux_dteId = $dte->dtedte ? $dte->dtedte->dter_id : "";
            if($dte->foliocontrol_id == 1){
                $dteDocs[$i]->dtefac_id = $dte->id;
                $dteDocs[$i]->dte_id = $dte->id;
                $dteDocs[$i]->dter_id = $aux_dteId;
                $dteDocs[$i]->nrodocto_factura = $dte->nrodocto;
                break;
            }
        }while ($dte->dtedte and $dte->foliocontrol_id != 1);
        //dd($dteDocs);
        if($dteDocs[$i]->dte_id){
            $sql = "SELECT dte.id,dte.nrodocto AS nrodocto_fac,
                        GROUP_CONCAT(DISTINCT notaventa.cotizacion_id) AS cotizacion_id,
                        GROUP_CONCAT(DISTINCT notaventa.oc_id) AS oc_id,
                        GROUP_CONCAT(DISTINCT notaventa.oc_file) AS oc_file,
                        GROUP_CONCAT(DISTINCT dteguiadesp.notaventa_id) AS notaventa_id,
                        GROUP_CONCAT(DISTINCT despachoord.despachosol_id) AS despachosol_id,
                        GROUP_CONCAT(DISTINCT dteguiadesp.despachoord_id) AS despachoord_id,
                        GROUP_CONCAT(DISTINCT dtedte.dter_id) AS dter_id,
                        GROUP_CONCAT(DISTINCT dter.nrodocto) AS nrodocto_guiadesp
                        FROM dte INNER JOIN dtedte
                        ON dte.id = dtedte.dte_id AND ISNULL(dte.deleted_at) and isnull(dtedte.deleted_at)
                        INNER JOIN dte AS dter
                        ON dter.id = dtedte.dter_id AND ISNULL(dter.deleted_at)
                        INNER JOIN dteguiadesp
                        ON dteguiadesp.dte_id = dter.id AND ISNULL(dteguiadesp.deleted_at)
                        INNER JOIN despachoord
                        ON despachoord.id = dteguiadesp.despachoord_id AND ISNULL(despachoord.deleted_at)
                        INNER JOIN notaventa
                        ON notaventa.id = despachoord.notaventa_id AND ISNULL(notaventa.deleted_at)
                        WHERE dte.id = $dte->id
                        GROUP BY dte.id;";
            $guia = DB::select($sql);
            $dteDocs[$i]->nrodocto_fac = $guia[0]->nrodocto_fac;
            $dteDocs[$i]->cotizacion_id = $guia[0]->cotizacion_id;
            $dteDocs[$i]->oc_id = $guia[0]->oc_id;
            $dteDocs[$i]->oc_file = $guia[0]->oc_file;
            $dteDocs[$i]->notaventa_id = $guia[0]->notaventa_id;
            $dteDocs[$i]->despachosol_id = $guia[0]->despachosol_id;
            $dteDocs[$i]->despachoord_id = $guia[0]->despachoord_id;
            $dteDocs[$i]->dter_id = $guia[0]->dter_id;
            $dteDocs[$i]->nrodocto_guiadesp = $guia[0]->nrodocto_guiadesp;
            //dd($dteDocs[$i]);
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
    (SELECT dte1.nrodocto
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
    dd($sql);
    return DB::select($sql);
}