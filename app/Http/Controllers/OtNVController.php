<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarOt;
use App\Models\AreaProduccion;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\InvMovModulo;
use App\Models\NotaVenta;
use App\Models\NotaVentaCerrada;
use App\Models\NotaVentaDetalle;
use App\Models\Ot;
use App\Models\OtAnul;
use App\Models\OtDet;
use App\Models\OtDetNVDet;
use App\Models\OtNotaVenta;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OtNVController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-otnv');
        //$datas = FormaPago::orderBy('id')->get();
        return view('otnv.index');
    }

    public function otnvpage(){
        $datas = consultaindex();
        return datatables($datas)->toJson();
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
        return view('otnv.listarnotaventa', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml','miVariableGlobal'));
    }

    public function listarnvpage(Request $request){
        $request->merge(['aprobstatus' => "3"]);
        $datas = consulta($request,1,1);
        return datatables($datas)->toJson();
    }
    public function totalizarlistarnvpage(Request $request){
        $respuesta = array();
        $datas = consulta($request,1,1);
        $aux_kgpend = 0;
        $aux_dinpend = 0;
        //$aux_totaldinero = 0;
        foreach ($datas as $data) {
            $aux_kgpend += $data->totalkilos - $data->totalkgsoldesp;
            $aux_dinpend += $data->subtotal - $data->totalsubtotalsoldesp;
        }
        $respuesta['aux_kgpend'] = $aux_kgpend;
        $respuesta['aux_dinpend'] = $aux_dinpend;
        //$respuesta['aux_totaldinero'] = $aux_totaldinero;
        return $respuesta;
    }

    public function crearot($id,$updatednum_at)
    {
        can('crear-otnv');
        $notaventa = NotaVenta::find($id);
        if (!$notaventa) {
            return redirect('otnv/listarnv')->with([
                'mensaje' => "No se encontró la Nota de Venta #$id.",
                'tipo_alert' => 'alert-error'
            ]);
        }
        if(strtotime($notaventa->updated_at) != $updatednum_at){
            return redirect('otnv/listarnv')->with([
                'mensaje'=>'Registro no fue creado. Registro Editado por otro usuario. Fecha Hora: '.$notaventa->updated_at,
                'tipo_alert' => 'alert-error'
            ]);
        }

        if(isset($notaventa->otnotaventa->ot_id)){
            $ot = $notaventa->otnotaventa->ot;
            return redirect('otnv')->with([
                'mensaje'=>'Registro no fue creado. OT ya fue generada OT:' . $ot->id . ' por otro usuario: ' . $ot->usuario->nombre,
                'tipo_alert' => 'alert-error'
            ]);
        }

        if(isset($data->cliente->clientebloqueado->descripcion)){
            return redirect('despachosol')->with([
                'mensaje'=>'Condición financiera en revisión: ' . $data->cliente->clientebloqueado->descripcion . ". Razon Social: " . $data->cliente->razonsocial,
                'tipo_alert' => 'alert-error'
            ]);    
        }

        foreach ($notaventa->despachosolsVigentes as $despachosol) {
            if(is_null($despachosol->aprorddesp)){
                return redirect('otnv/listarnv')->with([
                    'mensaje'=>'Nota de venta tiene una solicitud de despacho pendiente por ser aprobada. Solicitud despacho Nro.'.$despachosol->id,
                    'tipo_alert' => 'alert-error'
                ]);    
            }
        }

        $request1 = new Request();
        $request1->merge(['modulo_id' => 32]);
        $request1->request->set('modulo_id', 32);
        $request1->merge(['notaventa_id' => $notaventa->id]);
        $request1->request->set('notaventa_id', $notaventa->id);
        $request1->merge(['deldesbloqueo' => 0]);
        $request1->request->set('deldesbloqueo', 0);
        $clibloq = clienteBloqueado($notaventa->cliente_id,0,$request1);
        if(!is_null($clibloq["bloqueo"])){
            return redirect('otnv/listarnv')->with([
                "mensaje" => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                "tipo_alert" => "alert-error"
            ]);
        }


        $notaventa->plazoentrega = $newDate = date("d/m/Y", strtotime($notaventa->plazoentrega));
        $detalles = $notaventa->notaventadetalles()->get();
        $clienteselec = $notaventa->cliente()->get();

        $clienteDirec = $notaventa->clientedirec()->get();
        $fecha = date("d/m/Y", strtotime($notaventa->fechahora));
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();

        $user = Usuario::find(auth()->id());
        if (!$user) {
            return redirect('otnv/listarnv')->with([
                'mensaje' => "No se encontró el Usuario autenticado (id=" . auth()->id() . ").",
                'tipo_alert' => 'alert-error'
            ]);
        }
        $sucurArrayUsuario = $user->sucursales->pluck('id')->toArray();

        $vendedores1 = Usuario::join('sucursal_usuario', function ($join) use ($sucurArrayUsuario) {
            $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
            ->whereIn('sucursal_usuario.sucursal_id', $sucurArrayUsuario);
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

        $empresa = Empresa::find(1);
        if (!$empresa) {
            return redirect('otnv/listarnv')->with([
                'mensaje' => "No se encontró la Empresa (id=1).",
                'tipo_alert' => 'alert-error'
            ]);
        }
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $giros = Giro::orderBy('id')->get();
        $aux_sta=2;
        $aux_statusPant = 0;
        $invmovmodulo = InvMovModulo::where("cod","=","SOLDESP")->first();
        if (!$invmovmodulo) {
            return redirect('otnv/listarnv')->with([
                'mensaje' => "No se encontró el InvMovModulo con cod=SOLDESP.",
                'tipo_alert' => 'alert-error'
            ]);
        }
        $array_bodegasmodulo = $invmovmodulo->invmovmodulobodsals->pluck('id')->toArray();
        $tablas['sucurArray'] = $sucurArrayUsuario; //$clientesArray['sucurArray'];
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablas['sucurArray'])->get();
        return view('otnv.crear', compact('notaventa','clienteselec','clienteDirec','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant','array_bodegasmodulo','tablas'));
    }

        /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //public function guardar(ValidarOt $request)
    public function guardar(Request $request)
    {
        can('guardar-otnv');
        //dd($request);
        //$request->notaventa_id = "19434";
        $notaventa = NotaVenta::findOrFail($request->notaventa_id);
        //$notaventa = NotaVenta::findOrFail("19434");
        //dd($notaventa->otnotaventa);
        if(isset($notaventa->otnotaventa->ot_id)){
            $ot = $notaventa->otnotaventa->ot;
            return redirect('otnv')->with([
                'mensaje'=>'Registro no fue creado. OT ya fue generada OT:' . $ot->id . ' por otro usuario: ' . $ot->usuario->nombre,
                'tipo_alert' => 'alert-error'
            ]);
        }
        $cont_producto = count($request->producto_id);
        if($cont_producto<=0){
            return redirect('otnv')->with([
                'mensaje'=>'Registro no fue creado. No hay registros en el detalle.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$request->notaventa_id)->get();
        if(count($notaventacerrada) > 0){
            return redirect('otnv')->with([
                'mensaje'=>'Registro no fue creado. La nota de venta fue Cerrada. Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at)),
                'tipo_alert' => 'alert-error'
            ]);
        }
        foreach ($notaventa->cliente->clientebloqueados as $clientebloqueado) {
            return redirect('otnv')->with([
                'id' => 0,
                'mensaje'=>'Registro no fue guardado. Condición financiera en revisión: ' . $clientebloqueado->descripcion,
                'tipo_alert' => 'alert-error'
            ]);
        }

        if(strtotime($notaventa->updated_at) != $request->updatednum_at){
            return redirect('otnv/listarnv')->with([
                'mensaje'=>'Registro no fue creado. Registro Editado por otro usuario. Fecha Hora: '.$notaventa->updated_at,
                'tipo_alert' => 'alert-error'
            ]);    
        }

        foreach ($notaventa->despachosolsVigentes as $despachosol) {
            if(is_null($despachosol->aprorddesp)){
                return redirect('otnv/listarnv')->with([
                    'mensaje'=>'Nota de venta tiene una solicitud de despacho pendiente por ser aprobada. Solicitud despacho Nro.'.$despachosol->id,
                    'tipo_alert' => 'alert-error'
                ]);    
            }
        }

        $request1 = new Request();
        $request1->merge(['modulo_id' => 32]);
        $request1->request->set('modulo_id', 32);
        $request1->merge(['notaventa_id' => $notaventa->id]);
        $request1->request->set('notaventa_id', $notaventa->id);
        $request1->merge(['deldesbloqueo' => 0]);
        $request1->request->set('deldesbloqueo', 0);
        $clibloq = clienteBloqueado($notaventa->cliente_id,0,$request1);
        if(!is_null($clibloq["bloqueo"])){
            return redirect('otnv')->with([
                "mensaje" => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                "tipo_alert" => "alert-error"
            ]);
        }


        $notaventa->updated_at = date("Y-m-d H:i:s");
        $notaventa->save();
        $hoy = date("Y-m-d H:i:s");
        $request->request->add(['fechahora' => $hoy]);
        $request->request->add(['usuario_id' => auth()->id()]);
        $dateInput = explode('/',$request->plazoentrega);
        $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        $dateInput = explode('/',$request->fechaestdesp);
        $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        $ot = Ot::create($request->all());
        if(isset($request->notaventa_id)){
            OtNotaVenta::updateOrCreate(
                ['ot_id' => $ot->id,'notaventa_id' => $request->notaventa_id]
            );    
        }
        if($cont_producto>0){
            for ($i=0; $i < $cont_producto ; $i++){
                $aux_cant = $request->cant[$i];
                if(is_null($request->producto_id[$i])==false && is_null($aux_cant)==false && $aux_cant > 0){
                    $otdet = new OtDet();
                    $producto = Producto::findOrFail($request->producto_id[$i]);
                    $otdet->ot_id = $ot->id;
                    $otdet->producto_id = $request->producto_id[$i];
                    $otdet->cant = $request->cant_saldo[$i];
                    $otdet->unidadmedida_id = $request->unidadmedida_id[$i];
                    //dd($request->unidadmedida_id[$i]);
                    if(isset($producto->acuerdotecnico)){
                        $acuerdotecnico = $producto->acuerdotecnico;
                        $otdet->espesorprod = $acuerdotecnico->at_espesor;    
                    }
                    $otdet->preciounit = $request->preciounit[$i];
                    $otdet->precioxkilo = $request->precioxkilo[$i];
                    $otdet->precioxkiloreal = $request->precioxkiloreal[$i];
                    $otdet->kg = $request->totalkilos[$i];
                    $otdet->kgprod = $request->totalkilos[$i];
                    $otdet->subtotal = $request->subtotal[$i];
                    $otdet->requiere_fabricacion = $request->stafab[$i];
                    $otdet->obs = $request->otdet_obs[$i];
                    //dd($otdet);
                    if($otdet->save()){
                        if(isset($request->NVdet_id[$i])){
                            OtDetNVDet::updateOrCreate(
                                ['otdet_id' => $otdet->id,'notaventadetalle_id' => $request->NVdet_id[$i]]
                            );    
                        } 
                    }
                }
            }
        }
        return redirect('otnv')->with([
            'mensaje'=>'Registro creado con exito.',
            'tipo_alert' => 'alert-success'
        ]);
    }

    public function aprobot(Request $request)
    {
        //dd($request);
        if ($request->ajax()) {
            $ot = Ot::findOrFail($request->id);
            //dd($ot->otdets);
            if($ot == null){
                return response()->json([
                    'id' => 0,
                    'mensaje' => 'Registro fue eliminado previamente.',
                    'tipo_alert' => 'error'
                ]);
            }
            if($request->updated_at != $ot->updated_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            if(isset($ot->otanul)){
                return response()->json([
                    'id' => 0,
                    'mensaje' => 'Registro fue anulado previamente.',
                    'tipo_alert' => 'error'
                ]);
            }

            $request1 = new Request();
            $request1->merge(['modulo_id' => 32]);
            $request1->request->set('modulo_id', 32);
            $request1->merge(['notaventa_id' => $ot->otnotaventa->notaventa_id]);
            $request1->request->set('notaventa_id', $ot->otnotaventa->notaventa_id);
            $request1->merge(['deldesbloqueo' => 1]);
            $request1->request->set('deldesbloqueo', 1);
            $clibloq = clienteBloqueado($ot->otnotaventa->notaventa->cliente_id,0,$request1);
            if(!is_null($clibloq["bloqueo"])){
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                    'tipo_alert' => 'error'
                ]);
            }

            $ot->aprobstatus = 1;
            $ot->aprobfechahora = date("Y-m-d H:i:s");
            $ot->aprobusu_id = auth()->id();

            if ($ot->save()) {
                foreach ($ot->otdets as $otdet) {
                    $notaventadetalle = $otdet->otdetnvdet->notaventadetalle;
                    $notaventadetalle->requiere_fabricacion = $otdet->requiere_fabricacion;
                    $notaventadetalle->save();
                }
                $ot->otnotaventa->notaventa->updated_at = date("Y-m-d H:i:s");
                $ot->otnotaventa->notaventa->save();
    
                return response()->json([
                    'mensaje' => 'ok',
                    'id' => $request->id,
                    'nfila' => $request->nfila,
                ]);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

        /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id,$updatednum_at)
    {
        can('editar-otnv');
        //dd($updatednum_at);
        $ot = Ot::findOrFail($id);
        $notaventa = $ot->otnotaventa->notaventa;

        if(strtotime($ot->updated_at) != $updatednum_at){
            return redirect('otnv')->with([
                'mensaje'=>'Registro no pudo ser editado. Registro Editado por otro usuario. Fecha Hora: '.$ot->updated_at,
                'tipo_alert' => 'alert-error'
            ]);    
        }

        $request1 = new Request();
        $request1->merge(['modulo_id' => 32]);
        $request1->request->set('modulo_id', 32);
        $request1->merge(['notaventa_id' => $ot->otnotaventa->notaventa_id]);
        $request1->request->set('notaventa_id', $ot->otnotaventa->notaventa_id);
        $request1->merge(['deldesbloqueo' => 0]);
        $request1->request->set('deldesbloqueo', 0);
        $clibloq = clienteBloqueado($ot->otnotaventa->notaventa->cliente_id,0,$request1);
        if(!is_null($clibloq["bloqueo"])){
            return redirect('otnv')->with([
                'id' => 0,
                'mensaje' => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                'tipo_alert' => 'alert-error'
            ]);
        }

        //dd($ot);
        $ot->plazoentrega = $newDate = date("d/m/Y", strtotime($ot->plazoentrega));
        $ot->fechaestdesp = $newDate = date("d/m/Y", strtotime($ot->fechaestdesp));
        $detalles = $ot->otdets()->get();
        $clienteselec = $ot->cliente()->get();


        $fecha = date("d/m/Y", strtotime($ot->fechahora));
        $vendedores = Vendedor::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();

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
        $aux_sta=2;
        $aux_statusPant = 0;
        $invmovmodulo = InvMovModulo::where("cod","=","SOLDESP")->get();
        $array_bodegasmodulo = $invmovmodulo[0]->invmovmodulobodsals->pluck('id')->toArray();
        $user = Usuario::findOrFail(auth()->id());
        $tablas['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablas['sucurArray'])->get();

        //dd($clientedirecs);
        return view('otnv.editar', compact('ot','notaventa','clienteselec','detalles','comunas','vendedores','vendedores1','fecha','empresa','sucurArray','aux_sta','aux_cont','aux_statusPant','invmovmodulo','array_bodegasmodulo','tablas'));
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(Request $request, $id)
    {
        can('guardar-otnv');
        //dd($request);
        $ot = Ot::findOrFail($id);

        if(strtotime($ot->updated_at) != $request->updatednum_at){
            return redirect('otnv')->with([
                'mensaje'=>'Registro no pudo ser actualizado. Registro Editado por otro usuario. Fecha Hora: ' . $ot->updated_at,
                'tipo_alert' => 'alert-error'
            ]);    
        }

        if(isset($ot->otanul)){
            return redirect('otnv')->with([
                'mensaje'=>'Registro no pudo ser actualizado. Registro fue Anulado por otro usuario. Fecha Hora: ' . $ot->otanul->updated_at,
                'tipo_alert' => 'alert-error'
            ]);    
        }
        foreach ($ot->cliente->clientebloqueados as $clientebloqueado) {
            return redirect('otnv')->with([
                'id' => 0,
                'mensaje'=>'Registro no fue guardado. Condición financiera en revisión: ' . $clientebloqueado->descripcion,
                'tipo_alert' => 'alert-error'
            ]);
        }

        $dateInput = explode('/',$request->plazoentrega);
        $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        $dateInput = explode('/',$request->fechaestdesp);
        $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        $ot = Ot::findOrFail($id);

        $ot->updated_at = date("Y-m-d H:i:s");
        $ot->sucursal_id = $request->sucursal_id;
        $ot->fechaestdesp = $request->fechaestdesp;
        $ot->obs = $request->obs;
        if($ot->save()){
            $cont_producto = count($request->producto_id);
            if($cont_producto>0){
                for ($i=0; $i < $cont_producto ; $i++){
                    if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                        $notaventadetalle = NotaVentaDetalle::findOrFail($request->NVdet_id[$i]);
                        $notaventadetalle->requiere_fabricacion = $request->stafab[$i];
                        $otdet = OtDet::findOrFail($request->otdet_id[$i]);
                        $otdet->requiere_fabricacion = $request->stafab[$i];
                        $otdet->obs = $request->otdet_obs[$i];                        
                        $otdet->kg = $request->totalkilos[$i];
                        $otdet->kgprod = $request->totalkilos[$i];
                        $notaventadetalle->save();
                        $otdet->save();
                    }
                }
            }
        }
        return redirect('otnv')->with([
                                    'mensaje'=>'Registro actualizado con exito.',
                                    'tipo_alert' => 'alert-success'
                                ]);

    }

    public function procesar(Request $request)
    {
        if ($request->ajax()) {
            $ot = Ot::findOrFail($request->id);
            if(strtotime($ot->updated_at) != $request->updatednum_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro no pudo ser Actualizado. Registro Editado por otro usuario. Fecha Hora: '.$ot->updated_at,
                    'tipo_alert' => 'error'
                ]);    
            }
            $request1 = new Request();
            $request1->merge(['modulo_id' => 32]);
            $request1->request->set('modulo_id', 32);
            $request1->merge(['deldesbloqueo' => 0]);
            $request1->request->set('deldesbloqueo', 0);
            $request1->merge(['notaventa_id' => $ot->otnotaventa->notaventa_id]);
            $request1->request->set('notaventa_id', $ot->otnotaventa->notaventa_id);
            $clibloq = clienteBloqueado($ot->cliente_id,0,$request1);
            if(!is_null($clibloq["bloqueo"])){
                return response()->json([
                    "mensaje" => "Condición financiera en revisión: " . $clibloq["bloqueo"],
                    "tipo_alert" => "error",
                    'id' => 0,
                ]);
            }
            $request1->merge(['deldesbloqueo' => 1]);
            $request1->request->set('deldesbloqueo', 1);
            $clibloq = clienteBloqueado($ot->cliente_id,0,$request1);

            $ot->aprobstatus = 1;
            $ot->obsrechazo = null;
            $ot->aprobusu_id = auth()->id();
            $ot->aprobfechahora = date("Y-m-d H:i:s");
            $ot->updated_at = date("Y-m-d H:i:s");
            if($ot->save()){
                return response()->json([
                                            'mensaje' => 'Registro guardo con exito.',
                                            'status' => '0',
                                            'id' => $ot->id,
                                            'nfila' => $ot->id,
                                            'dte_id' => $ot->id,
                                        ]);
            } else {
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Ocurrio un error al intenta guardar.",
                    'tipo_alert' => 'error'
                ]);
            }
        }
    }


    public function anular(Request $request)
    {
        if ($request->ajax()) {
            $ot = Ot::findOrFail($request->id);
            if(strtotime($ot->updated_at) != $request->updatednum_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro no pudo ser Actualizado. Registro Editado por otro usuario. Fecha Hora: '.$ot->updated_at,
                    'tipo_alert' => 'error'
                ]);    
            }
            if(!isset($request->obs) or $request->obs == "" or $request->obs == null){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'El campo observacion es obligatorio',
                    'tipo_alert' => 'error'
                ]);    
            }

            //dd($request);

            $ot->updated_at = date("Y-m-d H:i:s");
            if($ot->save()){
                $request->request->add(['ot_id' => $ot->id]);
                $request->request->add(['usuario_id' => auth()->id()]);
                $otanul = OtAnul::create($request->all());

                return response()->json([
                                            'mensaje' => 'Registro guardo con exito.',
                                            'status' => '0',
                                            'id' => $ot->id,
                                            'nfila' => $ot->id,
                                            'dte_id' => $ot->id,
                                        ]);
            } else {
                return response()->json([
                    'id' => 0,
                    'mensaje' => "Ocurrio un error al intenta guardar.",
                    'tipo_alert' => 'error'
                ]);
            }
        }
    }

}

function consultaindex(){
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql = "SELECT ot.id,ot.fechahora,otnotaventa.notaventa_id,cliente.razonsocial,
    notaventa.oc_id,notaventa.oc_file,ot.obsrechazo,
    comuna.nombre as comuna_nombre,
    UNIX_TIMESTAMP(ot.updated_at) as updatednum_at,ot.updated_at,
    clientebloqueado.descripcion as clientebloqueado_descripcion,
    if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
    cliente.limitecredito,
    IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
    IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
    IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
    IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
    modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
    clientedesbloqueadomodulo_orddesp.modulo_id as modulo_id_orddesp,
    IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs,
    sum(otdet.kg) as aux_totalkg,
    '' as obsdev, '' as rutaeditar
    FROM ot INNER JOIN otdet
    ON ot.id = otdet.ot_id
    LEFT JOIN otnotaventa
    ON ot.id = otnotaventa.ot_id
    INNER JOIN notaventa
    ON otnotaventa.notaventa_id = notaventa.id AND isnull(notaventa.deleted_at)
    LEFT JOIN otdetnvdet
    ON otdetnvdet.otdet_id = otdet.id
    INNER JOIN cliente
    ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = notaventa.comunaentrega_id AND isnull(comuna.deleted_at)
    LEFT JOIN notaventadetalle
    ON notaventadetalle.id = otdetnvdet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
    LEFT JOIN clientebloqueado
    ON clientebloqueado.cliente_id = ot.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    LEFT JOIN vista_datacobranza
    ON vista_datacobranza.cliente_id = ot.cliente_id
    LEFT JOIN clientedesbloqueado
    ON clientedesbloqueado.cliente_id = notaventa.cliente_id and clientedesbloqueado.notaventa_id = notaventa.id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
    LEFT JOIN clientedesbloqueadomodulo
    ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 32
    LEFT JOIN modulo
    ON modulo.id = clientedesbloqueadomodulo.modulo_id
    LEFT JOIN clientedesbloqueadopro
    ON clientedesbloqueadopro.cliente_id = notaventa.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)

    LEFT JOIN clientedesbloqueado as clientedesbloqueado_orddesp
    ON clientedesbloqueado_orddesp.cliente_id = notaventa.cliente_id and clientedesbloqueado_orddesp.notaventa_id = notaventa.id and not isnull(clientedesbloqueado_orddesp.notaventa_id) and isnull(clientedesbloqueado_orddesp.deleted_at)
    LEFT JOIN clientedesbloqueadomodulo as clientedesbloqueadomodulo_orddesp
    ON clientedesbloqueadomodulo_orddesp.clientedesbloqueado_id = clientedesbloqueado_orddesp.id and clientedesbloqueadomodulo_orddesp.modulo_id = 32

    WHERE  (ot.aprobstatus = 0 or ot.aprobstatus = 3)
    AND ot.id NOT IN (SELECT otanul.ot_id FROM otanul WHERE ISNULL(otanul.deleted_at))
    AND otnotaventa.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
    AND ot.sucursal_id in ($sucurcadena)
    GROUP BY ot.id;";
    //dd($sql);
    $datas = DB::select($sql);
    foreach ($datas as &$data) {
        $data->rutaeditar = route('editar_otnv', ['id' => $data->id,'updatednum_at' => $data->updatednum_at]);
    }
    //dd($datas);
    return $datas;

}

function consulta($request,$aux_sql,$orden){
    //dd($request);
    if($orden==1){
        $aux_orden = "notaventadetalle.notaventa_id desc";
    }else{
        //$aux_orden = "notaventa.cliente_id,notaventa.comunaentrega_id";
        $aux_orden = "cliente.razonsocial,notaventa.comunaentrega_id";
        
    }
    $user = Usuario::findOrFail(auth()->id());
    if(empty($request->vendedor_id)){
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
        if(is_array($request->vendedor_id)){
            $aux_vendedorid = implode ( ',' , $request->vendedor_id);
        }else{
            $aux_vendedorid = $request->vendedor_id;
        }
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";

        //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);
    //dd($sucurcadena);


    if(!isset($request->sucursal_id) or empty($request->sucursal_id)){
        $aux_condsucursal_id = " true ";
    }else{
        if(is_array($request->sucursal_id)){
            $aux_sucursal = implode ( ',' , $request->sucursal_id);
        }else{
            $aux_sucursal = $request->sucursal_id;
        }
        $sucurArray = implode ( ',' , $user->sucursales->pluck('id')->toArray());
        $aux_condsucursal_id = " (notaventa.sucursal_id in ($aux_sucursal) and notaventa.sucursal_id in ($sucurArray))";
    }


    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "notaventa.fechahora>='$fechad' and notaventa.fechahora<='$fechah'";
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
/*
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
    }
*/
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true ";
    }else{
        if(is_array($request->comuna_id)){
            $aux_comuna = implode ( ',' , $request->comuna_id);
        }else{
            $aux_comuna = $request->comuna_id;
        }
        $aux_condcomuna_id = " notaventa.comunaentrega_id in ($aux_comuna) ";
    }


    if(empty($request->plazoentrega)){
        $aux_condplazoentrega = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->plazoentrega);
        $fechad = date_format($fecha, 'Y-m-d');
        $aux_condplazoentrega = "notaventa.plazoentrega='$fechad'";
    }
    //dd($aux_condplazoentrega);

    $aux_condproducto_id = " true";
    if(!empty($request->producto_id)){
        /*
        $aux_condproducto_id = str_replace(".","",$request->producto_id);
        $aux_condproducto_id = str_replace("-","",$aux_condproducto_id);
        $aux_condproducto_id = "notaventadetalle.producto_id='$aux_condproducto_id'";
        */

        $aux_codprod = explode(",", $request->producto_id);
        $aux_codprod = implode ( ',' , $aux_codprod);
        $aux_condproducto_id = "notaventadetalle.producto_id in ($aux_codprod)";
    }

    $aux_condmodulo_id = "";
    if(isset($request->modulo_id)){
        $aux_condmodulo_id = " and clientedesbloqueadomodulo.modulo_id = $request->modulo_id";
    }

    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);
    $arraySucFisxUsu = implode(",", sucFisXUsu($user->persona));
    if($aux_sql==1){
        $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
        comuna.nombre as comunanombre,sucursal.nombre as sucursal_nombre,
        vista_notaventatotales.cant,
        vista_notaventatotales.precioxkilo,
        vista_notaventatotales.totalkilos,
        vista_notaventatotales.subtotal,
        sum(if(areaproduccion.id=1,notaventadetalle.totalkilos,0)) AS pvckg,
        sum(if(areaproduccion.id=2,notaventadetalle.totalkilos,0)) AS cankg,
        sum(if(areaproduccion.id=1,notaventadetalle.subtotal,0)) AS pvcpesos,
        sum(if(areaproduccion.id=2,notaventadetalle.subtotal,0)) AS canpesos,
        sum(notaventadetalle.subtotal) AS totalps,
        (SELECT sum(kgsoldesp) as kgsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id) as totalkgsoldesp,
        (SELECT sum(subtotalsoldesp) as subtotalsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id) as totalsubtotalsoldesp,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho,
        tipoentrega.nombre as tipentnombre,tipoentrega.icono,
        (SELECT CONCAT(dte.nrodocto,';',oc_id,';',oc_folder,'/',oc_file) as nrodocto
            FROM dteoc INNER JOIN dte
            ON dteoc.dte_id = dte.id AND ISNULL(dteoc.deleted_at) AND ISNULL(dte.deleted_at)
            INNER JOIN dteguiadesp
            ON dteoc.dte_id = dteguiadesp.dte_id AND ISNULL(dteguiadesp.deleted_at)
            WHERE dteoc.oc_id = notaventa.oc_id
            AND isnull(dteguiadesp.notaventa_id)
            AND dte.cliente_id= notaventa.cliente_id
            AND dteguiadesp.dte_id NOT IN (SELECT dteanul.dte_id 
                                    FROM dteanul 
                                    WHERE dteanul.dte_id = dteguiadesp.dte_id 
                                    and ISNULL(dteanul.deleted_at))) as dte_nrodocto,
        if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
        '' as rutanuevasoldesp,
        notaventa.aprobfechahora as notaventa_aprobfechahora,
        cliente.limitecredito,
        IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
        IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
        IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
        IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
        clientedesbloqueado.obs as clientedesbloqueado_obs,
        modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
        clientedesbloqueadomodulo_orddesp.modulo_id as modulo_id_orddesp,
        IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs,
        GROUP_CONCAT(
            CONCAT_WS('|', notaventadetalle.producto_id, notaventadetalle.cant, notaventadetalle.preciounit, notaventadetalle.subtotal,if(ISNULL(vista_sumsoldespdet.cantsoldesp),0,vista_sumsoldespdet.cantsoldesp), notaventadetalle.totalkilos, notaventadetalle.requiere_fabricacion,acuerdotecnico.id)
            SEPARATOR ';'
        ) AS nvdetalle,
        SUM(notaventadetalle.requiere_fabricacion) as sumrequiere_fabricacion,
        '' as rutacrear,notaventa.updated_at,
        despachosol.id as despachosol_id
        FROM notaventa INNER JOIN notaventadetalle
        ON notaventa.id=notaventadetalle.notaventa_id and 
        if((SELECT cantsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventadetalle_id=notaventadetalle.id
                ) >= notaventadetalle.cant,false,true)
        LEFT JOIN vista_sumsoldespdet
        ON vista_sumsoldespdet.notaventadetalle_id=notaventadetalle.id
        INNER JOIN producto
        ON notaventadetalle.producto_id=producto.id
        INNER JOIN categoriaprod
        ON categoriaprod.id=producto.categoriaprod_id AND ISNULL(categoriaprod.deleted_at)
        INNER JOIN areaproduccion
        ON areaproduccion.id=categoriaprod.areaproduccion_id
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id
        INNER JOIN comuna
        ON comuna.id=notaventa.comunaentrega_id
        INNER JOIN tipoentrega
        ON tipoentrega.id=notaventa.tipoentrega_id
        INNER JOIN vista_notaventatotales
        ON notaventa.id=vista_notaventatotales.id
        INNER JOIN sucursal
        ON notaventa.sucursal_id = sucursal.id AND ISNULL(sucursal.deleted_at)
        LEFT JOIN clientebloqueado
        ON notaventa.cliente_id = clientebloqueado.cliente_id and isnull(clientebloqueado.deleted_at)
        LEFT JOIN vista_datacobranza
        ON vista_datacobranza.cliente_id = notaventa.cliente_id
        LEFT JOIN clientedesbloqueado
        ON clientedesbloqueado.cliente_id = notaventa.cliente_id and clientedesbloqueado.notaventa_id = notaventa.id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
        LEFT JOIN clientedesbloqueadomodulo
        ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id $aux_condmodulo_id
        LEFT JOIN modulo
        ON modulo.id = clientedesbloqueadomodulo.modulo_id
        LEFT JOIN clientedesbloqueadopro
        ON clientedesbloqueadopro.cliente_id = notaventa.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
        
        LEFT JOIN clientedesbloqueado as clientedesbloqueado_orddesp
        ON clientedesbloqueado_orddesp.cliente_id = notaventa.cliente_id and clientedesbloqueado_orddesp.notaventa_id = notaventa.id and not isnull(clientedesbloqueado_orddesp.notaventa_id) and isnull(clientedesbloqueado_orddesp.deleted_at)
        LEFT JOIN clientedesbloqueadomodulo as clientedesbloqueadomodulo_orddesp
        ON clientedesbloqueadomodulo_orddesp.clientedesbloqueado_id = clientedesbloqueado_orddesp.id and clientedesbloqueadomodulo_orddesp.modulo_id = 32
        LEFT JOIN despachosol
        ON despachosol.notaventa_id = notaventa.id and despachosol.id NOT IN (SELECT despachosolanul.despachosol_id from despachosolanul WHERE isnull(despachosolanul.deleted_at))
        and isnull(despachosol.aprorddesp) and isnull(despachosol.deleted_at)
        LEFT JOIN acuerdotecnico
        ON acuerdotecnico.producto_id = notaventadetalle.producto_id
        WHERE
        categoriaprod.id in (SELECT categoriaprodsuc.categoriaprod_id 
            FROM categoriaprodsuc 
            WHERE categoriaprodsuc.categoriaprod_id = categoriaprod.id
            AND categoriaprodsuc.sucursal_id IN ($arraySucFisxUsu))
        and $vendedorcond
        and $aux_condFecha
        and $aux_condrut
        and $aux_condoc_id
        and $aux_condgiro_id
        and $aux_condareaproduccion_id
        and $aux_condtipoentrega_id
        and $aux_condnotaventa_id
        and $aux_aprobstatus
        and $aux_condcomuna_id
        and $aux_condplazoentrega
        and $aux_condproducto_id
        AND $aux_condsucursal_id
        and notaventa.anulada is null
        and notaventa.findespacho is null
        and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
        and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        AND notaventa.sucursal_id in ($sucurcadena)
        AND notaventa.id not in (
                SELECT notaventa_id 
                    FROM otnotaventa 
                    WHERE otnotaventa.ot_id not in (SELECT otanul.ot_id FROM otanul WHERE isnull(otanul.deleted_at))
                    AND isnull(otnotaventa.deleted_at)
                )
        GROUP BY notaventadetalle.notaventa_id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho
        HAVING SUM(notaventadetalle.requiere_fabricacion) > 0
        ORDER BY $aux_orden;";
    }
    if($aux_sql==2){
        //if(categoriaprod.unidadmedida_id=3,producto.diamextpg,producto.diamextmm) AS diametro,
        $sql = "SELECT notaventadetalle.producto_id,producto.nombre,
        producto.diametro,sucursal.nombre as sucursal_nombre,
        claseprod.cla_nombre,producto.long,producto.peso,producto.tipounion,
        cant,cantsoldesp,
        totalkilos,
        subtotal,
        kgsoldesp,subtotalsoldesp,
        sum(cant-if(isnull(cantsoldesp),0,cantsoldesp)) as saldocant,
        sum(totalkilos-if(isnull(kgsoldesp),0,kgsoldesp)) as saldokg,
        sum(subtotal-if(isnull(subtotalsoldesp),0,subtotalsoldesp)) as saldoplata,
        (SELECT CONCAT(dte.nrodocto,';',oc_id,';',oc_folder,'/',oc_file) as nrodocto
            FROM dteoc INNER JOIN dte
            ON dteoc.dte_id = dte.id AND ISNULL(dteoc.deleted_at) AND ISNULL(dte.deleted_at)
            INNER JOIN dteguiadesp
            ON dteoc.dte_id = dteguiadesp.dte_id AND ISNULL(dteguiadesp.deleted_at)
            WHERE dteoc.oc_id = notaventa.oc_id
            AND isnull(dteguiadesp.notaventa_id)
            AND dte.cliente_id= notaventa.cliente_id
            AND dteguiadesp.dte_id NOT IN (SELECT dteanul.dte_id 
                                    FROM dteanul 
                                    WHERE dteanul.dte_id = dteguiadesp.dte_id 
                                    and ISNULL(dteanul.deleted_at))) as dte_nrodocto,
        if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
        '' as rutanuevasoldesp
        FROM notaventadetalle INNER JOIN notaventa
        ON notaventadetalle.notaventa_id=notaventa.id
        INNER JOIN producto
        ON notaventadetalle.producto_id=producto.id
        INNER JOIN claseprod
        ON producto.claseprod_id=claseprod.id
        INNER JOIN categoriaprod
        ON producto.categoriaprod_id=categoriaprod.id
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id
        LEFT JOIN vista_sumsoldespdet
        ON vista_sumsoldespdet.notaventadetalle_id=notaventadetalle.id
        INNER JOIN sucursal
        ON notaventa.sucursal_id = sucursal.id AND ISNULL(sucursal.deleted_at)
        LEFT JOIN clientebloqueado
        ON notaventa.cliente_id = clientebloqueado.cliente_id and isnull(clientebloqueado.deleted_at)
        WHERE 
        categoriaprod.id in (SELECT categoriaprodsuc.categoriaprod_id 
            FROM categoriaprodsuc 
            WHERE categoriaprodsuc.categoriaprod_id = categoriaprod.id
            AND categoriaprodsuc.sucursal_id IN (SELECT vista_sucfisxusu.sucursal_id
                    FROM vista_sucfisxusu
                    WHERE vista_sucfisxusu.usuario_id=$user->id))
        and $vendedorcond
        and $aux_condFecha
        and $aux_condrut
        and $aux_condoc_id
        and $aux_condgiro_id
        and $aux_condareaproduccion_id
        and $aux_condtipoentrega_id
        and $aux_condnotaventa_id
        and $aux_aprobstatus
        and $aux_condcomuna_id
        and $aux_condplazoentrega
        and $aux_condproducto_id
        AND $aux_condsucursal_id
        AND isnull(notaventa.findespacho)
        AND isnull(notaventa.anulada)
        AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
        and notaventadetalle.notaventa_id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        AND notaventa.sucursal_id in ($sucurcadena)
        GROUP BY notaventadetalle.producto_id
        ORDER BY producto.nombre,producto.peso;";
    }
    

    if($aux_sql==3){
        $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
        comuna.nombre as comunanombre,
        vista_notaventatotales.cant,
        vista_notaventatotales.precioxkilo,
        sum(vista_notaventatotales.totalkilos) as totalkilos,
        sum(vista_notaventatotales.subtotal) as subtotal,
        sum(if(areaproduccion.id=1,notaventadetalle.totalkilos,0)) AS pvckg,
        sum(if(areaproduccion.id=2,notaventadetalle.totalkilos,0)) AS cankg,
        sum(if(areaproduccion.id=1,notaventadetalle.subtotal,0)) AS pvcpesos,
        sum(if(areaproduccion.id=2,notaventadetalle.subtotal,0)) AS canpesos,
        sum(notaventadetalle.subtotal) AS totalps,
        sum((SELECT sum(kgsoldesp) as kgsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id)) as totalkgsoldesp,
        sum((SELECT sum(subtotalsoldesp) as subtotalsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id)) as totalsubtotalsoldesp,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho,
        tipoentrega.nombre as tipentnombre,tipoentrega.icono,
        if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
        '' as rutanuevasoldesp
        FROM notaventa INNER JOIN notaventadetalle
        ON notaventa.id=notaventadetalle.notaventa_id and 
        if((SELECT cantsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventadetalle_id=notaventadetalle.id
                ) >= notaventadetalle.cant,false,true)
        INNER JOIN producto
        ON notaventadetalle.producto_id=producto.id
        INNER JOIN categoriaprod
        ON categoriaprod.id=producto.categoriaprod_id
        INNER JOIN areaproduccion
        ON areaproduccion.id=categoriaprod.areaproduccion_id
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id
        INNER JOIN comuna
        ON comuna.id=notaventa.comunaentrega_id
        INNER JOIN tipoentrega
        ON tipoentrega.id=notaventa.tipoentrega_id
        INNER JOIN vista_notaventatotales
        ON notaventa.id=vista_notaventatotales.id
        LEFT JOIN clientebloqueado
        ON notaventa.cliente_id = clientebloqueado.cliente_id and isnull(clientebloqueado.deleted_at)
        WHERE 
        categoriaprod.id in (SELECT categoriaprodsuc.categoriaprod_id 
            FROM categoriaprodsuc 
            WHERE categoriaprodsuc.categoriaprod_id = categoriaprod.id
            AND categoriaprodsuc.sucursal_id IN (SELECT vista_sucfisxusu.sucursal_id
                    FROM vista_sucfisxusu
                    WHERE vista_sucfisxusu.usuario_id=$user->id))
        and $vendedorcond
        and $aux_condFecha
        and $aux_condrut
        and $aux_condoc_id
        and $aux_condgiro_id
        and $aux_condareaproduccion_id
        and $aux_condtipoentrega_id
        and $aux_condnotaventa_id
        and $aux_aprobstatus
        and $aux_condcomuna_id
        and $aux_condplazoentrega
        and $aux_condproducto_id
        and notaventa.anulada is null
        and notaventa.findespacho is null
        and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
        and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        AND notaventa.sucursal_id in ($sucurcadena)
        GROUP BY notaventa.cliente_id
        ORDER BY $aux_orden;";
        //dd($sql);
    }
    //dd($sql);
    $datas = DB::select($sql);
    //dd($datas);
    if($aux_sql==1){
        foreach ($datas as &$data) {
            //dd($data->nvdetalle);
    
            // Array para almacenar el resultado final
            $detalleArrayFinal = [];
    
            // Dividir el campo detallenv en registros individuales
            $detalleArray = explode(';', $data->nvdetalle);
            //dd($detalleArray);
    
            // Crear un array con todos los producto_id
            $productoIds = array_map(function ($detalle) {
                return explode('|', $detalle)[0]; // Extraemos solo producto_id
            }, $detalleArray);
    
            // Procesar cada registro de detallenv y agregar el nombre del producto
            //dd($detalleArray);
            foreach ($detalleArray as $index => $detalle) {
                //dd($detalle);
                $productoarray = Producto::atributosProducto($productoIds[$index]);
                list($producto_id, $cant, $precio, $subtotal, $cantsoldesp, $totalkilos, $requiere_fabricacion,$acuerdotecnico_id) = array_pad(explode('|', $detalle), 8, null);
                //$producto_nombre = isset($productos[$producto_id]) ? $productos[$producto_id] : 'Desconocido';
                $detalleFinal = implode('|', [$producto_id, $cant, $precio, $subtotal, $cantsoldesp, $productoarray["nombre"], $totalkilos, $requiere_fabricacion,$acuerdotecnico_id]);
                $detalleArrayFinal[] = $detalleFinal;
            }
            //dd(implode(';', $detalleArrayFinal));
    
            // Reconstruir el campo detallenv con los nuevos valores
            $data->nvdetalle = implode(';', $detalleArrayFinal);
            $data->rutacrear = route('crear_otnv', ['id' => $data->id,'updatednum_at' => strtotime($data->updated_at)]);
        }
    }
    filtrarclientesbloqueados($request,$datas);
    //dd($datas);
    return $datas;
}