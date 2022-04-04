<?php

namespace App\Http\Controllers;

use App\Events\GuardarFacturaDespacho;
use App\Events\GuardarGuiaDespacho;
use App\Events\Notificacion;
use App\Http\Requests\ValidarDespachoOrd;
use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteBloqueado;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoObs;
use App\Models\DespachoOrd;
use App\Models\DespachoOrd_InvMov;
use App\Models\DespachoOrdAnul;
use App\Models\DespachoOrdDet;
use App\Models\DespachoOrdDet_InvBodegaProducto;
use App\Models\DespachoOrdRec;
use App\Models\DespachoSol;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\InvBodegaProducto;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\NotaVentaCerrada;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespachoOrdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-orden-despacho');
        /*
        $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
        $notaventacerradaArray = NotaVentaCerrada::pluck('notaventa_id')->toArray();
        $datas = DespachoOrd::orderBy('id')
                ->whereNull('aprguiadesp')
                ->whereNull('guiadespacho')->whereNull('numfactura')
                ->whereNotIn('id',  $despachoordanul)
                ->whereNotIn('notaventa_id', $notaventacerradaArray)
                ->get();
        return view('despachoord.index', compact('datas'));
        */
        return view('despachoord.index');
    }

    public function despachoordpage(){
        $datas = consultaindex();
        return datatables($datas)->toJson();
    }


    public function indexguia()
    {
        can('listar-guia-despacho');
        $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
        $notaventacerradaArray = NotaVentaCerrada::pluck('notaventa_id')->toArray();
        $datas = DespachoOrd::orderBy('id')
                        ->where('aprguiadesp','1')
                        ->whereNull('guiadespacho')
                        ->whereNotIn('id',  $despachoordanul)
                        ->whereNotIn('notaventa_id', $notaventacerradaArray)
                        ->get();
        $aux_vista = 'G';
        $aux_titulo = "Asignar Guia Despacho";
        return view('despachoord.indexguiafact', compact('datas','aux_vista','aux_titulo'));
    }

    public function indexfact()
    {
        can('listar-factura-despacho');
        $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
        $notaventacerradaArray = NotaVentaCerrada::pluck('notaventa_id')->toArray();
        $datas = DespachoOrd::orderBy('id')
                        ->whereNotNull('guiadespacho')
                        ->whereNull('numfactura')
                        ->whereNotIn('id',  $despachoordanul)
                        ->whereNotIn('notaventa_id', $notaventacerradaArray)
                        ->get();
        $aux_vista = 'F';
        $aux_titulo = "Asignar Número de Factura";
        return view('despachoord.indexguiafact', compact('datas','aux_vista','aux_titulo'));
    }

        public function indexcerrada()
    {
        can('listar-orden-despacho-cerrada');
        $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
        $datas = DespachoOrd::orderBy('id')
                        ->whereNotNull('guiadespacho')
                        ->whereNotNull('numfactura')
                        ->whereNotIn('id',  $despachoordanul)
                        ->get();
        $aux_vista = 'C';
        $aux_titulo = "Ordenes de Despacho Cerradas";
        return view('despachoord.indexguiafact', compact('datas','aux_vista','aux_titulo'));
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

    public function crearord($id)
    {
        can('crear-orden-despacho');
        $data = DespachoSol::findOrFail($id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $detalles = $data->despachosoldets()->get();
        /*
        foreach($detalles as $detalle){
            dd($detalle);
            $sql = "SELECT cantsoldesp
                    FROM vista_sumsoldespdet
                    WHERE notaventadetalle_id=$detalle->id";
            $datasuma = DB::select($sql);
            if(empty($datasuma)){
                $sumacantsoldesp= 0;
            }else{
                $sumacantsoldesp= $datasuma[0]->cantsoldesp;
            }
            //if($detalle->cant > $sumacantsoldesp);
            
        } */
        //dd($detalles);
        $vendedor_id=$data->notaventa->vendedor_id;
        $clienteselec = $data->notaventa->cliente()->get();
        //session(['aux_aprocot' => '0']);
        //dd($clienteselec[0]->rut);

        $clientesArray = Cliente::clientesxUsuario($vendedor_id);
        $clientes = $clientesArray['clientes'];
        //$vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        //dd($sucurArray);
        //Aqui si estoy filtrando solo las categorias de asignadas al usuario logueado
        //******************* */
        $clientedirecs = Cliente::where('rut', $clienteselec[0]->rut)
        ->join('clientedirec', 'cliente.id', '=', 'clientedirec.cliente_id')
        ->join('cliente_sucursal', 'cliente.id', '=', 'cliente_sucursal.cliente_id')
        ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->select([
                    'cliente.id as cliente_id',
                    'cliente.razonsocial',
                    'cliente.telefono',
                    'cliente.email',
                    'cliente.regionp_id',
                    'cliente.provinciap_id',
                    'cliente.comunap_id',
                    'cliente.contactonombre',
                    'cliente.direccion',
                    'clientedirec.id',
                    'clientedirec.direcciondetalle'
                ])->get();
        //dd($clientedirecs);
        $clienteDirec = $data->notaventa->clientedirec()->get();
        $fecha = date("d/m/Y", strtotime($data->fechahora));
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $productos = Producto::productosxUsuario();

        $vendedores1 = Usuario::join('sucursal_usuario', function ($join) {
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
            ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
                    })
            ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
            ->join('vendedor', function ($join) {
                $join->on('persona.id', '=', 'vendedor.persona_id')
                    ->where('vendedor.sta_activo', '=', 1);
            })
            ->select([
                'vendedor.id',
                'persona.nombre',
                'persona.apellido'
            ])
            ->get();

        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $giros = Giro::orderBy('id')->get();
        $despachoobss = DespachoObs::orderBy('id')->get();
        $aux_sta=2;
        $aux_statusPant = 0;
        session(['aux_fecinicreOD' => date("Y-m-d H:i:s")]); //Fecha inicio de creacion Orden de despacho

        //dd($clientedirecs);
        return view('despachoord.crear', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','despachoobss','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id'));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarDespachoOrd $request)
    {
        can('guardar-orden-despacho');
        //dd($request);
        //dd(session('aux_fecinicreOD'));
        $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$request->notaventa_id)->get();
        //dd($notaventacerrada);
        if(count($notaventacerrada) == 0){
            $despachosol = DespachoSol::findOrFail($request->despachosol_id);

            $array_despachoord = DespachoOrd::where("despachosol_id",$request->despachosol_id)->pluck('id')->toArray();
            $despachoordrec = DespachoOrdRec::whereIn('despachoord_id',$array_despachoord)
                            ->where("updated_at",">",session('aux_fecinicreOD'))
                            ->get();
            if(count($despachoordrec) > 0){
                return redirect('despachoord')->with([
                    'mensaje'=>'Registro no fue creado. Motivo: Fue actualizado un Rechazo.',
                    'tipo_alert' => 'alert-error'
                ]);

            }else{
                //dd($aux_despachoord);

                $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$despachosol->notaventa->cliente_id)->get();
                if(count($clibloq) > 0){
                    return redirect('despachoord')->with([
                        'mensaje'=>'Registro no fue guardado. Cliente Bloqueado: ' . $clibloq[0]->descripcion ,
                        'tipo_alert' => 'alert-error'
                    ]);
                }
                if($despachosol->updated_at == $request->updated_at){
                    $despachosol->updated_at = date("Y-m-d H:i:s");
                    $despachosol->save();
                    $hoy = date("Y-m-d H:i:s");
                    $request->request->add(['fechahora' => $hoy]);
                    $request->request->add(['usuario_id' => auth()->id()]);
                    $dateInput = explode('/',$request->plazoentrega);
                    $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
                    $dateInput = explode('/',$request->fechaestdesp);
                    $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
                    $despachoord = DespachoOrd::create($request->all());
                    $despachoord_id = $despachoord->id;
                    $cont_producto = count($request->producto_id);
                    if($cont_producto>0){
                        for ($i=0; $i < $cont_producto ; $i++){
                            $aux_cantord = $request->cantord[$i];
                            if(is_null($request->producto_id[$i])==false && is_null($aux_cantord)==false && $aux_cantord > 0){
                                $despachoorddet = new DespachoOrdDet();
                                $despachoorddet->despachoord_id = $despachoord_id;
                                $despachoorddet->despachosoldet_id = $request->despachosoldet_id[$i];
                                $despachoorddet->notaventadetalle_id = $request->notaventadetalle_id[$i];
                                $despachoorddet->cantdesp = $request->cantord[$i];
                                if($despachoorddet->save()){
                                    $cont_bodegas = count($request->invcant);
                                    if($cont_bodegas>0){
                                        for ($b=0; $b < $cont_bodegas ; $b++){
                                            if($request->invbodegaproducto_producto_id[$b] == $request->producto_id[$i] and ($request->invcant[$b] != 0)){
                                                $despachoorddet_invbodegaproducto = new DespachoOrdDet_InvBodegaProducto();
                                                $despachoorddet_invbodegaproducto->despachoorddet_id = $despachoorddet->id;
                                                $despachoorddet_invbodegaproducto->invbodegaproducto_id = $request->invbodegaproducto_id[$b];
                                                $despachoorddet_invbodegaproducto->invbodegaproducto_id = $request->invbodegaproducto_id[$b];
                                                $despachoorddet_invbodegaproducto->cant = $request->invcant[$b] * -1;
                                                $despachoorddet_invbodegaproducto->save();
                                            }
                                        }
                                    }
                                    /*
                                    $notaventadetalle = NotaVentaDetalle::findOrFail($request->NVdet_id[$i]);
                                    $notaventadetalle->cantsoldesp = $request->cantsoldesp[$i];
                                    $notaventadetalle->save();
                                    */
                                    //$despacho_id = $despachoord->id;
                                }
                            }
                        }
                    }
                    return redirect('despachoord')->with([
                        'mensaje'=>'Registro creado con exito.',
                        'tipo_alert' => 'alert-success'
                    ]);
                }else{
                    return redirect('despachoord')->with([
                        'mensaje'=>'Registro no fue creado. Registro Editado por otro usuario. Fecha Hora: '.$despachosol->updated_at,
                        'tipo_alert' => 'alert-error'
                    ]);
                }
            }
        }else{
            return redirect('despachoord')->with([
                'mensaje'=>'Registro no fue creado. La nota de venta fue Cerrada. Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at)),
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
    public function editar($id)
    {
        can('editar-orden-despacho');
        $data = DespachoOrd::findOrFail($id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $detalles = $data->despachoorddets()->get();
        //dd($detalles);
/*
        foreach($detalles as $detalle){
            dd($detalle);
            $sql = "SELECT cantsoldesp
                    FROM vista_sumsoldespdet
                    WHERE notaventadetalle_id=$detalle->notaventadetalle_id";
            $datasuma = DB::select($sql);
            if(empty($datasuma)){
                $sumacantsoldesp= 0;
            }else{
                $sumacantsoldesp= $datasuma[0]->cantsoldesp;
            }
            //if($detalle->cant > $sumacantsoldesp);
            
        }*/
        $vendedor_id=$data->notaventa->vendedor_id;
        $clienteselec = $data->notaventa->cliente()->get();
        //session(['aux_aprocot' => '0']);
        //dd($clienteselec[0]->rut);

        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();

        $clientesArray = Cliente::clientesxUsuario($vendedor_id);
        $clientes = $clientesArray['clientes'];
        //$vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        //dd($sucurArray);
        //Aqui si estoy filtrando solo las categorias de asignadas al usuario logueado
        //******************* */
        $clientedirecs = Cliente::where('rut', $clienteselec[0]->rut)
        ->join('clientedirec', 'cliente.id', '=', 'clientedirec.cliente_id')
        ->join('cliente_sucursal', 'cliente.id', '=', 'cliente_sucursal.cliente_id')
        ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->select([
                    'cliente.id as cliente_id',
                    'cliente.razonsocial',
                    'cliente.telefono',
                    'cliente.email',
                    'cliente.regionp_id',
                    'cliente.provinciap_id',
                    'cliente.comunap_id',
                    'cliente.contactonombre',
                    'cliente.direccion',
                    'clientedirec.id',
                    'clientedirec.direcciondetalle'
                ])->get();
        //dd($clientedirecs);
        $clienteDirec = $data->notaventa->clientedirec()->get();
        $fecha = date("d/m/Y", strtotime($data->fechahora));
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $productos = Producto::productosxUsuario();

        $vendedores1 = Usuario::join('sucursal_usuario', function ($join) {
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
            ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
                    })
            ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
            ->join('vendedor', function ($join) {
                $join->on('persona.id', '=', 'vendedor.persona_id')
                    ->where('vendedor.sta_activo', '=', 1);
            })
            ->select([
                'vendedor.id',
                'persona.nombre',
                'persona.apellido'
            ])
            ->get();

        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $giros = Giro::orderBy('id')->get();
        $despachoobss = DespachoObs::orderBy('id')->get();
        $aux_sta=2;
        $aux_statusPant = 0;

        return view('despachoord.editar', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','despachoobss','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id'));
  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarDespachoOrd $request, $id)
    {
        can('guardar-orden-despacho');
        $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$request->notaventa_id)->get();
        //dd($notaventacerrada);
        if(count($notaventacerrada) == 0){
            //dd($request);
            $dateInput = explode('/',$request->plazoentrega);
            $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
            $dateInput = explode('/',$request->fechaestdesp);
            $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
            $despachoord = DespachoOrd::findOrFail($id);
            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$despachoord->notaventa->cliente_id)->get();
            if(count($clibloq) > 0){
                return redirect('despachoord')->with([
                    'mensaje'=>'Registro no fue guardado. Cliente Bloqueado: ' . $clibloq[0]->descripcion ,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            if($despachoord->updated_at == $request->updated_at){
                $despachoord->updated_at = date("Y-m-d H:i:s");
                $despachoord->comunaentrega_id = $request->comunaentrega_id;
                $despachoord->tipoentrega_id = $request->tipoentrega_id;
                $despachoord->plazoentrega = $request->plazoentrega;
                $despachoord->lugarentrega = $request->lugarentrega;
                $despachoord->contacto = $request->contacto;
                $despachoord->contactoemail = $request->contactoemail;
                $despachoord->contactotelf = $request->contactotelf;
                $despachoord->observacion = $request->observacion;
                $despachoord->fechaestdesp = $request->fechaestdesp;
                $despachoord->despachoobs_id = $request->despachoobs_id;
                //dd($request);
                if($despachoord->save()){
                    $cont_producto = count($request->producto_id);
                    if($cont_producto>0){
                        for ($i=0; $i < $cont_producto ; $i++){
                            if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                                $despachoorddet = DespachoOrdDet::findOrFail($request->NVdet_id[$i]);
                                $despachoorddet->cantdesp = $request->cantord[$i];
                                if($despachoorddet->save()){
                                    if($request->cantord[$i]==0){
                                        $despachoorddet->usuariodel_id = auth()->id();
                                        $despachoorddet->save();
                                        DB::table('despachoorddet_invbodegaproducto')->where('despachoorddet_id', $despachoorddet->id)->delete();
                                        $despachoorddet->delete();
                                    }else{
                                        $cont_bodegas = count($request->invcant);
                                        if($cont_bodegas>0){
                                            for ($b=0; $b < $cont_bodegas ; $b++){
                                                if($request->invbodegaproducto_producto_id[$b] == $request->producto_id[$i]){
                                                    DB::table('despachoorddet_invbodegaproducto')->updateOrInsert(
                                                        ['despachoorddet_id' => $request->NVdet_id[$i], 'invbodegaproducto_id' => $request->invbodegaproducto_id[$b]],
                                                        [
                                                            'cant' => $request->invcant[$b] * -1
                                                        ]
                                                    );
                                                }
                                            }
                                        }
                                    }

                                    /*
                                    $notaventadetalle = NotaVentaDetalle::findOrFail($despachosoldet->notaventadetalle_id);
                                    $notaventadetalle->cantsoldesp = $request->cantsoldesp[$i];
                                    $notaventadetalle->save();
                                    */
                                    //$despacho_id = $despachosol->id;    
                                }
                            }
                        }
                    }
                }
                return redirect('despachoord')->with([
                                                            'mensaje'=>'Registro actualizado con exito.',
                                                            'tipo_alert' => 'alert-success'
                                                        ]);
            }else{
                return redirect('despachoord')->with([
                    'mensaje'=>'Registro no fue modificado. Registro Editado por otro usuario. Fecha Hora: '.$despachoord->updated_at,
                                                            'tipo_alert' => 'alert-error'
                                                        ]);
            }
        }else{
            return redirect('despachoord')->with([
                'mensaje'=>'Registro no fue Modificado. La nota de venta fue Cerrada. Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at)),
                'tipo_alert' => 'alert-error'
            ]);
        }


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

    public function anular(Request $request)
    {
        if ($request->ajax()) {
            $despachoord = DespachoOrd::findOrFail($request->id);
            if(empty($despachoord->guiadespacho) and empty($despachoord->numfactura)){
                $despachoordanul = new DespachoOrdAnul();
                $despachoordanul->despachoord_id = $request->id;
                $despachoordanul->usuario_id = auth()->id();
                if ($despachoordanul->save()) {
                    //return response()->json(['mensaje' => 'ok']);
                    return response()->json([
                        'error'=>'1',
                        'mensaje'=>'Registro anulado con exito.',
                        'tipo_alert' => 'success'
                    ]);
                } else {
                    //return response()->json(['mensaje' => 'ng']);
                    return response()->json([
                        'error'=>'0',
                        'mensaje'=>'Registro No fue anulado. Error al intentar modificar el registro.',
                        'tipo_alert' => 'error'
                    ]);
                }
            }else{
                //return response()->json(['mensaje' => 'guidesp_factura']);
                return response()->json([
                    'error'=>'0',
                    'mensaje'=>'Registro no fue anulado. Ya tiene asignado Guia despacho o Factura',
                    'tipo_alert' => 'error'
                ]);

            }
        } else {
            abort(404);
        }
    }

    public function guardarguiadesp(Request $request)
    {
        if ($request->ajax()) {
            $despachoord = DespachoOrd::where('guiadespacho','=',$request->guiadespacho)->get();
            $aux_contgdesp = count($despachoord);
            if($aux_contgdesp>0){
                return response()->json(['mensaje' => 'dup']);
            }else{
                $despachoord = DespachoOrd::findOrFail($request->id);
                $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$despachoord->notaventa_id)->get();
                if(count($notaventacerrada) == 0){
                    if($request->updatesolonumguia){
                        return updatenumguia($despachoord,$request);
                    }else{
                        $aux_bandera = true;
                        foreach ($despachoord->despachoorddets as $despachoorddet) {
                            //dd($despachoorddet->despachoorddet_invbodegaproductos);
                            $aux_respuesta = InvBodegaProducto::validarExistenciaStock($despachoorddet->despachoorddet_invbodegaproductos,$request->invbodega_id);
                            if($aux_respuesta["bandera"] == false){
                                $aux_bandera = $aux_respuesta["bandera"];
                                break;
                            }
                        }
                        if($aux_bandera){
                            $invmov_array = array();
                            $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                            $invmov_array["annomes"] = $aux_respuesta["annomes"];
                            $invmov_array["desc"] = "Salida de Bodega de Despacho, ya en poder del cliente / Orden Despacho Nro: " . $request->id;
                            $invmov_array["obs"] = "Salida de Bodega de Despacho, ya en poder del cliente / Orden Despacho Nro: " . $request->id;
                            $invmov_array["invmovmodulo_id"] = 3; //Guia de Despacho
                            $invmov_array["idmovmod"] = $request->guiadespacho;
                            $invmov_array["invmovtipo_id"] = 2;
                            $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
                            $invmov_array["usuario_id"] = auth()->id();
                            $arrayinvmov_id = array();
                            
                            $invmov = InvMov::create($invmov_array);
                            array_push($arrayinvmov_id, $invmov->id);
                            foreach ($despachoord->despachoorddets as $despachoorddet) {
                                foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                                    $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                                        ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $request->invbodega_id],
                                        [
                                            'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                                            'invbodega_id' => $request->invbodega_id
                                        ]
                                    );
            
                                    $array_invmovdet = $oddetbodprod->attributesToArray();
                                    $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                                    $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                                    $array_invmovdet["invbodega_id"] = $request->invbodega_id;
                                    $array_invmovdet["sucursal_id"] = $invbodegaproducto->invbodega->sucursal_id;
                                    $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                                    $array_invmovdet["invmovtipo_id"] = 2;
                                    $array_invmovdet["invmov_id"] = $invmov->id;
                                    $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                                    $array_invmovdet["cantxgrupo"] = 1;
                                    $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                                    $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                                    $invmovdet = InvMovDet::create($array_invmovdet);                              
                                }
                            }
    
                            return updatenumguia($despachoord,$request);
                            /*
                            $despachoord->guiadespacho = $request->guiadespacho;
                            $despachoord->guiadespachofec = date("Y-m-d H:i:s");
                            if ($despachoord->save()) {
                                Event(new GuardarGuiaDespacho($despachoord));
                                return response()->json([
                                                        'mensaje' => 'ok',
                                                        'despachoord' => $despachoord,
                                                        'guiadespachofec' => date("Y-m-d", strtotime($despachoord->guiadespachofec)),
                                                        'id' => $request->id,
                                                        'nfila' => $request->nfila,
                                                        ]);
                            } else {
                                return response()->json(['mensaje' => 'ng']);
                            }
                            */
                        }else{
                            return response()->json([
                                'mensaje' => "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"],
                                'menper' => "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"]
                            ]);
                        }
                    }

                    /** ******** */
                    /*
                    $despachoord->guiadespacho = $request->guiadespacho;
                    $despachoord->guiadespachofec = date("Y-m-d H:i:s");
                    if ($despachoord->save()) {
                        Event(new GuardarGuiaDespacho($despachoord));
                        return response()->json([
                                                'mensaje' => 'ok',
                                                'despachoord' => $despachoord,
                                                'guiadespachofec' => date("Y-m-d", strtotime($despachoord->guiadespachofec))
                                                ]);
                    } else {
                        return response()->json(['mensaje' => 'ng']);
                    } 
                    */                       
                }else{
                    $mensaje = 'Nota Venta fue cerrada: Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at));
                    return response()->json(['mensaje' => $mensaje]);
                }
            }
        } else {
            abort(404);
        }    
    }

    public function guardarfactdesp(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            $despachoord = DespachoOrd::findOrFail($request->id);
            $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$despachoord->notaventa_id)->get();
            if(count($notaventacerrada) == 0){
                $despachoord->numfactura = $request->numfactura;
                $dateInput = explode('/',$request->fechafactura);
                $despachoord->fechafactura = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
                $despachoord->numfacturafec = date("Y-m-d H:i:s");
                if ($despachoord->save()) {
                    Event(new GuardarFacturaDespacho($despachoord));
                    return response()->json([
                                            'mensaje' => 'ok',
                                            'despachoord' => $despachoord
                                            ]);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }    
            }else{
                $mensaje = 'Nota Venta fue cerrada: Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at));
                return response()->json(['mensaje' => $mensaje]);
            }
        } else {
            abort(404);
        }
    }
    
    public function consultarod(Request $request)
    {
        if ($request->ajax()) {
            $despachoord = DespachoOrd::findOrFail($request->id);
            if ($despachoord) {
                return response()->json([
                                        'mensaje' => 'ok',
                                        'despachoord' => $despachoord,
                                        'fechafactura' => date("d/m/Y", strtotime($despachoord->fechafactura))
                                        ]);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function aproborddesp(Request $request)
    {
        if ($request->ajax()) {
            $despachoord = DespachoOrd::findOrFail($request->id);
            $aux_bandera = true;
            foreach ($despachoord->despachoorddets as $despachoorddet) {
                $aux_respuesta = InvBodegaProducto::validarExistenciaStock($despachoorddet->despachoorddet_invbodegaproductos);
                if($aux_respuesta["bandera"] == false){
                    $aux_bandera = $aux_respuesta["bandera"];
                    break;
                }
            }
            if($aux_bandera){
                $invmov_array = array();
                $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                $invmov_array["annomes"] = $aux_respuesta["annomes"];
                $invmov_array["desc"] = "Salida de Bodega Nro OD: " . $request->id;
                $invmov_array["obs"] = "Salida de Bodega por aprobacion de Orden de despacho Nro OD: " . $request->id;
                $invmov_array["invmovmodulo_id"] = 2; //Modulo Orden Despacho
                $invmov_array["idmovmod"] = $request->id;
                $invmov_array["invmovtipo_id"] = 2;
                $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
                $invmov_array["usuario_id"] = auth()->id();
                $arrayinvmov_id = array();
                
                $invmov = InvMov::create($invmov_array);
                array_push($arrayinvmov_id, $invmov->id);
                foreach ($despachoord->despachoorddets as $despachoorddet) {
                    foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                        $array_invmovdet = $oddetbodprod->attributesToArray();
                        $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                        $array_invmovdet["invbodega_id"] = $oddetbodprod->invbodegaproducto->invbodega_id;
                        $array_invmovdet["sucursal_id"] = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id;
                        $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                        $array_invmovdet["invmovtipo_id"] = 2;
                        $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                        $array_invmovdet["cantxgrupo"] = 1;
                        $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                        $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                        $array_invmovdet["invmov_id"] = $invmov->id;
                        $invmovdet = InvMovDet::create($array_invmovdet);                                
                    }
                }
                $invmov_array = array();
                $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                $invmov_array["annomes"] = $aux_respuesta["annomes"];
                $invmov_array["desc"] = "Entrada a Bodega despacho Nro: " . $request->id;
                $invmov_array["obs"] = "Entrada a Bodega despacho por aprobacion de Orden de despacho Nro: " . $request->id;
                $invmov_array["invmovmodulo_id"] = 2; //Modulo Orden Despacho
                $invmov_array["idmovmod"] = $request->id;
                $invmov_array["invmovtipo_id"] = 1;
                $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
                $invmov_array["usuario_id"] = auth()->id();
                
                $invmov = InvMov::create($invmov_array);
                array_push($arrayinvmov_id, $invmov->id);
                foreach ($despachoord->despachoorddets as $despachoorddet) {
                    foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                        $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                            ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $request->invbodega_id],
                            [
                                'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                                'invbodega_id' => $request->invbodega_id
                            ]
                        );
                        $array_invmovdet = $oddetbodprod->attributesToArray();
                        $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                        $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                        $array_invmovdet["invbodega_id"] = $request->invbodega_id;
                        $array_invmovdet["sucursal_id"] = $invbodegaproducto->invbodega->sucursal_id;
                        $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                        $array_invmovdet["invmovtipo_id"] = 1;
                        $array_invmovdet["cant"] = $array_invmovdet["cant"] * -1;
                        $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                        $array_invmovdet["cantxgrupo"] = 1;
                        $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                        $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                        $array_invmovdet["invmov_id"] = $invmov->id;
                        $invmovdet = InvMovDet::create($array_invmovdet);                                
                    }
                }
                $despachoord->aprguiadesp = 1;
                $despachoord->aprguiadespfh = date("Y-m-d H:i:s");
                if ($despachoord->save()) {
                    //$despachoord->invmovs()->sync($arrayinvmov_id);
                    $despachoord_invmov = DespachoOrd_InvMov::create([
                            'despachoord_id' => $despachoord->id,
                            'invmov_id' => $arrayinvmov_id[0]
                        ]);
                    $despachoord_invmov = DespachoOrd_InvMov::create(
                        [
                            'despachoord_id' => $despachoord->id,
                            'invmov_id' => $arrayinvmov_id[1]
                        ]);
                    $aux_usuariodestino_id = NULL;
                    if($despachoord->notaventa->vendedor->persona->usuario){
                        $aux_usuariodestino_id = $despachoord->notaventa->vendedor->persona->usuario->id;
                    }
                    Event(new Notificacion( //ENVIO ARRAY CON LOS DATOS PARA CREAR LA NOTIFICACION
                        [
                            'usuarioorigen_id' => auth()->id(),
                            'usuariodestino_id' => $aux_usuariodestino_id,
                            'vendedor_id' => $despachoord->notaventa->vendedor_id,
                            'status' => 1,
                            'nombretabla' => 'despachoord',
                            'mensaje' => 'Nueva Orden Despacho Nro:'.$despachoord->id,
                            'rutadestino' => 'notaventaconsulta',
                            'tabla_id' => $despachoord->id,
                            'accion' => 'Nueva Orden Despacho',
                            'mensajetitle' => 'OD:'.$despachoord->id.' NV:'.$despachoord->notaventa_id,
                            'icono' => 'fa fa-fw fa-male text-primary',
                            'detalle' => "
                                <p><b>Datos:</b></p>
                                <ul>
                                    <li><b>Nro. Nota Venta: </b> $despachoord->notaventa_id </li>
                                    <li><b>Nro. Orden Despacho: </b> $despachoord->id </li>
                                    <li><b>RUT:</b> " . $despachoord->notaventa->cliente->rut . "</li>
                                    <li><b>Razon Social:</b> " . $despachoord->notaventa->cliente->razonsocial . "</li>
                                    <li><b>Vendedor:</b> " . $despachoord->notaventa->vendedor->persona->nombre . " " . $despachoord->notaventa->vendedor->persona->apellido . "</li>
                                </ul>                    
                            "
                        ]
                    ));
                    return response()->json([
                                            'mensaje' => 'ok',
                                            'id' => $request->id,
                                            'nfila' => $request->nfila,
                                        ]);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            }else{
                return response()->json([
                    'mensaje' => 'MensajePersonalizado',
                    'menper' => "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"]
                ]);
            }
        } else {
            abort(404);
        }
    }

    public function reporte(Request $request){
        //$respuesta = reportesol($request);
        //return $respuesta;
    }

    public function exportPdf($id,$stareport = '1')
    {
        if(can('ver-pdf-orden-de-despacho',false)){
            $despachoord = DespachoOrd::findOrFail($id);
            //dd($despachoord);
            $despachoorddets = $despachoord->despachoorddets()->get();
            //dd($despachoorddets);
            $empresa = Empresa::orderBy('id')->get();
            $rut = number_format( substr ( $despachoord->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachoord->notaventa->cliente->rut, strlen($despachoord->notaventa->cliente->rut) -1 , 1 );
            //dd($empresa[0]['iva']);
            if($stareport == '1'){
                if(env('APP_DEBUG')){
                    return view('despachoord.reporte', compact('despachoord','despachoorddets','empresa'));
                }
                $pdf = PDF::loadView('despachoord.reporte', compact('despachoord','despachoorddets','empresa'));
                //return $pdf->download('cotizacion.pdf');
                return $pdf->stream(str_pad($despachoord->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachoord->notaventa->cliente->razonsocial . '.pdf');
            }else{
                if($stareport == '2'){
                    return view('despachoord.listado1', compact('despachoord','despachoorddets','empresa'));        
                    $pdf = PDF::loadView('despachoord.listado1', compact('despachoord','despachoorddets','empresa'));
                    //return $pdf->download('cotizacion.pdf');
                    return $pdf->stream(str_pad($despachoord->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachoord->notaventa->cliente->razonsocial . '.pdf');
                }
            }
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
    }

    public function listarorddespxnv(Request $request){
        $respuesta = array();
		$respuesta['exito'] = false;
		$respuesta['mensaje'] = "Código no Existe";
		$respuesta['tabla'] = "";
        if($request->ajax()){
            $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
            $despchoords = DespachoOrd::orderBy('id')
                    ->where('notaventa_id','=',$request->id)
                    ->whereNotNull('numfactura')
                    ->whereNotIn('id',  $despachoordanul)
                    ->get();
            $respuesta['tabla'] .= "<table id='tabladespachoorddet' name='tabladespachoorddet' class='table display AllDataTables table-hover table-condensed' data-page-length='10'>
            <thead>
                <tr>
                    <th>ID OD</th>
                    <th>Fecha</th>
                    <th style='text-align:right'>Solic</th>
                    <th style='text-align:right'>Entregado</th>
                    <th class='textcenter'>Unidad</th>
					<th class='textleft'>Descripción</th>
					<th class='textleft'>Diametro</th>
					<th class='textleft'>Clase</th>
					<th class='textright'>Largo</th>
                    <th class='textcenter'>TU</th>
                    <th class='textcenter'>Peso</th>
                    <th class='textcenter'>Guia</th>
                    <th class='textcenter'>FecFact</th>
                    <th class='textcenter'>Nfact</th>
                </tr>
            </thead>
            <tbody>";
            $i=0;
            $aux_totalcantsoldesp = 0;
            $aux_totalcantdesp = 0;
            foreach ($despchoords as $despchoord) {
                foreach ($despchoord->despachoorddets as $despachoorddet) {
                    //dd($despachoorddet);
                    //Si codigo producto es nulo muestra todo, de lo contrario solo muestra el producto igual a producto_id
                    if($request->producto_id != null){
                        if($request->producto_id != $despachoorddet->notaventadetalle->producto_id){
                            continue;
                        }
                    }
                    $i++;
                    $unidades = $despachoorddet->notaventadetalle->producto->categoriaprod->unidadmedidafact->nombre;
                    $nombreproduc = $despachoorddet->notaventadetalle->producto->nombre;
                    $diametro = $despachoorddet->notaventadetalle->producto->diametro;
                    /*
                    $diametro = $despachoorddet->notaventadetalle->producto->diamextpg;
                    if ($despachoorddet->notaventadetalle->producto->categoriaprod->unidadmedida_id != 3){
                        $diametro = $despachoorddet->notaventadetalle->producto->diamextmm . 'mm';
                    }
                    */
                    $cla_nombre = $despachoorddet->notaventadetalle->producto->claseprod->cla_nombre;
                    $long = $despachoorddet->notaventadetalle->producto->long;
                    $tipounion = $despachoorddet->notaventadetalle->producto->tipounion;
                    $cantsoldesp = $despachoorddet->despachosoldet->cantsoldesp;
                    $peso = $despachoorddet->notaventadetalle->peso;
                    $tablaOrdTrab= "";
                    $aux_botonMostrar = "";
                    if(count($despachoorddet->despachoordrecdets) > 0){
                        //dd($despachoorddet->despachoordrecdets);
                        $tablaOrdTrab .= "
                            <table class='table display AllDataTables table-hover table-condensed' data-page-length='10'>
                            <thead>
                                <tr>
                                    <th style='text-align:right' class='btn-accion-tabla btn-sm tooltipsC' title='Id Rechazo'>ID</th>
                                    <th style='text-align:right' class='btn-accion-tabla btn-sm tooltipsC' title='Cant Rechazada'>Cant</th>
                                </tr>
                            </thead>
                            <tbody>";
                            $aux_saldoentregado = $despachoorddet->cantdesp;
                            foreach ($despachoorddet->despachoordrecdets as $despachoordrecdet){
                                //dd($despachoordrecdet->cantrec);
                                $tablaOrdTrab .= "
                                    <tr>
                                        <td>
                                            <a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Rechazo OD' onclick='genpdfODRec($despachoordrecdet->id,1)'>
                                                $despachoordrecdet->id
                                            </a>
                                        </td>
                                        <td style='text-align:right'>$despachoordrecdet->cantrec</td>
                                    </tr>";
                                $aux_saldoentregado -= $despachoordrecdet->cantrec;
                            }
                            $tablaOrdTrab .= "
                            </tbody>
                                <tfoot>
                                    <tr>
                                        <th style='text-align:right'></th>
                                        <th style='text-align:right' class='btn-accion-tabla btn-sm tooltipsC' title='Entregado' >". number_format($aux_saldoentregado, 0, ",", ".") ."</th>
                                    </tr>
                                </tfoot>        
                            </table>";
                            $aux_botonMostrar = "
                                <a class='btn-accion-tabla btn-sm tooltipsC' title='Rechazo' onclick='mostrarH($i,\"botonD\",\"divTabOT\")'>
                                    <i name='botonD$i' id='botonD$i' class='fa fa-fw fa-caret-down'></i>
                                </a>";
                    }
//                    $tablaOrdTrab= "";
                    $respuesta['tabla'] .= "
                    <tr id='fila$i' name='fila$i'>
                        <td id='id$i' name='id$i'>
                            <a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden de Despacho' onclick='genpdfOD($despachoorddet->despachoord_id,1)'>
                                $despachoorddet->despachoord_id
                            </a>                            
                        </td>
                        <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($despachoorddet->created_at)) . "</td>
                        <td style='text-align:right'>". number_format($cantsoldesp, 0, ",", ".") ."</td>
                        <td style='text-align:right'>" . $aux_botonMostrar . number_format($despachoorddet->cantdesp, 0, ",", ".") .
                            "<div id='divTabOT$i' style='display:none;'>
                                    $tablaOrdTrab
                            </div>
                        </td>
                        <td class='textcenter'>$unidades</td>
						<td class='textleft'>$nombreproduc</td>
                        <td class='textleft'>$diametro</td>
                        <td class='textleft'>$cla_nombre</td>
						<td class='textright'>$long mts</td>
                        <td class='textcenter'>$tipounion</td>
                        <td class='textcenter'>$peso</td>
                        <td class='textcenter'>" . $despachoorddet->despachoord->guiadespacho ." </td>
                        <td class='textcenter'>" . date('d-m-Y', strtotime($despachoorddet->despachoord->fechafactura)) . "</td>
                        <td class='textcenter'>" . $despachoorddet->despachoord->numfactura . "</td>    
                    </tr>";

                    $respuesta['exito'] = true;
                    $aux_totalcantsoldesp += $cantsoldesp;
                    $aux_totalcantdesp += $despachoorddet->cantdesp;
                }
            }
            $respuesta['tabla'] .= "
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan='2' style='text-align:left'>TOTALES</th>
                        <th style='text-align:right'></th>
                        <th style='text-align:right'>". number_format($aux_totalcantdesp, 0, ",", ".") ."</th>
                        <th colspan='10' style='text-align:right'></th>
                    </tr>
                </tfoot>
                </table>";
        }
        return $respuesta;
    }

    public function buscarguiadesp(Request $request)
    {
        if ($request->ajax()) {
            $despachoord = DespachoOrd::where('guiadespacho' ,'=',$request->guiadespacho)->get();
            if(count($despachoord) > 0){
                return response()->json(['mensaje' => 'ok',
                'Mensaje' => 'Encontrado'
               ]);
            }else{
                return response()->json(['mensaje' => 'no',
                'Mensaje' => 'No existe.'
               ]);
            }
        }
    }

    public function totalizarindex(){
        $respuesta = array();
        $datas = consultaindex();
        $aux_totalkg = 0;
        //$aux_totaldinero = 0;
        foreach ($datas as $data) {
            $aux_totalkg += $data->aux_totalkg;
            //$aux_totaldinero += $data->subtotal;
        }
        $respuesta['aux_totalkg'] = $aux_totalkg;
        //$respuesta['aux_totaldinero'] = $aux_totaldinero;
        return $respuesta;
    }

}


function consulta($request){
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
        $aux_condFecha = "despachosol.fechahora>='$fechad' and despachosol.fechahora<='$fechah'";
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

    $aux_condaprobord = "true";
    switch ($request->filtro) {
        case 1:
            //Filtra solo las aprobadas. Esto es para la consulta para crear ordenes de Despacho
            $aux_condaprobord = "despachosol.aprorddesp = 1";
            break;
        case 2:
            //Muestra todo sin importar si fue aprobadada o no. Esto es para el reporte
            $aux_condaprobord = "true";
            break;
    }

    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);

    $sql = "SELECT despachosol.id,despachosol.fechahora,cliente.rut,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,
            comuna.nombre as comunanombre,
            despachosol.notaventa_id,despachosol.fechaestdesp,
            sum(despachosoldet.cantsoldesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) AS totalkilos
            FROM despachosol INNER JOIN despachosoldet
            ON despachosol.id=despachosoldet.despachosol_id
            AND if((SELECT cantdesp
                    FROM vista_sumorddespdet
                    WHERE despachosoldet_id=despachosoldet.id
                    ) >= despachosoldet.cantsoldesp,FALSE,TRUE)
            INNER JOIN notaventa
            ON notaventa.id=despachosol.notaventa_id
            INNER JOIN notaventadetalle
            ON despachosoldet.notaventadetalle_id=notaventadetalle.id
            INNER JOIN producto
            ON notaventadetalle.producto_id=producto.id
            INNER JOIN categoriaprod
            ON categoriaprod.id=producto.categoriaprod_id
            INNER JOIN areaproduccion
            ON areaproduccion.id=categoriaprod.areaproduccion_id
            INNER JOIN cliente
            ON cliente.id=notaventa.cliente_id
            INNER JOIN comuna
            ON comuna.id=despachosol.comunaentrega_id
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
            and $aux_condaprobord
            and despachosol.deleted_at is null AND notaventa.deleted_at is null AND notaventadetalle.deleted_at is null
            GROUP BY despachosol.id;";
/*
(select sum(cantsoldesp) as cantsoldesp
                    from despachosol inner join despachosoldet
                    on despachosol.id=despachosoldet.despachosol_id
                    where despachosol.id not in (select despachosol_id from despachosolanul)
                    and despachosoldet.notaventadetalle_id=notaventadetalle.id
                    despachosol.deleted_at is null
                    group by notaventadetalle_id)
*/
    //dd("$sql");
    $datas = DB::select($sql);
    //dd($datas);
    return $datas;
}


function consultaindex(){

    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,despachoord.fechaestdesp,
    cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
    '' as notaventaxk,comuna.nombre as comuna_nombre,
    tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
    SUM(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg
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
    WHERE ISNULL(despachoord.aprguiadesp)
    AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
    AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
    AND notaventa.sucursal_id in ($sucurcadena)
    GROUP BY despachoorddet.despachoord_id;";

    return DB::select($sql);

}

function updatenumguia($despachoord,$request){
    $despachoord->guiadespacho = $request->guiadespacho;
    $despachoord->guiadespachofec = date("Y-m-d H:i:s");
    if ($despachoord->save()) {
        Event(new GuardarGuiaDespacho($despachoord));
        return response()->json([
                                'mensaje' => 'ok',
                                'despachoord' => $despachoord,
                                'guiadespachofec' => date("Y-m-d", strtotime($despachoord->guiadespachofec)),
                                'status' => '0',
                                'id' => $request->id,
                                'nfila' => $request->nfila,
                                ]);
    } else {
        return response()->json(['mensaje' => 'ng']);
    }

}