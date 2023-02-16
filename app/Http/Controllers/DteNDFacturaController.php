<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarDTEND;
use App\Models\CentroEconomico;
use App\Models\Dte;
use App\Models\DteDet;
use App\Models\DteDte;
use App\Models\DteNcNd;
use App\Models\Empresa;
use App\Models\Seguridad\Usuario;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DteNDFacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-nota-debito-factura');
        return view('dtendfactura.index');
    }

    public function dtendfacturapage($dte_id = ""){
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
        return view('dtendfactura.crear',compact('tablas','centroeconomicos'));

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarDTEND $request)
    {
        can('guardar-nota-debito-factura');
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

        $centroeconomico = CentroEconomico::findOrFail($request->centroeconomico_id);
        //$cliente = Cliente::findOrFail($request->cliente_id);
        $request->request->add(['fchemis' => date('Y-m-d')]);

        $request->request->add(['cliente_id' => $dtefac->cliente_id]);
        $request->request->add(['tipodespacho' => $dtefac->tipodespacho]);
        $request->request->add(['fechahora' => date("Y-m-d H:i:s")]);
        //$request->request->add(['tasaiva' => $dteguiadesp->iva]);
        $request->request->add(['sucursal_id' => $centroeconomico->sucursal_id]);
        $request->request->add(['comuna_id' => $dtefac->comuna_id]);
        $request->request->add(['foliocontrol_id' => 6]); //CODIGO DE TIPO DE DTE 6=NOTA DEBITO
        $request->request->add(['usuario_id' => auth()->id()]);


        $dte = Dte::create($request->all());
        $dte_id = $dte->id;

        $arrayDTE = Dte::busDTEOrig($request->dte_id); //Buscar la factura inicial del DTE
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
                $dtedet->montoitem = $request->montoitem[$i];
                //$dtedet->obsdet = $request->obsdet[$i];
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

        return redirect('dtendfactura')->with([
            'mensaje'=>'Nota de Debito creada con exito.',
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
        $request->request->add(['foliocontrol_id' => "6"]);
        $request->request->add(['condFoliocontrol' => $request->tdfoliocontrol_id]);
        $request->request->add(['TipoDTE' => "ND"]);
        $respuesta = Dte::consdte_dtedet($request);
        return $respuesta;
    }

    public function generardtesii(Request $request){
        $request->request->add(['foliocontrol_id' => "6"]);
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
    WHERE dte.foliocontrol_id=6 
    AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
    AND ISNULL(dte.statusgen)
    AND dte.sucursal_id IN ($sucurcadena)
    AND $aux_conddte_id
    GROUP BY dte.id
    ORDER BY dte.id desc;";

    return DB::select($sql);
}