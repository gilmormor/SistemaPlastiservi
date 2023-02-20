<?php

namespace App\Http\Controllers;

use App\Models\CentroEconomico;
use App\Models\Empresa;
use App\Models\Seguridad\Usuario;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        $tablas['empresa'] = Empresa::findOrFail(1);
        $centroeconomicos = CentroEconomico::orderBy('id')->get();

        //dd($tablas);
        return view('dtefacturadir.crear',compact('tablas','centroeconomicos'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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


    $sql = "SELECT dte.id,dte.nrodocto as nrodocto_factura,dte.fechahora,cliente.rut,cliente.razonsocial,
    comuna.nombre as nombre_comuna,
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
    AND dte.id NOT IN (SELECT dtedte.dte_id FROM dtedte WHERE ISNULL(dtedte.deleted_at))
    AND dte.sucursal_id IN ($sucurcadena)
    AND ISNULL(dte.statusgen)
    AND $aux_conddte_id
    GROUP BY dte.id
    ORDER BY dte.id desc;";

    return DB::select($sql);
}