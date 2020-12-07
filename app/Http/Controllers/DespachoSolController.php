<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarDespachoSol;
use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteBloqueado;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoSol;
use App\Models\DespachoSolAnul;
use App\Models\DespachoSolDet;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\NotaVenta;
use App\Models\NotaVentaDetalle;
use App\Models\PlazoPago;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespachoSolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-solicitud-despacho');
        $despachosolanul = DespachoSolAnul::orderBy('id')->pluck('despachosol_id')->toArray();
        $datas = DespachoSol::orderBy('id')
                ->whereNull('aprorddesp')
                ->whereNotIn('id', $despachosolanul)
                ->get();
        return view('despachosol.index', compact('datas'));
    }

    public function listarnv()
    {
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
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
                ->where('vendedor.id','=',$vendedor_id)
                ->get();
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
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
        }
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        //* Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado*/
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaAct = date("d/m/Y");

        /*
        $request = [
            'fechad'            => '',
            'fechah'            => '',
            'rut'               => '',
            'vendedor_id'       => '',
            'oc_id'             => '',
            'giro_id'           => '',
            'areaproduccion_id' => '',
            'tipoentrega_id'    => '',
            'notaventa_id'      => '',
            'aprobstatus'       => ''
        ];
        $respuesta = reporte1($request);
        */ 

        return view('despachosol.listarnotaventa', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','comunas','fechaAct'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear($id)
    {
    }

    public function crearsol($id)
    {
        can('crear-solicitud-despacho');
        $data = NotaVenta::findOrFail($id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $detalles = $data->notaventadetalles()->get();
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
        $vendedor_id=$data->vendedor_id;
        $clienteselec = $data->cliente()->get();
        //session(['aux_aprocot' => '0']);
        //dd($clienteselec[0]->rut);

        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
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
        $clienteDirec = $data->clientedirec()->get();
        $fecha = date("d/m/Y", strtotime($data->fechahora));
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();

        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
        //******************* */
        $productos = CategoriaProd::join('categoriaprodsuc', 'categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
        ->join('sucursal', 'categoriaprodsuc.sucursal_id', '=', 'sucursal.id')
        ->join('producto', 'categoriaprod.id', '=', 'producto.categoriaprod_id')
        ->join('claseprod', 'producto.claseprod_id', '=', 'claseprod.id')
        ->select([
                'producto.id',
                'producto.nombre',
                'claseprod.cla_nombre',
                'producto.codintprod',
                'producto.diamextmm',
                'producto.espesor',
                'producto.long',
                'producto.peso',
                'producto.tipounion',
                'producto.precioneto',
                'categoriaprod.precio',
                'categoriaprodsuc.sucursal_id'
                ])
                ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
                ->get();
        //****************** */
        $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
        //* Filtro solos los clientes que esten asignados a la sucursal */
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono','cliente.giro_id'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();

        //dd($clientes);
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
        $aux_sta=2;
        $aux_statusPant = 0;

        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
        }
        //dd($clientedirecs);
        return view('despachosol.crear', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id'));
        
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarDespachoSol $request)
    {
        can('guardar-solicitud-despacho');
        $notaventa = NotaVenta::findOrFail($request->notaventa_id);
        //dd('cliente bloquedo');
        
        $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$notaventa->cliente_id)->get();
        if(count($clibloq) > 0){
            return redirect('despachosol/index')->with([
                'mensaje'=>'Registro no fue guardado. Cliente Bloqueado: ' . $clibloq[0]->descripcion ,
                'tipo_alert' => 'alert-error'
            ]);
        }
        if($notaventa->updated_at == $request->updated_at){
            $notaventa->updated_at = date("Y-m-d H:i:s");
            $notaventa->save();
            $hoy = date("Y-m-d H:i:s");
            $request->request->add(['fechahora' => $hoy]);
            $request->request->add(['usuario_id' => auth()->id()]);
            $dateInput = explode('/',$request->plazoentrega);
            $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
            $dateInput = explode('/',$request->fechaestdesp);
            $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
            $comuna = Comuna::findOrFail($request->comuna_id);
            $request->request->add(['provincia_id' => $comuna->provincia_id]);
            $request->request->add(['region_id' => $comuna->provincia->region_id]);
            $despachosol = DespachoSol::create($request->all());
            $despachosolid = $despachosol->id;
            $cont_producto = count($request->producto_id);
            if($cont_producto>0){
                for ($i=0; $i < $cont_producto ; $i++){
                    $aux_cantsol = $request->cantsol[$i];
                    if(is_null($request->producto_id[$i])==false && is_null($aux_cantsol)==false && $aux_cantsol > 0){
                        $despachosoldet = new DespachoSolDet();
                        $despachosoldet->despachosol_id = $despachosolid;
                        $despachosoldet->notaventadetalle_id = $request->NVdet_id[$i];
                        $despachosoldet->cantsoldesp = $request->cantsoldesp[$i];
                        if($despachosoldet->save()){
                            /*
                            $notaventadetalle = NotaVentaDetalle::findOrFail($request->NVdet_id[$i]);
                            $notaventadetalle->cantsoldesp = $request->cantsoldesp[$i];
                            $notaventadetalle->save();
                            */
                            //$despacho_id = $despachosol->id;    
                        }
                    }
                }
            }
            return redirect('despachosol/index')->with([
                'mensaje'=>'Registro creado con exito.',
                'tipo_alert' => 'alert-success'
            ]);
        }else{
            return redirect('despachosol/index')->with([
                'mensaje'=>'Registro no fue creado. Registro Editado por otro usuario. Fecha Hora: '.$notaventa->updated_at,
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
        can('editar-solicitud-despacho');
        $data = DespachoSol::findOrFail($id);
        //dd($data);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $detalles = $data->despachosoldets()->get();
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

        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
        //******************* */
        $productos = CategoriaProd::join('categoriaprodsuc', 'categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
        ->join('sucursal', 'categoriaprodsuc.sucursal_id', '=', 'sucursal.id')
        ->join('producto', 'categoriaprod.id', '=', 'producto.categoriaprod_id')
        ->join('claseprod', 'producto.claseprod_id', '=', 'claseprod.id')
        ->select([
                'producto.id',
                'producto.nombre',
                'claseprod.cla_nombre',
                'producto.codintprod',
                'producto.diamextmm',
                'producto.espesor',
                'producto.long',
                'producto.peso',
                'producto.tipounion',
                'producto.precioneto',
                'categoriaprod.precio',
                'categoriaprodsuc.sucursal_id'
                ])
                ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
                ->get();
        //****************** */
        $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
        //* Filtro solos los clientes que esten asignados a la sucursal */
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono','cliente.giro_id'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();

        //dd($clientes);
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
        $aux_sta=2;
        $aux_statusPant = 0;

        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
        }
        //dd($clientedirecs);
        return view('despachosol.editar', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id'));
  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarDespachoSol $request, $id)
    {
        can('guardar-solicitud-despacho');
        $dateInput = explode('/',$request->plazoentrega);
        $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        $dateInput = explode('/',$request->fechaestdesp);
        $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        $despachosol = DespachoSol::findOrFail($id);
        $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$despachosol->notaventa->cliente_id)->get();
        if(count($clibloq) > 0){
            return redirect('despachosol/index')->with([
                'mensaje'=>'Registro no fue guardado. Cliente Bloqueado: ' . $clibloq[0]->descripcion ,
                'tipo_alert' => 'alert-error'
            ]);
        }
        if($despachosol->updated_at == $request->updated_at){
            $despachosol->updated_at = date("Y-m-d H:i:s");
            $despachosol->comunaentrega_id = $request->comunaentrega_id;
            $despachosol->tipoentrega_id = $request->tipoentrega_id;
            $despachosol->plazoentrega = $request->plazoentrega;
            $despachosol->lugarentrega = $request->lugarentrega;
            $despachosol->contacto = $request->contacto;
            $despachosol->contactoemail = $request->contactoemail;
            $despachosol->contactotelf = $request->contactotelf;
            $despachosol->observacion = $request->observacion;
            $despachosol->fechaestdesp = $request->fechaestdesp;
            //dd($request);
            if($despachosol->save()){
                $cont_producto = count($request->producto_id);
                if($cont_producto>0){
                    for ($i=0; $i < $cont_producto ; $i++){
                        if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                            $despachosoldet = DespachoSolDet::findOrFail($request->NVdet_id[$i]);
                            $despachosoldet->cantsoldesp = $request->cantsoldesp[$i];
                            if($despachosoldet->save()){ //Si al editar dejo en cero la cantidad solicitada elimino el registro en detalle solicitud
                                if($request->cantsoldesp[$i]==0){
                                    $despachosoldet->usuariodel_id = auth()->id();
                                    $despachosoldet->save();
                                    $despachosoldet->delete();
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
            return redirect('despachosol/index')->with([
                                                        'mensaje'=>'Registro actualizado con exito.',
                                                        'tipo_alert' => 'alert-success'
                                                    ]);
        }else{
            return redirect('despachosol/index')->with([
                                                        'mensaje'=>'Registro no fue modificado. Registro Editado por otro usuario. Fecha Hora: '.$despachosol->updated_at,
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


    public function listarsoldesp() //Listar solicitudes de despacho
    {
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
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
                ->where('vendedor.id','=',$vendedor_id)
                ->get();
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
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
        }
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        //* Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado*/
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaAct = date("d/m/Y");

        /*
        $request = [
            'fechad'            => '',
            'fechah'            => '',
            'rut'               => '',
            'vendedor_id'       => '',
            'oc_id'             => '',
            'giro_id'           => '',
            'areaproduccion_id' => '',
            'tipoentrega_id'    => '',
            'notaventa_id'      => '',
            'aprobstatus'       => ''
        ];
        $respuesta = reporte1($request);
        */ 

        return view('despachoord.listardespachosol', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','comunas','fechaAct'));

    }

    public function reporte(Request $request){
        $respuesta = reporte1($request);
        return $respuesta;
    }

    public function reportesoldesp(Request $request){
        $respuesta = reportesoldesp1($request);
        return $respuesta;
    }

    public function anular(Request $request)
    {
        if ($request->ajax()) {
            $despachosol = DespachoSol::findOrFail($request->id);
            if($despachosol->despachoords->count() == 0){
                $despachosolanul = new DespachoSolAnul();
                $despachosolanul->despachosol_id = $request->id;
                $despachosolanul->usuario_id = auth()->id();
                if ($despachosolanul->save()) {
                    /*
                    $despachosoldets = $despachosol->despachosoldets;
                    foreach ($despachosoldets as $despachosoldet){
                        $notaventadetalle = NotaVentaDetalle::findOrFail($despachosoldet->notaventadetalle_id);
                        $notaventadetalle->cantsoldesp = $notaventadetalle->cantsoldesp - $despachosoldet->cantsoldesp;
                        $notaventadetalle->save();
                    }*/
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            }else{
                return response()->json(['mensaje' => 'hijo']);
            }
        } else {
            abort(404);
        }
    }

    public function aproborddesp(Request $request)
    {
        if ($request->ajax()) {
            $despachosol = DespachoSol::findOrFail($request->id);
            if($despachosol->despachoords->count() == 0){
                $despachosol->aprorddesp = 1;
                $despachosol->aprorddespfh = date("Y-m-d H:i:s");;
                if ($despachosol->save()) {
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            }else{
                return response()->json(['mensaje' => 'hijo']);
            }
        } else {
            abort(404);
        }
    }


    public function exportPdf($id,$stareport = '1')
    {
        $despachosol = DespachoSol::findOrFail($id);
        $despachosoldets = $despachosol->despachosoldets()->get();
        //dd($despachosol);
        $empresa = Empresa::orderBy('id')->get();
        $rut = number_format( substr ( $despachosol->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachosol->notaventa->cliente->rut, strlen($despachosol->notaventa->cliente->rut) -1 , 1 );
        //dd($empresa[0]['iva']);
        if($stareport == '1'){
            if(env('APP_DEBUG')){
                return view('despachosol.reporte', compact('despachosol','despachosoldets','empresa'));
            }
        
            $pdf = PDF::loadView('despachosol.reporte', compact('despachosol','despachosoldets','empresa'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream(str_pad($despachosol->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachosol->notaventa->cliente->razonsocial . '.pdf');
        }else{
            if($stareport == '2'){
                return view('despachosol.listado1', compact('despachosol','despachosoldets','empresa'));        
                $pdf = PDF::loadView('despachosol.listado1', compact('despachosol','despachosoldets','empresa'));
                //return $pdf->download('cotizacion.pdf');
                return $pdf->stream(str_pad($despachosol->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachosol->notaventa->cliente->razonsocial . '.pdf');
    
            }
        }
    }

    //Reporte previo a la solicitud de Despacho, para saber como esta la nota de venta
    public function pdfSolDespPrev($id,$stareport = '1')
    {
        $notaventa = NotaVenta::findOrFail($id);
        $notaventaDetalles = $notaventa->notaventadetalles()->get();
        $empresa = Empresa::orderBy('id')->get();
        $rut = number_format( substr ( $notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $notaventa->cliente->rut, strlen($notaventa->cliente->rut) -1 , 1 );
        //dd($empresa[0]['iva']);
        if(env('APP_DEBUG')){
            return view('despachosol.reportesolprev', compact('notaventa','notaventaDetalles','empresa'));
        }
        $pdf = PDF::loadView('despachosol.reportesolprev', compact('notaventa','notaventaDetalles','empresa'));
        //return $pdf->download('cotizacion.pdf');
        return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');        
    }
}


function consulta($request,$aux_sql,$orden){
    if($orden==1){
        $aux_orden = "notaventadetalle.notaventa_id desc";
    }else{
        $aux_orden = "notaventa.cliente_id";
    }
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

    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
    }

    if(empty($request->plazoentrega)){
        $aux_condplazoentrega = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->plazoentrega);
        $fechad = date_format($fecha, 'Y-m-d');
        $aux_condplazoentrega = "notaventa.plazoentrega='$fechad'";
    }
    //dd($aux_condplazoentrega);

    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);
    if($aux_sql==1){
        $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
        comuna.nombre as comunanombre,
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
        tipoentrega.nombre as tipentnombre,tipoentrega.icono
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
        and $aux_condplazoentrega
        and notaventa.anulada is null
        and notaventa.findespacho is null
        and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
        GROUP BY notaventadetalle.notaventa_id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho
        ORDER BY $aux_orden;";
    }
    
    if($aux_sql==2){
        $sql = "SELECT notaventadetalle.id,notaventadetalle.producto_id,producto.nombre,
        if(categoriaprod.unidadmedida_id=3,producto.diamextpg,producto.diamextmm) AS diametro,
        claseprod.cla_nombre,producto.long,producto.peso,producto.tipounion,
        cant,cantsoldesp,
        totalkilos,
        subtotal,
        kgsoldesp,subtotalsoldesp,
        sum(totalkilos-if(isnull(kgsoldesp),0,kgsoldesp)) as saldokg,
        sum(subtotal-if(isnull(subtotalsoldesp),0,subtotalsoldesp)) as saldokg
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
        and $aux_condplazoentrega
        AND isnull(notaventa.findespacho)
        AND isnull(notaventa.anulada)
        AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
        GROUP BY notaventadetalle.producto_id;";
    }

    //dd($sql);
    $datas = DB::select($sql);
    //dd($datas);
    return $datas;
}

function reporte1($request){
    $respuesta = array();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = "Código no Existe";
    $respuesta['tabla'] = "";
    $respuesta['tabla2'] = "";
    $respuesta['tabla3'] = "";

    if($request->ajax()){
        $datas = consulta($request,1,1);
        $aux_colvistoth = "";
        if(auth()->id()==1 or auth()->id()==2){
            $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";
        }
        $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";

        $respuesta['tabla'] .= "<table id='tabla-data-listar' name='tabla-data-listar' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Razón Social</th>
                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                <th class='tooltipsC' title='Nota de Venta'>NV</th>
                <th class='tooltipsC' title='Precio x Kg'>$ x Kg</th>
                <th>Comuna</th>
                <th style='text-align:right' class='tooltipsC' title='Kg Pendiente'>Kg Pend</th>
                <th style='text-align:right' class='tooltipsC' title='$ Pendiente'>$ Pend</th>
                <!--<th style='text-align:right' class='tooltipsC' title='Precio Promedio x Kg'>Prom</th>-->
                <th class='tooltipsC' title='Solicitud Despacho'>Despacho</th>
            </tr>
        </thead>
        <tbody>";

        $i = 0;
        $aux_Tpvckg = 0;
        $aux_Tpvcpesos= 0;
        $aux_Tcankg = 0;
        $aux_Tcanpesos = 0;
        $aux_totalKG = 0;
        $aux_totalps = 0;
        $aux_prom = 0;
        foreach ($datas as $data) {
            $colorFila = "";
            $aux_data_toggle = "";
            $aux_title = "";

            $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
            $prompvc = 0;
            $promcan = 0;
            $aux_prom = 0;
            if($data->pvckg!=0){
                $prompvc = $data->pvcpesos / $data->pvckg;
            }
            if($data->cankg!=0){
                $promcan = $data->canpesos / $data->cankg;
            }
            if($data->totalkilos>0){
                $aux_prom = $data->subtotal / $data->totalkilos;
            }

            $Visto       = $data->visto;
            $checkVisto  = 'checked';
            if(empty($data->visto))
                $checkVisto = '';

            $aux_colvistotd = "";
            if(empty($data->visto)){
                $fechavisto = '';
            }else{
                $fechavisto = 'Leido:' . date('d-m-Y h:i:s A', strtotime($data->visto));
            }
            
            $aux_colvistotd = "
            <td class='tooltipsC' style='text-align:center' class='tooltipsC' title='$fechavisto'>
                <div class='checkbox'>
                    <label style='font-size: 1.2em'>";
                    if(!empty($data->anulada)){
                        $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto disabled>";
                    }else{
                        if(auth()->id()==1 or auth()->id()==2){
                            $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto onclick='visto($data->id,$i)'>";
                        }else{
                            $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto disabled>";
                        }
                    }
                    $aux_colvistotd .= "<span class='cr'><i class='cr-icon fa fa-check'></i></span>
                    </label>
                </div>
            </td>";
            if(empty($data->oc_file)){
                $aux_enlaceoc = $data->oc_id;
            }else{
                $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
            }
            $nuevoSolDesp = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Vista Previa SD' onclick='pdfSolDespPrev($data->id,2)'>
                                <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                            </a>";
            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$data->cliente_id)->get();
            if(count($clibloq) > 0){
                $aux_descbloq = $clibloq[0]->descripcion;
                $nuevoSolDesp .= "<a class='btn-accion-tabla tooltipsC' title='Cliente Bloqueado: $aux_descbloq'>
                                    <i class='fa fa-fw fa-ban text-danger'></i>
                                </a>";
            }else{
                $ruta_nuevoSolDesp = route('crearsol_despachosol', ['id' => $data->id]);
                $nuevoSolDesp .= "<a href='$ruta_nuevoSolDesp' class='btn-accion-tabla tooltipsC' title='Hacer solicitud despacho: $data->tipentnombre'>
                    <i class='fa fa-fw $data->icono'></i>
                    </a>";
            }
            if(!empty($data->anulada)){
                $colorFila = 'background-color: #87CEEB;';
                $aux_data_toggle = "tooltip";
                $aux_title = "Anulada Fecha:" . $data->anulada;
                $nuevoSolDesp = "";
            }
            //dd($ruta_nuevoSolDesp);
            $respuesta['tabla'] .= "
            <tr id='fila$i' name='fila$i' style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                <td id='id$i' name='id$i'>$data->id</td>
                <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                <td>
                    <!--<a href='" . route('exportPdf_notaventa', ['id' => $data->id,'stareport' => '1']) . "' class='btn-accion-tabla tooltipsC' title='Nota de Venta' target='_blank'>-->
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV($data->id,1)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>$data->id
                    </a>
                </td>
                <td>
                    <!--<a href='" . route('exportPdf_notaventa', ['id' => $data->id,'stareport' => '2']) . "' class='btn-accion-tabla tooltipsC' title='Precio x Kg' target='_blank'>-->
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Precio x Kg' onclick='genpdfNV($data->id,2)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                    </a>
                </td>
                <td>$data->comunanombre</td>
                <td id='totalkilos$i' name='totalkilos$i' style='text-align:right'>".number_format($data->totalkilos - $data->totalkgsoldesp, 2, ",", ".") ."</td>
                <td id='totalps$i' name='totalps$i' style='text-align:right'>".number_format($data->subtotal - $data->totalsubtotalsoldesp, 2, ",", ".") ."</td>
                <!--<td id='prompvc$i' name='prompvc$i' style='text-align:right'>".number_format($aux_prom, 2, ",", ".") ."</td>-->
                <td>
                    $nuevoSolDesp
                </td>
            </tr>";

            if(empty($data->anulada)){
                $aux_Tpvckg += $data->pvckg;
                $aux_Tpvcpesos += $data->pvcpesos;
                $aux_Tcankg += $data->cankg;
                $aux_Tcanpesos += $data->canpesos;
                $aux_totalKG += ($data->totalkilos - $data->totalkgsoldesp);
                $aux_totalps += ($data->subtotal - $data->totalsubtotalsoldesp);    
            }


            //dd($data->contacto);
        }

        $aux_promGeneral = 0;
        if($aux_totalKG>0){
            $aux_promGeneral = $aux_totalps / $aux_totalKG;
        }
        $respuesta['tabla'] .= "
        </tbody>
        <tfoot>
            <tr>
                <th colspan='7' style='text-align:left'>TOTAL</th>
                <th style='text-align:right'>". number_format($aux_totalKG, 2, ",", ".") ."</th>
                <th style='text-align:right'>". number_format($aux_totalps, 2, ",", ".") ."</th>
                <!--<th style='text-align:right'>". number_format($aux_promGeneral, 2, ",", ".") ."</th>-->
                <th style='text-align:right'></th>
            </tr>
        </tfoot>

        </table>";

        /*****CONSULTA AGRUPADO POR CLIENTE******/
        $datas = consulta($request,1,2);
        $aux_clienteid = $datas[0]->cliente_id;

        $respuesta['tabla2'] .= "<table id='tabla-data-listar' name='tabla-data-listar' class='table display AllDataTables table-hover table-condensed tablascons2' data-page-length='50'>
        <thead>
            <tr>
                <th>Razón Social</th>
                <th>Comuna</th>
                <th style='text-align:right' class='tooltipsC' title='Kg Pendiente'>Kg Pend</th>
                <th style='text-align:right' class='tooltipsC' title='$ Pendiente'>$ Pend</th>
            </tr>
        </thead>
        <tbody>";

        $aux_kgpend = 0;
        $aux_platapend = 0;
        $razonsocial = "";
        $aux_comuna  = "";
        $aux_totalkg = 0;
        $aux_totalplata = 0;
        foreach ($datas as $data) {
            if($data->cliente_id!=$aux_clienteid){
                $respuesta['tabla2'] .= "
                <tr>
                    <td>$razonsocial</td>
                    <td>$aux_comuna</td>
                    <td style='text-align:right'>".number_format($aux_kgpend, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($aux_platapend, 2, ",", ".") ."</td>
                </tr>";
                $aux_kgpend = 0;
                $aux_platapend = 0;
            }
            $aux_kgpend += ($data->totalkilos - $data->totalkgsoldesp);
            $aux_platapend += ($data->subtotal - $data->totalsubtotalsoldesp);
            $aux_totalkg += ($data->totalkilos - $data->totalkgsoldesp);
            $aux_totalplata += ($data->subtotal - $data->totalsubtotalsoldesp);
            $razonsocial = $data->razonsocial;
            $aux_comuna  = $data->comunanombre;

        }
        $respuesta['tabla2'] .= "
            <tr>
                <td>$razonsocial</td>
                <td>$aux_comuna</td>
                <td style='text-align:right'>".number_format($aux_kgpend, 2, ",", ".") ."</td>
                <td style='text-align:right'>".number_format($aux_platapend, 2, ",", ".") ."</td>
            </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='2' style='text-align:left'>TOTALES</th>
                    <th style='text-align:right'>". number_format($aux_totalkg, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalplata, 2, ",", ".") ."</th>
                </tr>
            </tfoot>

            </table>";


            /*****CONSULTA AGRUPADO POR PRODUCTO*****/
        $datas = consulta($request,2,1);
        $respuesta['tabla3'] .= "<table id='tabla-data-listar' name='tabla-data-listar' class='table display AllDataTables table-hover table-condensed tablascons2' data-page-length='50'>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Diametro</th>
                <th>Clase</th>
                <th>Largo</th>
                <th>Peso</th>
                <th>TU</th>
                <th style='text-align:right' class='tooltipsC' title='Kg Pendiente'>Kg Pend</th>
                <th style='text-align:right' class='tooltipsC' title='$ Pendiente'>$ Pend</th>
            </tr>
        </thead>
        <tbody>";
        $aux_totalkg = 0;
        $aux_totalplata = 0;
        foreach ($datas as $data) {
            $aux_totalkg += ($data->totalkilos - $data->kgsoldesp);
            $aux_totalplata += ($data->subtotal - $data->subtotalsoldesp);    
            $respuesta['tabla3'] .= "
            <tr>
                <td>$data->nombre</td>
                <td>$data->diametro</td>
                <td>$data->cla_nombre</td>
                <td>$data->long</td>
                <td>$data->peso</td>
                <td>$data->tipounion</td>
                <td style='text-align:right'>".number_format($data->totalkilos - $data->kgsoldesp, 2, ",", ".") ."</td>
                <td style='text-align:right'>".number_format($data->subtotal - $data->subtotalsoldesp, 2, ",", ".") ."</td>
            </tr>";
        }        
        $respuesta['tabla3'] .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='6' style='text-align:left'>TOTALES</th>
                    <th style='text-align:right'>". number_format($aux_totalkg, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalplata, 2, ",", ".") ."</th>
                </tr>
            </tfoot>
            </table>";

        return $respuesta;
    }
}

function reportesoldesp1($request){
    $respuesta = array();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = "Código no Existe";
    $respuesta['tabla'] = "";

    if($request->ajax()){
        $datas = consultasoldesp($request);

        $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th class='tooltipsC' title='Fecha Estimada de Despacho'>Fecha ED</th>
                <th>Razón Social</th>
                <th class='tooltipsC' title='Solicitud de Despacho'>SD</th>
                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                <th class='tooltipsC' title='Nota de Venta'>NV</th>
                <th>Comuna</th>
                <th class='tooltipsC' title='Total Kg Pendientes'>Total Kg</th>
                <th class='tooltipsC' title='Orden Despacho'>Despacho</th>
            </tr>
        </thead>
        <tbody>";

        $i = 0;
        foreach ($datas as $data) {

            $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
            if(empty($data->oc_file)){
                $aux_enlaceoc = $data->oc_id;
            }else{
                $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
            }
            $ruta_nuevoOrdDesp = route('crearord_despachoord', ['id' => $data->id]);
            //dd($ruta_nuevoSolDesp);

            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$data->cliente_id)->get();
            if(count($clibloq) > 0){
                $aux_descbloq = $clibloq[0]->descripcion;
                $nuevoOrdDesp = "<a class='btn-accion-tabla tooltipsC' title='Cliente Bloqueado: $aux_descbloq'>
                                    <i class='fa fa-fw fa-ban text-danger'></i>
                                </a>";
            }else{
                $ruta_nuevoSolDesp = route('crearsol_despachosol', ['id' => $data->id]);
                $nuevoOrdDesp = "<a href='$ruta_nuevoOrdDesp' class='btn-accion-tabla tooltipsC' title='Hacer orden despacho: $data->tipentnombre'>
                                    <i class='fa fa-fw $data->icono'></i>
                                </a>";    
            }

            $respuesta['tabla'] .= "
            <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                <td id='id$i' name='id$i'>$data->id
                </td>
                <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                <td id='fechaestdesp$i' name='fechaestdesp$i'>" . date('d-m-Y', strtotime($data->fechaestdesp)) . "</td>
                <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                <td>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD($data->id,1)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>$data->id
                    </a>
                </td>
                <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                <td>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV($data->notaventa_id,1)'>
                    <i class='fa fa-fw fa-file-pdf-o'></i>$data->notaventa_id
                    </a>
                </td>
                <td id='comuna$i' name='comuna$i'>$data->comunanombre</td>
                <td style='text-align:right'>".
                    number_format($data->totalkilos - $data->totalkilosdesp, 2, ",", ".") .
                "</td>
                <td>
                    $nuevoOrdDesp
                </td>
            </tr>";

            //dd($data->contacto);
        }

        $respuesta['tabla'] .= "
        </tbody>
        </table>";
        return $respuesta;
    }

}

function consultasoldesp($request){
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
    if(empty($request->fechaestdesp)){
        $aux_condfechaestdesp = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechaestdesp);
        $fechad = date_format($fecha, 'Y-m-d');
        $aux_condfechaestdesp = "despachosol.fechaestdesp='$fechad'";
    }

    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);

    $sql = "SELECT despachosol.id,despachosol.fechahora,notaventa.cliente_id,cliente.rut,cliente.razonsocial,notaventa.oc_id,
            notaventa.oc_file,
            comuna.nombre as comunanombre,
            despachosol.notaventa_id,despachosol.fechaestdesp,tipoentrega.nombre as tipentnombre,tipoentrega.icono,
            IFNULL(vista_despordxdespsoltotales.totalkilos,0) as totalkilosdesp,
            vista_despsoltotales.totalkilos
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
            INNER JOIN tipoentrega
            ON tipoentrega.id=despachosol.tipoentrega_id
            INNER JOIN vista_despsoltotales
            ON despachosol.id = vista_despsoltotales.id
            LEFT JOIN vista_despordxdespsoltotales
            ON despachosol.id = vista_despordxdespsoltotales.despachosol_id
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
            and $aux_condfechaestdesp
            and despachosol.deleted_at is null AND notaventa.deleted_at is null AND notaventadetalle.deleted_at is null
            GROUP BY despachosol.id
            ORDER BY despachosol.id DESC;";
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