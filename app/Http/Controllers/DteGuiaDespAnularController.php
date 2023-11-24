<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\Comuna;
use App\Models\Dte;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DteGuiaDespAnularController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-guia-despacho-anular');
        return view('dteguiadespanular.index');
    }

    public function dteguiadespanularpage(){
        $datas = consultaindex();
        return datatables($datas)->toJson();
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

        return view('dteguiadespanular.listarguiadesp', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }

    public function listarguiadesppage(Request $request){
        $request->merge([
            'aux_condindtraslado' => "indtraslado = 6 and 
                dte.nrodocto in (SELECT despachoord.guiadespacho FROM despachoord where 
                despachoord.id not in (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at)) 
                AND 
                ISNULL(despachoord.deleted_at)) "
        ]);
        $datas = Dte::consultalistarguiadesppage($request);
        //dd($datas);
        return datatables($datas)->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

    
    public function dteguiadespanular(Request $request){
        $dte = Dte::findOrFail($request->dte_id);

        if($dte->updated_at != $request->updated_at){
            return response()->json([
                'id' => 0,
                'mensaje' => 'Registro Editado por otro usuario. Fecha Hora: '.$dte->updated_at,
                'tipo_alert' => 'error'
            ]);    
        }
        foreach ($dte->dtedets as $dtedet) {
            foreach ($dtedet->dtedet_despachoorddet->despachoorddet->despachoorddet_invbodegaproductos as $despachoorddet_invbodegaproducto) {
                dd($despachoorddet_invbodegaproducto->invbodegaproducto);
            }
        }

        dd($request);
    }

}


function consultaindex(){

    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql ="SELECT dte.id,dte.nrodocto,dte.fechahora,dte.fchemis,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
        despachoord.despachosol_id,dteguiadesp.despachoord_id,despachoord.fechaestdesp,comuna.nombre as cmnarecep,dte.kgtotal,
        dteguiadesp.tipoentrega_id,tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,
        clientebloqueado.descripcion as clientebloqueado_descripcion,dte.updated_at,dteanul.obs
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
        INNER JOIN dteanul
        ON dte.id = dteanul.dte_id and ISNULL(dteanul.deleted_at)
        LEFT JOIN clientebloqueado
        ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        WHERE despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
        AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
        AND notaventa.sucursal_id in ($sucurcadena)
        AND NOT ISNULL(dte.statusgen)
        AND dte.foliocontrol_id=2
        ORDER BY dte.id desc;";

    return DB::select($sql);

}