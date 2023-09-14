<?php

namespace App\Http\Controllers;

use App\Events\GuardarGuiaDespacho;
use App\Http\Requests\ValidarGuiaDesp;
use App\Models\AreaProduccion;
use App\Models\CentroEconomico;
use App\Models\Cliente;
use App\Models\ClienteDirec;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoOrd;
use App\Models\Empresa;
use App\Models\Foliocontrol;
use App\Models\Giro;
use App\Models\GuiaDesp;
use App\Models\GuiaDespAnul;
use App\Models\GuiaDespDet;
use App\Models\InvBodegaProducto;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\InvMovDet_BodOrdDesp;
use App\Models\InvMovModulo;
use App\Models\NotaVentaCerrada;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

require_once(base_path('vendor/setasign/fpdf/fpdf.php'));
require_once(base_path('vendor/setasign/fpdi/src/autoload.php'));

class GuiaDespController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-guia-despacho');
        return view('guiadesp.index');
    }

    public function guiadesppage(){
        $datas = consultaindex();
        return datatables($datas)->toJson();
    }


    public function listarorddesp()
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

        return view('guiadesp.listardespachoord', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));

        //can('listar-guia-despacho');
        $aux_vista = 'G';
        //dd('entro');
        return view('guiadesp.listardespachoord', compact('aux_vista'));
    }

    public function listarorddesppage(Request $request){
        $datas = consultalistarorddesppage($request);
        //dd($datas);
        return datatables($datas)->toJson();
    }
/*
    public function productobuscarpage(Request $request){
        $datas = Producto::productosxCliente($request);
        return datatables($datas)->toJson();
    }

    public function clientebuscarpage(){
        $datas = Cliente::clientesxUsuarioSQL();
        return datatables($datas)->toJson();
    }

    public function productobuscarpageid(Request $request){
        $datas = Producto::productosxCliente($request);
        return datatables($datas)->toJson();
    }

    public function clientebuscarpageid($id){
        $datas = Cliente::clientesxUsuarioSQL();
        return datatables($datas)->toJson();
    }
*/
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear($id)
    {
        can('crear-guia-despacho');
        $data = DespachoOrd::findOrFail($id);
        $detalles = $data->despachoorddets;
        $guiadesp = $data->guiadesp->whereNull('deleted_at');
        //dd(GuiaDespAnul::whereNull('deleted_at')->pluck('guiadesp_id')->toArray());
        $guiadesp = $guiadesp->whereNotIn('id',GuiaDespAnul::whereNull('deleted_at')->pluck('guiadesp_id')->toArray());
        if(count($guiadesp) > 0){
            return redirect('guiadesp/listarorddesp')->with([
                'mensaje'=>'Guia despacho creada o procesada por otro usuario!',
                'tipo_alert' => 'alert-error'
            ]);
 
        }
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $comunas = Comuna::orderBy('id')->get();
        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $centroeconomicos = CentroEconomico::orderBy('id')->get();
        $aux_status = "1"; //Crear
        //dd($data);
        return view('guiadesp.crear', compact('data','detalles','comunas','empresa','tipoentregas','centroeconomicos','aux_status'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarGuiaDesp $request)
    {
        can('guardar-guia-despacho');
        //dd($request);
        $despachoord = DespachoOrd::findOrFail($request->despachoord_id);
        if($request->updated_at != $despachoord->updated_at){
            return redirect('guiadesp_listarorddesp')->with([
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            return redirect('guiadesp')->with([
                'mensaje'=>'Guia Despacho sin items, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        if(count($despachoord->despachoorddets) != $cont_producto){
            return redirect('guiadesp')->with([
                'mensaje'=>'Cantidad de item diferentes a la Orden de espacho Original.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $empresa = Empresa::findOrFail(1);

        $hoy = date("Y-m-d H:i:s");
        $request->request->add(['fechahora' => $hoy]);
        $date = str_replace('/', '-', $request->fchemis);
        $request->request->add(['fchemis' => date('Y-m-d', strtotime($date))]);

        $mntneto = 0;
        $kgtotal = 0;
        foreach($despachoord->despachoorddets as $despachoorddet){
            $NVDet = $despachoorddet->notaventadetalle;
            $mntneto += (($NVDet->subtotal/$NVDet->cant) * $despachoorddet->cantdesp);
            $kgtotal += (($NVDet->totalkilos/$NVDet->cant) * $despachoorddet->cantdesp);
        }
        $request->request->add(['mntneto' => $mntneto]);
        $request->request->add(['tasaiva' => $empresa->iva]);
        $request->request->add(['iva' => round($empresa->iva * $mntneto/100)]);
        $request->request->add(['mnttotal' => round($mntneto + $empresa->iva)]);
        $request->request->add(['kgtotal' => $kgtotal]);
        $request->request->add(['sucursal_id' => $despachoord->notaventa->sucursal_id]);
        $request->request->add(['cliente_id' => $despachoord->notaventa->cliente_id]);
        $request->request->add(['comuna_id' => $despachoord->notaventa->comuna_id]);
        $request->request->add(['vendedor_id' => $despachoord->notaventa->vendedor_id]);
        $request->request->add(['oc_id' => $despachoord->notaventa->oc_id]);
        $request->request->add(['oc_file' => $despachoord->notaventa->oc_file]);

        
        $guiadesp = GuiaDesp::create($request->all());
        $guiadespid = $guiadesp->id;

        //$notaventaid = 1;
        //SI ESTA VACIO EL NUMERO DE COTIZACION SE CREA EL DETALLE DE LA NOTA DE VENTA DE LA TABLA DEL LADO DEL CLIENTE
        //SI NO ESTA VACIO EL NUMERO DE COTIZACION SE LLENA EL DETALLE DE LA NOTA DE VENTA DE LA TABLA DETALLE COTIZACION
        $kgtotal = 0;

        if(!empty($request->despachoord_id)){
            if($cont_producto>0){
                for ($i=0; $i < $cont_producto ; $i++){
                    if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                        $producto = Producto::findOrFail($request->producto_id[$i]);
                        $guiadespdet = new GuiaDespDet();
                        $guiadespdet->guiadesp_id = $guiadespid;
                        $guiadespdet->despachoorddet_id = $request->despachoorddet_id[$i];
                        $guiadespdet->notaventadetalle_id = $request->notaventadetalle_id[$i];
                        $guiadespdet->producto_id = $request->producto_id[$i];
                        $guiadespdet->nrolindet = $request->nrolindet[$i];
                        $guiadespdet->vlrcodigo = $request->producto_id[$i];
                        $guiadespdet->nmbitem = $request->nmbitem[$i];
                        $guiadespdet->dscitem = $request->dscitem[$i];
                        $guiadespdet->qtyitem = $request->qtyitem[$i];
                        $guiadespdet->unmditem = $request->unmditem[$i];
                        $guiadespdet->unidadmedida_id = $request->unidadmedida_id[$i];
                        $guiadespdet->prcitem = $request->prcitem[$i];
                        $guiadespdet->montoitem = $request->montoitem[$i];
                        $guiadespdet->obsdet = $request->obsdet[$i];
                        $guiadespdet->itemkg = $request->itemkg[$i];
                        $kgtotal += $request->itemkg[$i];
                        $guiadespdet->save();
                        $guiadespdet_id = $guiadespdet->id;
                    }
                }
            }
        }
        $guiadesp->kgtotal = $kgtotal;
        $guiadesp->save();
        return redirect('guiadesp')->with([
                                            'mensaje'=>'Guia Despacho creada con exito.',
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
        can('editar-guia-despacho');
        $guiadesp = GuiaDesp::findOrFail($id);
        if($guiadesp->statusgen == 1){
            return redirect('guiadesp')->with([
                'mensaje'=>'Guia Despacho ya fue Generada! Nro: ' . $guiadesp->nrodocto ,
                'tipo_alert' => 'alert-error'
            ]);
        }

        if($guiadesp->guiadespanul){
            return redirect('guiadesp')->with([
                'mensaje'=>'Registro fué anulado!',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $detalles = $guiadesp->guiadespdets;
        $data = DespachoOrd::findOrFail($guiadesp->despachoord_id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        //dd($data->notaventa->cliente->clientedirecs);
        $comunas = Comuna::orderBy('id')->get();
        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $centroeconomicos = CentroEconomico::orderBy('id')->get();
        $aux_status = "2"; //Editar
        //dd($data);
        return view('guiadesp.editar', compact('data','detalles','guiadesp','comunas','empresa','tipoentregas','centroeconomicos','aux_status'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarGuiaDesp $request, $id)
    {
        can('guardar-guia-despacho');
        $guiadesp = GuiaDesp::findOrFail($id);
        if($request->updated_at != $guiadesp->updated_at){
            return redirect('guiadesp')->with([
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            return redirect('guiadesp')->with([
                'mensaje'=>'Guia Despacho sin items, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        if(count($guiadesp->guiadespdets) != $cont_producto){
            return redirect('guiadesp')->with([
                'mensaje'=>'Cantidad de item diferentes a la Orden de espacho Original.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $empresa = Empresa::findOrFail(1);

        $date = str_replace('/', '-', $request->fchemis);
        $guiadesp->fchemis = date('Y-m-d', strtotime($date));

        $mntneto = 0;
        $kgtotal = 0;
        foreach($guiadesp->despachoord->despachoorddets as $despachoorddet){
            $NVDet = $despachoorddet->notaventadetalle;
            $mntneto += (($NVDet->subtotal/$NVDet->cant) * $despachoorddet->cantdesp);
            $kgtotal += (($NVDet->totalkilos/$NVDet->cant) * $despachoorddet->cantdesp);
        }
        $guiadesp->obs = $request->obs;
        $guiadesp->centroeconomico_id = $request->centroeconomico_id;
        $guiadesp->tipodespacho = $request->tipodespacho;
        $guiadesp->lugarentrega = $request->lugarentrega;
        $guiadesp->comunaentrega_id = $request->comunaentrega_id;
        $guiadesp->indtraslado = $request->indtraslado;
        $guiadesp->ot = $request->ot;
        $guiadesp->mntneto = $mntneto;
        $guiadesp->tasaiva = $empresa->iva;
        $guiadesp->iva = round($empresa->iva * $mntneto/100);
        $guiadesp->mnttotal = round($mntneto + $guiadesp->iva);
        $guiadesp->kgtotal = $kgtotal;
        $guiadesp->updated_at = date("Y-m-d H:i:s");
        $guiadesp->save();

        //$notaventaid = 1;
        //SI ESTA VACIO EL NUMERO DE COTIZACION SE CREA EL DETALLE DE LA NOTA DE VENTA DE LA TABLA DEL LADO DEL CLIENTE
        //SI NO ESTA VACIO EL NUMERO DE COTIZACION SE LLENA EL DETALLE DE LA NOTA DE VENTA DE LA TABLA DETALLE COTIZACION
        $kgtotal = 0;
        if(!empty($request->despachoord_id)){
            if($cont_producto>0){
                for ($i=0; $i < $cont_producto ; $i++){
                    if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                        $guiadespdet = GuiaDespDet::findOrFail($request->iddet[$i]);
                        $guiadespdet->nmbitem = $request->nmbitem[$i];
                        $guiadespdet->dscitem = $request->dscitem[$i];
                        $guiadespdet->qtyitem = $request->qtyitem[$i];
                        $guiadespdet->unmditem = $request->unmditem[$i];
                        $guiadespdet->unidadmedida_id = $request->unidadmedida_id[$i];
                        $guiadespdet->prcitem = $request->prcitem[$i];
                        $guiadespdet->montoitem = $request->montoitem[$i];
                        $guiadespdet->obsdet = $request->obsdet[$i];
                        $guiadespdet->itemkg = $request->itemkg[$i];
                        $kgtotal += $request->itemkg[$i];
                        $guiadespdet->save();
                    }
                }
            }
        }
        $guiadesp->kgtotal = $kgtotal;
        $guiadesp->save();
        return redirect('guiadesp')->with([
                                            'mensaje'=>'Guia Despacho Actualizada con exito.',
                                            'tipo_alert' => 'alert-success'
                                        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request,$id)
    {
        can('eliminar-guia-despacho');
        //dd($request);
        if ($request->ajax()) {
            //dd($id);
            $guiadesp = GuiaDesp::findOrFail($id);
            if($request->updated_at == $guiadesp->updated_at){
                if (GuiaDesp::destroy($id)) {
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $guiadesp = GuiaDesp::withTrashed()->findOrFail($id);
                    $guiadesp->usuariodel_id = auth()->id();
                    $guiadesp->save();
                    //Eliminar detalle de cotizacion
                    GuiaDespDet::where('guiadesp_id', $id)->update(['usuariodel_id' => auth()->id()]);
                    GuiaDespDet::where('guiadesp_id', '=', $id)->delete();
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }    
            }else{
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro no puede ser eliminado, fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }


        } else {
            abort(404);
        }
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

    public function guardarguiadesp(Request $request)
    {
        if ($request->ajax()) {
            $guiadesp = GuiaDesp::findOrFail($request->guiadesp_id);
            //$ArchivoTXT = dteguiadesp($guiadesp->id,201,"XML");
            //dd($ArchivoTXT);
            //Storage::disk('public')->put('/facturacion/dte/procesados/txtprueba.txt', $ArchivoTXT);

            //dd($ArchivoTXT);
            /*
            $ArchivoTXT = dteguiadesp($guiadesp->id,71);
            $soap = new SoapController();
            $Carga_TXTDTE = $soap->Carga_TXTDTE($ArchivoTXT,"TXT");
            dd($Carga_TXTDTE);*/

            if($guiadesp->updated_at != $request->updated_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro fué creado o modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            if(!is_null($guiadesp->statusgen)){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Guia de Despacho ya fue Generada! Nro: ' . $guiadesp->nrodocto,
                    'tipo_alert' => 'error'
                ]);
            }
            $foliocontrol = Foliocontrol::where("doc","=","GDVE")->get();
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
                    'mensaje'=>'Se agotaron los folios. Se deben pedir nuevos folios',
                    'tipo_alert' => 'error'
                ]);
            }
            $foliocontrol = Foliocontrol::findOrFail($foliocontrol[0]->id);
            if($foliocontrol->bloqueo == 1){
                $aux_guidesp = GuiaDesp::whereNotNull("nrodocto")
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
                    if(is_null($guiadesp->nrodocto)){
                        return response()->json([
                            'id' => 0,
                            'mensaje' => 'Existe una Guia de Despacho pendiente por Generar: ' . $aux_guidesp[0]->nrodocto,
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
            $despachoord = DespachoOrd::findOrFail($guiadesp->despachoord_id);
            $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$despachoord->notaventa_id)->get();
            if(count($notaventacerrada) == 0){
                $aux_bandera = true;
                foreach ($despachoord->despachoorddets as $despachoorddet) {
                    //dd($despachoorddet->despachoorddet_invbodegaproductos);
                    $aux_respuesta = InvBodegaProducto::validarExistenciaStock($despachoorddet->despachoorddet_invbodegaproductos,$request->invbodega_id);
                    if($aux_respuesta["bandera"] == false){
                        //dd($despachoorddet->despachoorddet_invbodegaproductos);
                        //dd($request->invbodega_id);
                        $aux_bandera = $aux_respuesta["bandera"];
                        break;
                    }
                }
                if($aux_bandera){
                    $invmodulo = InvMovModulo::where("cod","ORDDESP")->get();
                    if(count($invmodulo) == 0){
                        $foliocontrol->bloqueo = 0;
                        $foliocontrol->save();
                        return response()->json([
                            'mensaje' => 'MensajePersonalizado',
                            'menper' => "No existe modulo SOLDESP"    
                        ]);
                    }
                    $invmoduloBod = InvMovModulo::findOrFail($invmodulo[0]->id);
                    $aux_DespachoBodegaId = $invmoduloBod->invmovmodulobodents[0]->id; //Id Bodega Despacho (La bodega despacho debe ser unica)
                    $aux_bodegadespacho = 0;
                    foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                        //BUSCAR BODEGA DESPACHO DE SUCURSAL 
                        if($invmovmodulobodent->sucursal_id == $despachoord->notaventa->sucursal_id){
                            $aux_bodegadespacho = $invmovmodulobodent->id;
                        }
                    }
                    if($aux_bodegadespacho == 0){
                        $foliocontrol->bloqueo = 0;
                        $foliocontrol->save();
                        return response()->json([
                            'mensaje' => 'No existe Bodega Despacho en Sucursal: ' . $despachoord->notaventa->sucursal->nombre
                        ]);
                    }
                    $empresa = Empresa::findOrFail(1);
                    $soap = new SoapController();
                    $aux_folio = $guiadesp->nrodocto;
                    if(is_null($guiadesp->nrodocto)){
                        $bandNoExisteFolio = true;
                        do {
                            $Solicitar_Folio = $soap->Solicitar_Folio($empresa->rut,"52");
                            if(isset($Solicitar_Folio->Estatus)){
                                if($Solicitar_Folio->Estatus == 0){
                                    $Estado_DTE = $soap->Estado_DTE($empresa->rut,"52",$Solicitar_Folio->Folio);
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
                    $ArchivoTXT = dteguiadesp($guiadesp->id,$aux_folio,$tipoArch);
                    $Carga_TXTDTE = $soap->Carga_TXTDTE($ArchivoTXT,$tipoArch);
                    //$Carga_TXTDTE = $soap->Carga_TXTDTE($ArchivoTXT,"XML");
                    if(isset($Carga_TXTDTE->Estatus)){
                        //ACTUALIZO EL CAMPO nrodocto
                        //SI OCURRIO ALGUN ERROR SE QUE TENGO EL FOLIO, 
                        //SE QUE NO LO PUEDO VOLVER A PEDIR PORQUE POR ALGUNA RAZON SE GENERO UN ERROR EN EL ULTIMO FOLIO SOLICITADO
                        $aux_giadesp = GuiaDesp::where('id', $guiadesp->id)
                                ->update(['nrodocto' => $aux_folio]);
                        if($Carga_TXTDTE->Estatus == 0){
                            $guiadesp->fchemisgen = date("Y-m-d H:i:s");
                            //$date = str_replace('/', '-', $request->fchemis);
                            //$guiadesp->fchemis = date('Y-m-d', strtotime($date));
                            //$guiadesp->fchemis = date("Y-m-d H:i:s");
                            /*
                            $guiadesp->pdf = $Carga_TXTDTE->PDF;
                            $guiadesp->pdfcedible = $Carga_TXTDTE->PDFCedible;
                            $guiadesp->xml = $Carga_TXTDTE->XML;
                            */
                            $guiadesp->statusgen = 1;
                            $guiadesp->aprobstatus = 1;
                            $guiadesp->aprobusu_id = auth()->id();
                            $guiadesp->aprobfechahora = date("Y-m-d H:i:s");
                
                            $guiadesp->save();
                            //$fchemisDMY = date("d-m-Y_His",strtotime($guiadesp->fchemis));

                            Storage::disk('public')->put('/facturacion/dte/procesados/ge' . $aux_folio . '.xml', $Carga_TXTDTE->XML);
                            Storage::disk('public')->put('/facturacion/dte/procesados/ge' . $aux_folio . '.pdf', $Carga_TXTDTE->PDF);
                            Storage::disk('public')->put('/facturacion/dte/procesados/ge' . $aux_folio . '_cedible.pdf', $Carga_TXTDTE->PDFCedible);
                            /*
                            $pdf = new Fpdi();
                            $files = array("storage/facturacion/dte/procesados/ge" . $aux_folio . ".pdf","storage/facturacion/dte/procesados/ge" . $aux_folio . "_cedible.pdf");
                            foreach ($files as $file) {
                                $pageCount = $pdf->setSourceFile($file);
                                for ($pagNo=1; $pagNo <= $pageCount; $pagNo++) { 
                                    $template = $pdf->importPage($pagNo);
                                    $size = $pdf->getTemplateSize($template);
                                    $pdf->AddPage($size['orientation'], $size);
                                    $pdf->useTemplate($template);
                                }
                            }
                            $pdf->Output("F","storage/facturacion/dte/procesados/ge" . $aux_folio . "_U.pdf");
                            */

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
                        $foliocontrol->bloqueo = 0;
                        $foliocontrol->save();
                        return response()->json([
                            'id' => 0,
                            'mensaje'=>'Error: ' . $Solicitar_Folio,
                            'tipo_alert' => 'error'                
                        ]);
                    }
    
                    $invmov_array = array();
                    $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                    $invmov_array["annomes"] = $aux_respuesta["annomes"];
                    $invmov_array["desc"] = "Salida de BD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                    $invmov_array["obs"] = "Salida de BD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                    $invmov_array["invmovmodulo_id"] = $invmoduloBod->id; //Guia de Despacho
                    $invmov_array["idmovmod"] = $request->id;
                    $invmov_array["invmovtipo_id"] = 2;
                    $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
                    $invmov_array["usuario_id"] = auth()->id();
                    $arrayinvmov_id = array();
                    
                    $invmov = InvMov::create($invmov_array);
                    array_push($arrayinvmov_id, $invmov->id);
                    foreach ($despachoord->despachoorddets as $despachoorddet) {
                        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                            $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                                ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $aux_bodegadespacho],
                                [
                                    'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                                    'invbodega_id' => $aux_bodegadespacho
                                ]
                            );
    
                            $array_invmovdet = $oddetbodprod->attributesToArray();
                            $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                            $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                            $array_invmovdet["invbodega_id"] = $aux_bodegadespacho;
                            $array_invmovdet["sucursal_id"] = $despachoord->notaventa->sucursal_id; //$invbodegaproducto->invbodega->sucursal_id;
                            $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                            $array_invmovdet["invmovtipo_id"] = 2;
                            $array_invmovdet["invmov_id"] = $invmov->id;
                            $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                            $array_invmovdet["cantxgrupo"] = 1;
                            $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                            $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                            $invmovdet = InvMovDet::create($array_invmovdet);
                            $invmovdet_bodorddesp = InvMovDet_BodOrdDesp ::create([
                                'invmovdet_id' => $invmovdet->id,
                                'despachoorddet_invbodegaproducto_id' => $oddetbodprod->id
                            ]);
                        }
                    }
                    $guiadesp = GuiaDesp::findOrFail($request->guiadesp_id);
                    return updatenumguia($despachoord,$guiadesp,$foliocontrol,$request);
                }else{
                    $foliocontrol->bloqueo = 0;
                    $foliocontrol->save();
                    return response()->json([
                        'id' => 0,
                        'mensaje'=> "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"],
                        'tipo_alert' => 'error'
                    ]);

                    return response()->json([
                        'mensaje' => "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"],
                        'menper' => "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"]
                    ]);
                }
            }else{
                $foliocontrol->bloqueo = 0;
                $foliocontrol->save();
                $mensaje = 'Nota Venta fue cerrada: Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at));
                return response()->json(['mensaje' => $mensaje]);
            }
        } else {
            abort(404);
        }    
    }

    public function exportPdf($id,$stareport = '1')
    {
        if(can('ver-pdf-guia-despacho',false)){
            $guiadesp = GuiaDesp::findOrFail($id);

            $despachoord = DespachoOrd::findOrFail($guiadesp->despachoord_id);
            //dd($despachoord);
            $despachoorddets = $despachoord->despachoorddets()->get();
            //dd($despachoorddets);
            $empresa = Empresa::orderBy('id')->get();
            $rut = number_format( substr ( $despachoord->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachoord->notaventa->cliente->rut, strlen($despachoord->notaventa->cliente->rut) -1 , 1 );
            //dd($empresa[0]['iva']);
            if(env('APP_DEBUG')){
                return view('guiadesp.reporte', compact('despachoord','despachoorddets','empresa'));
            }
            $pdf = PDF::loadView('guiadesp.reporte', compact('despachoord','despachoorddets','empresa'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream(str_pad("GUIADESP" . $despachoord->guiadesp->id, 5, "0", STR_PAD_LEFT) .' - '. $despachoord->notaventa->cliente->razonsocial . '.pdf');
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
    }

    public function validarupdated(Request $request)
    {
        //dd($request);
        $guiadesp = GuiaDesp::findOrFail($request->guiadesp_id);
        if($request->updated_at == $guiadesp->updated_at){
            return response()->json(['mensaje' => 'ok']);
        }else{
            return response()->json([
                'id' => 0,
                'mensaje'=>'Registro no puede editado, fué modificado por otro usuario.',
                'tipo_alert' => 'error'
            ]);
        }
    }

    public function consultarGuiaDesp(Request $request)
    {
        if ($request->ajax()) {
            $guiadesp = GuiaDesp::findOrFail($request->id);
            if ($guiadesp) {
                return response()->json([
                                        'mensaje' => 'ok',
                                        'guiadesp' => $guiadesp,
                                        'fechafactura' => date("d/m/Y", strtotime($guiadesp->fchemis))
                                        ]);
            } else {
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro no Existe. Id: ' . $request->id,
                    'tipo_alert' => 'error'
                ]);
            }
        } else {
            abort(404);
        }
    }


}

function consultaindex(){

    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql ="SELECT guiadesp.id,guiadesp.nrodocto,guiadesp.fechahora,guiadesp.fchemis,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,guiadesp.notaventa_id,
        despachoord.despachosol_id,guiadesp.despachoord_id,despachoord.fechaestdesp,comuna.nombre as cmnarecep,guiadesp.kgtotal,
        guiadesp.tipoentrega_id,tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,
        clientebloqueado.descripcion as clientebloqueado_descripcion,guiadesp.updated_at
        FROM guiadesp INNER JOIN notaventa
        ON guiadesp.notaventa_id = notaventa.id AND ISNULL(guiadesp.deleted_at) and isnull(notaventa.deleted_at)
        INNER JOIN despachoord
        ON guiadesp.despachoord_id = despachoord.id AND ISNULL(despachoord.deleted_at)
        INNER JOIN tipoentrega
        ON guiadesp.tipoentrega_id  = tipoentrega.id AND ISNULL(tipoentrega.deleted_at)
        INNER JOIN cliente
        ON notaventa.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
        inner join comuna
        ON cliente.comunap_id  = comuna.id AND ISNULL(comuna.deleted_at)
        LEFT JOIN clientebloqueado
        ON notaventa.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        WHERE despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
        AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
        AND guiadesp.id NOT IN (SELECT guiadespanul.guiadesp_id FROM guiadespanul WHERE ISNULL(guiadespanul.deleted_at))
        AND notaventa.sucursal_id in ($sucurcadena)
        AND ISNULL(guiadesp.statusgen)
        ORDER BY guiadesp.id desc;";

    return DB::select($sql);

}

function consultalistarorddesppage($request){

    if(empty($request->vendedor_id)){
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $vendedorcond = "notaventa.vendedor_id=" . $vendedor_id ;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
            $sucurArray = $user->sucursales->pluck('id')->toArray();
        }else{
            $vendedorcond = " true ";
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
    }else{
        $vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }

    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "despachoord.fechahora>='$fechad' and despachoord.fechahora<='$fechah'";
    }
    if(empty($request->rut)){
        $aux_condrut = " true";
    }else{
        $aux_condrut = "cliente.rut='$request->rut'";
    }
    if(empty($request->oc_id)){
        $aux_condoc_id = " true";
    }else{
        $aux_condoc_id = "notaventa.oc_id='$request->oc_id'";
    }
    if(empty($request->giro_id)){
        $aux_condgiro_id = " true";
    }else{
        $aux_condgiro_id = "notaventa.giro_id='$request->giro_id'";
    }
    if(empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = " true";
    }else{
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }
    if(empty($request->tipoentrega_id)){
        $aux_condtipoentrega_id = " true";
    }else{
        $aux_condtipoentrega_id = "notaventa.tipoentrega_id='$request->tipoentrega_id'";
    }
    if(empty($request->notaventa_id)){
        $aux_condnotaventa_id = " true";
    }else{
        $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
    }

    if(empty($request->aprobstatus)){
        $aux_aprobstatus = " true";
    }else{
        switch ($request->aprobstatus) {
            case 1:
                $aux_aprobstatus = "notaventa.aprobstatus='0'";
                break;
            case 2:
                $aux_aprobstatus = "notaventa.aprobstatus='$request->aprobstatus'";
                break;    
            case 3:
                $aux_aprobstatus = "(notaventa.aprobstatus='1' or notaventa.aprobstatus='3')";
                break;
            case 4:
                $aux_aprobstatus = "notaventa.aprobstatus='$request->aprobstatus'";
                break;
        }
        
    }

    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
    }


    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql = "SELECT despachoord.id,notaventa.cotizacion_id,despachoord.despachosol_id,despachoord.fechahora,despachoord.fechaestdesp,
    cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
    '' as notaventaxk,comuna.nombre as comuna_nombre,
    tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
    SUM(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
    sum(round((despachoorddet.cantdesp * notaventadetalle.preciounit) * ((notaventa.piva+100)/100))) as subtotal,
    despachoord.updated_at,'' as rutacrear
    FROM despachoord INNER JOIN notaventa
    ON despachoord.notaventa_id = notaventa.id AND ISNULL(despachoord.deleted_at) and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
    INNER JOIN despachoorddet
    ON despachoorddet.despachoord_id = despachoord.id AND ISNULL(despachoorddet.deleted_at)
    INNER JOIN notaventadetalle
    ON notaventadetalle.id = despachoorddet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
    INNER JOIN tipoentrega
    ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
    LEFT JOIN clientebloqueado
    ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    WHERE $vendedorcond
    and $aux_condFecha
    and $aux_condrut
    and $aux_condoc_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condtipoentrega_id
    and $aux_condnotaventa_id
    and $aux_aprobstatus
    and $aux_condcomuna_id
    and despachoord.aprguiadesp='1' and isnull(despachoord.guiadespacho)
    AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
    AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
    AND notaventa.sucursal_id in ($sucurcadena)
    AND despachoord.id NOT IN (SELECT guiadesp.despachoord_id FROM guiadesp WHERE ISNULL(guiadesp.deleted_at) AND guiadesp.id not in (SELECT guiadespanul.guiadesp_id FROM guiadespanul WHERE ISNULL(guiadespanul.deleted_at)))
    GROUP BY despachoorddet.despachoord_id;";
    $arrays = DB::select($sql);
    $i = 0;
    foreach ($arrays as $array) {
        $arrays[$i]->rutacrear = route('crear_guiadesp', ['id' => $array->id]);
        $i++;
    }
    return $arrays;
}

function updatenumguia($despachoord,$guiadesp,$foliocontrol,$request){
    $foliocontrol->ultfoliouti = $guiadesp->nrodocto;
    $foliocontrol->bloqueo = 0;
    $despachoord->guiadespacho = $guiadesp->nrodocto;
    $despachoord->guiadespachofec = ($guiadesp->fchemis . " 00:00:00");
    if ($despachoord->save() and $guiadesp->save() and $foliocontrol->save()) {
        //dteguiadesp($guiadesp->id);
        Event(new GuardarGuiaDespacho($despachoord));
        return response()->json([
                                'mensaje' => 'ok',
                                'despachoord' => $despachoord,
                                'guiadespachofec' => date("Y-m-d", strtotime($despachoord->guiadespachofec)),
                                'status' => '0',
                                'id' => $request->id,
                                'nfila' => $request->nfila,
                                'nrodocto' => $guiadesp->nrodocto
                                ]);
    } else {
        return response()->json(['mensaje' => 'ng']);
    }
}

function dteguiadesp($id,$Folio,$tipoArch){
    $Folio = str_pad($Folio, 10, "0", STR_PAD_LEFT);
    $guiadesp = GuiaDesp::findOrFail($id);
    $rutrecep = $guiadesp->cliente->rut;
    $rutrecep = number_format( substr ( $rutrecep, 0 , -1 ) , 0, "", "") . '-' . substr ( $rutrecep, strlen($rutrecep) -1 , 1 );

    $empresa = Empresa::findOrFail(1);
    $RznSoc = strtoupper(substr(trim($empresa->razonsocial),0,100));
    $GiroEmis = strtoupper(substr(trim($empresa->giro),0,80));
    $Acteco = substr(trim($empresa->acteco),0,6);
    $DirOrigen = strtoupper(substr(trim($empresa->sucursal->direccion),0,60));
    $CmnaOrigen = strtoupper(substr(trim($empresa->sucursal->comuna->nombre),0,20));
    $CiudadOrigen = strtoupper(substr(trim($empresa->sucursal->comuna->provincia->nombre),0,20));
    $contacto = strtoupper(substr(trim($guiadesp->notaventa->contacto . " Telf:" . $guiadesp->notaventa->contactotelf),0,80));
    $CorreoRecep = strtoupper(substr(trim($guiadesp->cliente->contactoemail),0,80));
    $RznSocRecep = strtoupper(substr(trim($guiadesp->cliente->razonsocial),0,100));
    $GiroRecep = strtoupper(substr(trim($guiadesp->cliente->giro),0,42));
    $DirRecep = strtoupper(substr(trim($guiadesp->cliente->direccion),0,70));
    $CmnaRecep = strtoupper(substr(trim($guiadesp->cliente->comuna->nombre),0,20));
    $CiudadRecep = strtoupper(substr(trim($guiadesp->cliente->provincia->nombre),0,20));

    $FolioRef = substr(trim($guiadesp->oc_id),0,20);
    $contenido = "";

    if($tipoArch == "TXT"){
        $fchemisDMY = date("d-m-Y_His",strtotime($guiadesp->fchemis));
        $fchemis = date("d-m-Y",strtotime($guiadesp->fchemis)); // date("Y-m-d");
        $contenido = "ENC|52||$Folio|$fchemis||$guiadesp->tipodespacho|$guiadesp->indtraslado|||||||||||$fchemis|" . 
        "$empresa->rut|$RznSoc|$GiroEmis|||$DirOrigen|$CmnaOrigen|$CiudadOrigen|||$rutrecep||" . 
        "$RznSocRecep|$GiroRecep|$contacto|$CorreoRecep|$DirRecep|$CmnaRecep|$CiudadRecep||||||||||" .
        "$guiadesp->mntneto|||$guiadesp->tasaiva|$guiadesp->iva||||||$guiadesp->mnttotal||$guiadesp->mnttotal|\r\n" . 
        "ACT|$Acteco|\r\n";
        foreach ($guiadesp->guiadespdets as $guiadespdet) {
            $contenido .= "DET|$guiadespdet->nrolindet||$guiadespdet->nmbitem|$guiadespdet->dscitem||||$guiadespdet->qtyitem|||$guiadespdet->unmditem|$guiadespdet->prcitem|||||||||$guiadespdet->montoitem|\r\n" . 
                        "ITEM|INTERNO|$guiadespdet->vlrcodigo|\r\n";
        }
        $TpoDocRef = (empty($guiadesp->despachoord_id) ? "" : "OD:" . $guiadesp->despachoord_id . " ") . (empty($guiadesp->ot) ? "" : "OT:" . $guiadesp->ot . " ")  . (empty($guiadesp->obs) ? "" : $guiadesp->obs . " ") . (empty($guiadesp->lugarentrega) ? "" : $guiadesp->lugarentrega . " ")  . (empty($guiadesp->comunaentrega_id) ? "" : $guiadesp->comunaentrega->nombre . " ");
        $TpoDocRef = substr(trim($TpoDocRef),0,90);
        $contenido .= "REF|1|801||$guiadesp->oc_id||$fchemis||$TpoDocRef|";
    
    }
    

    if($tipoArch == "XML"){
        $FchEmis = $guiadesp->fchemis;
        //$FchEmis = date("d-m-Y",strtotime($guiadesp->fchemis));
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
        "<TipoDTE>52</TipoDTE>" .
        "<Folio>$Folio</Folio>" .
        "<FchEmis>$FchEmis</FchEmis>" .
        "<TipoDespacho>$guiadesp->tipodespacho</TipoDespacho>" .
        "<IndTraslado>$guiadesp->indtraslado</IndTraslado>" .
        "<TpoImpresion>N</TpoImpresion>" .
        "</IdDoc>" .
        "<Emisor>" .
        "<RUTEmisor>$empresa->rut</RUTEmisor>" .
        "<RznSoc>$RznSoc</RznSoc>" .
        "<GiroEmis>$GiroEmis</GiroEmis>" .
        "<Acteco>$Acteco</Acteco>" .
        "<FolioAut>sdfsdf</FolioAut>" .
        "<FchAut>$FchEmis</FchAut>" .
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
        "<MntNeto>$guiadesp->mntneto</MntNeto>" .
        "<TasaIVA>$guiadesp->tasaiva</TasaIVA>" .
        "<IVA>$guiadesp->iva</IVA>" .
        "<MntTotal>$guiadesp->mnttotal</MntTotal>" .
        "</Totales>" .
        "</Encabezado>";
    
        $aux_totalqtyitem = 0;
    
        foreach ($guiadesp->guiadespdets as $guiadespdet) {
            $VlrCodigo = substr(trim($guiadespdet->vlrcodigo),0,35);
            $NmbItem = strtoupper(substr(trim($guiadespdet->nmbitem),0,80));
            $DscItem = strtoupper(trim($guiadespdet->dscitem));
            $UnmdItem = substr(trim($guiadespdet->unmditem),0,4);
            $contenido .= "<Detalle>" .
            "<NroLinDet>$guiadespdet->nrolindet</NroLinDet>" .
            "<CdgItem>" .
            "<TpoCodigo>INTERNO</TpoCodigo>" .
            "<VlrCodigo>" . $VlrCodigo . "</VlrCodigo>" .
            "</CdgItem>" .
            "<NmbItem>" . $VlrCodigo . "</NmbItem>" .
            "<DscItem>" . $NmbItem . "</DscItem>" .
            "<QtyItem>" . $guiadespdet->qtyitem . "</QtyItem>" .
            "<UnmdItem>" . $UnmdItem . "</UnmdItem>" .
            "<PrcItem>$guiadespdet->prcitem</PrcItem>" .
            "<MontoItem>$guiadespdet->montoitem</MontoItem>" .
            "</Detalle>";
            $aux_totalqtyitem += $guiadespdet->qtyitem;
        }
    
        $TpoDocRef = (empty($guiadesp->despachoord_id) ? "" : "OD:" . $guiadesp->despachoord_id . " ") . (empty($guiadesp->ot) ? "" : "OT:" . $guiadesp->ot . " ")  . (empty($guiadesp->obs) ? "" : $guiadesp->obs . " ") . (empty($guiadesp->lugarentrega) ? "" : $guiadesp->lugarentrega . " ")  . (empty($guiadesp->comunaentrega_id) ? "" : $guiadesp->comunaentrega->nombre . " ");
        $TpoDocRef = sanear_string(strtoupper(substr(trim($TpoDocRef),0,90)));
        //dd($TpoDocRef);
    
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
        "</Referencia>" .
        "<Referencia>" .
        "<NroLinRef>3</NroLinRef>" .
        "<TpoDocRef>801</TpoDocRef>" .
        "<FolioRef>$FolioRef</FolioRef>" .
        "<FchRef>$FchEmis</FchRef>" .
        "<RazonRef>$TpoDocRef</RazonRef>" .
        "</Referencia>" .
        "</Documento>" .
        "</DTE>";

        //"</DTE>]]>";

        //dd($contenido);
    
    }

    return $contenido;


    //Storage::disk('public')->put('/facturacion/dte/procesados/ge' . $guiadesp->nrodocto . '_' . $fchemisDMY . '.txt', $contenido);

}