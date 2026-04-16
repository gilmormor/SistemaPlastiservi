<?php

namespace App\Http\Controllers;

use App\Models\CategoriaGrupoValMes;
use App\Models\EtapaProd;
use App\Models\InvBodega;
use App\Models\Maquina;
use App\Models\InvBodegaProducto;
use App\Models\InvControl;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\InvMovDetOpDetRegProd;
use App\Models\OpDet;
use App\Models\OpDetRegProd;
use App\Models\OpDetRegProdTemp;
use App\Models\Produccion;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpDetRegProdTempAprobSupController extends Controller
{
    public function etapaprod()
    {
        can('listar-registro-produccion-aprobar-supervidor');
        $data = EtapaProd::etapasProdxPersona();
        $aux_contEtapasProd = count($data);
        if($aux_contEtapasProd==0){
            return redirect()->route('inicio')->with('mensaje','No tiene etapas de produccion asignadas, consulte con el administrador del sistema.')->send();
        }
        if($aux_contEtapasProd==1){
            session(['etapaprod_id' => $data[0]->etapaprod_id]);
            return redirect()->route('opdetregprodtempaprobsup');
        }else{
            return redirect()->route('opdetregprodtempaprobsup_selecetapaprod');
        }
    }

    public function selecetapaprod(){
        can('listar-registro-produccion-aprobar-supervidor');
        return view('opdetregprodtempaprobsup.selecetapaprod');
    }
    public function selecetapaprodpage(){
        $datas = EtapaProd::etapasProdxPersona();
        return datatables($datas)->toJson();
    }

    public function setId($id)
    {
        session(['etapaprod_id' => $id]);
        return redirect()->route('opdetregprodtempaprobsup_index01');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-registro-produccion-aprobar-supervidor');
        $data = EtapaProd::etapasProdxPersona();
        $aux_contEtapasProd = count($data);
        if($aux_contEtapasProd==0){
            return redirect()->route('inicio')->with('mensaje','No tiene etapas de produccion asignadas, consulte con el administrador del sistema.')->send();
        }
        if($aux_contEtapasProd==1){
            session(['etapaprod_id' => $data[0]->etapaprod_id]);
            return redirect()->route('opdetregprodtempaprobsup_index01');
        }else{
            return redirect()->route('opdetregprodtempaprobsup_selecetapaprod');
        }

        //$datas = FormaPago::orderBy('id')->get();
        //return view('opdetregprodtempaprobsup.index');
    }

    public function index01()
    {
        can('listar-registro-produccion-aprobar-supervidor');
        //dd(session('etapaprod_id'));
        //$datas = FormaPago::orderBy('id')->get();
        $tablas["etapaprod"] = EtapaProd::findOrFail(session('etapaprod_id'));
        return view('opdetregprodtempaprobsup.index', compact('tablas'));
    }

    public function opdetregprodtempaprobsuppage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_etapaprod_id = session('etapaprod_id');

        $aux_statusaprob = "opdetregprodtemp.aprobstatus = 1";
        
        $sql = "SELECT opdetregprodtemp.id,opdet.op_id,opdetregprodtemp.opdet_id,
                sucursal.nombre AS sucursal_nombre,
                cliente.razonsocial,ot.id as ot_id,producto.id as producto_id,
                opdet.kg as opdet_kg,opdet.cant as opdet_cant,opdet.cantrec as opdet_cantrec,
                opdet.kgrec as opdet_kgrec,opdet.cantprod as opdet_cantprod,
                opdet.kgprod as opdet_kgprod,opdet.kgscrap as opdet_kgscrap,
                opdetregprodtemp.kg,opdetregprodtemp.kgscrap,opdetregprodtemp.cant,
                (opdet.kgrec - (opdet.kgprod + opdetregprodtemp.kg + opdetregprodtemp.kgscrap)) as kgsaldo,
                opdetregprodtemp.updated_at,
                UNIX_TIMESTAMP(opdetregprodtemp.updated_at) as updatednum_at
                from opdetregprodtemp INNER JOIN sucursal 
                ON opdetregprodtemp.sucursal_id=sucursal.id
                INNER JOIN producto
                ON opdetregprodtemp.producto_id=producto.id
                INNER JOIN etapaprod
                ON opdetregprodtemp.etapaprod_id=etapaprod.id 
                INNER JOIN opdet
                ON opdetregprodtemp.opdet_id=opdet.id
                INNER JOIN op
                ON opdet.op_id=op.id
                INNER JOIN otdet
                ON op.otdet_id=otdet.id
                INNER JOIN ot
                ON otdet.ot_id=ot.id
                INNER JOIN cliente
                ON ot.cliente_id=cliente.id
                where opdetregprodtemp.sucursal_id IN ($sucurcadena) 
                AND etapaprod.id=$aux_etapaprod_id
                AND $aux_statusaprob
                AND isnull(opdetregprodtemp.deleted_at);";
        //dd($sql);
        $datas = DB::select($sql);
        foreach ($datas as &$data) {
            $data->producto_nombre = Producto::atributosProducto($data->producto_id)["nombre"];
        }
        return datatables($datas)->toJson();
    }

    public function listaropdet()
    {
        $fechaAct = date("d/m/Y");
        $user = Usuario::findOrFail(auth()->id());
        $tablashtml['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablashtml['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablashtml['sucurArray'])->get();
        $tablashtml["etapaprod"] = EtapaProd::findOrFail(session('etapaprod_id'));
        return view('opdetregprodtempaprobsup.listaropdet', compact('fechaAct','tablashtml'));
    }
    public function listaropdetpage(Request $request){
        $datas = consultaopdet($request);
        return datatables($datas)->toJson();
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
    public function editar($cadena)
    {
        can('editar-registro-produccion');
        // Separamos por el carácter "&"
        list($id, $updatednum_at) = explode("&", $cadena);
        $data = OpDetRegProdTemp::findOrFail($id);
        if(strtotime($data->updated_at) != $updatednum_at){
            return redirect('opdetregprodtempaprobsup/index01')->with([
                'mensaje'=>'Registro Editado por otro usuario. Fecha Hora: '.$data->updated_at,
                'tipo_alert' => 'alert-error'
            ]);    
        }
        //dd($id);
        /* $opdet_id = session('opdet_id');
        $updatednum_at = session('opdet_updatednum_at'); */
        $opdet = OpDet::findOrFail($data->opdet_id);
        $tablas["operarios"] = consultaOperarios($data->etapaprod_id);
        return view('opdetregprodtempaprobsup.editar', compact('data','opdet','tablas'));
    }

    public function aprob(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            // 1️⃣ Obtener el registro base
            $opdetregprodtemp = OpDetRegProdTemp::findOrFail($request->id);
            $aux_etapaprod_orden = $opdetregprodtemp->opdet->areaproduccionsucetapaprod->orden;
            $op = $opdetregprodtemp->opdet->op;
            foreach ($op->opdets as &$opdet) {
                $opdet->orden = $opdet->areaproduccionsucetapaprod->orden;
            }

            if($opdetregprodtemp == null){
                return response()->json([
                    'resp' => 0,
                    'tipmen' => 'error',
                    'mensaje' => 'Registro eliminado por otro usuario.'
                ]);
            }
            if(strtotime($opdetregprodtemp->updated_at) != $request->updated_at){
                return response()->json([
                    'resp' => 0,
                    'tipmen' => 'error',
                    'mensaje'=>'Registro fué modificado por otro usuario.'
                ]);
            }
            //dd($request);

            DB::beginTransaction();
            try {
                $usuario = Usuario::findOrFail(auth()->id());
                $opdet = OpDet::findOrFail($opdetregprodtemp->opdet_id);
                $opdet->updated_at = date("Y-m-d H:i:s");
                $opdetregprodtemp->updated_at = date("Y-m-d H:i:s");
                $opdetregprodtemp->aprobstatus = $request->staaprob;
                if($request->obsaprob){
                    //$opdetregprodtemp->aprobobs = ($request->obsaprob ?? "") . $request->obsaprob ?? " / Usuario: " . auth()->id() . " " . $usuario->nombre . " " . date("d-m-Y H:i:s");
                    $opdetregprodtemp->aprobobs = $request->obsaprob;
                    if($opdetregprodtemp->aprobstatus == 3){
                        $opdetregprodtemp->aprobobs .= " / Usuario: " . auth()->id() . " " . $usuario->nombre . " " . date("d-m-Y H:i:s");
                    }
                }
                $esUltimaEtapa = false;
                if($opdetregprodtemp->aprobstatus == 2){
                    // 1️⃣ Copiar todos los atributos del temp al registro definitivo
                    $data = $opdetregprodtemp->toArray();
                    unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
                    $data['opdetregprodtemp_id'] = $opdetregprodtemp->id;
                    $data['usuario_id'] = auth()->id();

                    // 2️⃣ Crear el registro definitivo en opdetregprod (necesario antes del else
                    //    para poder usar $produccion->id en la trazabilidad de inventario)
                    $produccion = OpDetRegProd::create($data);

                    // 3️⃣ Obtener la siguiente etapa de producción (la de mayor orden inmediato)
                    $siguienteEtapa = collect($op->opdets)
                        ->where('orden', '>', $aux_etapaprod_orden)
                        ->sortBy('orden')
                        ->first();

                    if ($siguienteEtapa) {
                        // Hay siguiente etapa: transferir kg/cant a la siguiente etapa
                        $opdetSig = OpDet::findOrFail($siguienteEtapa->id);
                        if($opdetSig->kgrec == 0){
                            $opdetSig->saldokg = $opdetregprodtemp->kg;
                        }else{
                            $opdetSig->saldokg += $opdetregprodtemp->kg;
                        }
                        $opdetSig->cantrec += $opdetregprodtemp->cant;
                        $opdetSig->kgrec   += $opdetregprodtemp->kg;
                        $opdetSig->save();

                    } else {
                        // 4️⃣ Última etapa: ingresar producto a bodega de Producción (tipo=5)
                        $esUltimaEtapa = true;

                        // Verificar período de inventario no cerrado
                        $annomes = CategoriaGrupoValMes::annomes(date('Ym'));
                        $periodoVigente = InvControl::where('annomes', $annomes)
                            ->where('sucursal_id', $opdetregprodtemp->sucursal_id)
                            ->where('status', 1)
                            ->count();
                        if ($periodoVigente > 0) {
                            throw new \Exception('El período ' . $annomes . ' está cerrado. No se puede ingresar a bodega de producción.');
                        }

                        // Buscar bodegas tipo=5 de la sucursal
                        $bodegasProduccion = InvBodega::where('tipo', 5)
                            ->where('sucursal_id', $opdetregprodtemp->sucursal_id)
                            ->whereNull('deleted_at')
                            ->get();

                        if ($bodegasProduccion->isEmpty()) {
                            throw new \Exception('No existe bodega de producción (tipo=5) configurada para esta sucursal.');
                        }

                        // Si hay más de 1 bodega y el usuario aún no eligió, solicitar selección
                        if ($bodegasProduccion->count() > 1 && !$request->invbodega_id) {
                            DB::rollBack();
                            return response()->json([
                                'resp'    => 2,
                                'tipmen'  => 'info',
                                'mensaje' => 'Seleccione la bodega de producción destino.',
                                'bodegas' => $bodegasProduccion->map(function($b){
                                    return ['id' => $b->id, 'nombre' => $b->nombre];
                                }),
                            ]);
                        }

                        $invbodega_id = $request->invbodega_id
                            ? (int)$request->invbodega_id
                            : $bodegasProduccion->first()->id;

                        // Crear el movimiento de inventario de entrada
                        $invmov = InvMov::create([
                            'fechahora'       => date('Y-m-d H:i:s'),
                            'annomes'         => $annomes,
                            'desc'            => 'Ingreso producción — última etapa',
                            'obs'             => 'OP: ' . $opdetregprodtemp->opdet->op_id,
                            'invmovmodulo_id' => 9,               // Producción
                            'idmovmod'        => $produccion->id, // Trazabilidad → opdetregprod
                            'invmovtipo_id'   => 1,               // Entrada
                            'sucursal_id'     => $opdetregprodtemp->sucursal_id,
                            'usuario_id'      => auth()->id(),
                        ]);

                        // Buscar o crear el registro InvBodegaProducto
                        $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                            [
                                'producto_id'  => $opdetregprodtemp->producto_id,
                                'invbodega_id' => $invbodega_id,
                            ],
                            [
                                'producto_id'  => $opdetregprodtemp->producto_id,
                                'invbodega_id' => $invbodega_id,
                            ]
                        );

                        // Crear el detalle del movimiento
                        $producto = Producto::findOrFail($opdetregprodtemp->producto_id);
                        $invmovdet = InvMovDet::create([
                            'invmov_id'            => $invmov->id,
                            'invbodegaproducto_id' => $invbodegaproducto->id,
                            'producto_id'          => $opdetregprodtemp->producto_id,
                            'invbodega_id'         => $invbodega_id,
                            'sucursal_id'          => $opdetregprodtemp->sucursal_id,
                            'unidadmedida_id'      => $producto->categoriaprod->unidadmedida_id,
                            'invmovtipo_id'        => 1,
                            'cant'                 => $opdetregprodtemp->cant,
                            'cantgrupo'            => $opdetregprodtemp->cant,
                            'cantxgrupo'           => 1,
                            'peso'                 => $producto->peso,
                            'cantkg'               => $opdetregprodtemp->kg,
                        ]);

                        // Registrar trazabilidad: invmovdet ↔ opdetregprod
                        InvMovDetOpDetRegProd::create([
                            'invmovdet_id'    => $invmovdet->id,
                            'opdetregprod_id' => $produccion->id,
                        ]);
                    }
                }
                $opdet->save();
                $opdetregprodtemp->save();

                DB::commit();

                $respData = [
                    'resp'    => 1,
                    'tipmen'  => 'success',
                    'mensaje' => 'Actualizado con exito.',
                ];
                // Si se aprobó (no rechazado), incluir id de producción para etiquetas
                if ($opdetregprodtemp->aprobstatus == 2 && isset($produccion)) {
                    $respData['opdetregprod_id'] = $produccion->id;
                    $respData['es_ultima_etapa'] = $esUltimaEtapa ? 1 : 0;
                }
                return response()->json($respData);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'resp' => 0,
                    'tipmen' => 'error',
                    'mensaje'=> "Error: " . $e->getMessage(),
                    'tipo_alert' => 'alert-error'
                ]);
            }
        }
    }

    /**
     * Etiqueta de bodega (última etapa) — con QR apuntando a opdetregprod_id.
     * Sirve tanto para impresión inmediata como para reimpresión posterior.
     */
    public function etiquetaBodega($opdetregprod_id)
    {
        can('listar-registro-produccion-aprobar-supervidor');
        $produccion = OpDetRegProd::findOrFail($opdetregprod_id);
        $opdet = $produccion->opdet;
        $op    = $opdet->op;
        $otdet = $op->otdet;
        $ot    = $otdet->ot;

        // Nombre del producto usando la función global del proyecto
        $productoData = Producto::atributosProducto($produccion->producto_id);
        $productoNombre = $productoData['nombre'] ?? '—';
        $productoCodigo = $produccion->producto_id;

        // Nota de Venta (si el OT tiene relación con NV)
        $notaventa_id = null;
        if ($otdet->otdetnvdet && $otdet->otdetnvdet->notaventadetalle) {
            $notaventa_id = $otdet->otdetnvdet->notaventadetalle->notaventa_id;
        }

        return view('opdetregprodtempaprobsup.etiqueta-bodega', compact(
            'produccion', 'opdet', 'op', 'otdet', 'ot',
            'productoNombre', 'productoCodigo', 'notaventa_id'
        ));
    }

    /**
     * Etiqueta de etapa intermedia — muestra datos de la etapa actual y la próxima.
     * Sirve tanto para impresión inmediata como para reimpresión posterior.
     */
    public function etiquetaEtapa($opdetregprod_id)
    {
        can('listar-registro-produccion-aprobar-supervidor');
        $produccion = OpDetRegProd::findOrFail($opdetregprod_id);
        $opdet = $produccion->opdet;
        $op    = $opdet->op;
        $otdet = $op->otdet;
        $ot    = $otdet->ot;

        // Nombre del operario
        $operario = \App\Models\Operario::find($produccion->operario_id);
        $operarioNombre = $operario ? $operario->nombre : '—';

        // Máquina asociada al opdet (si existe)
        $maquinaNombre = '—';
        if ($opdet->opdetmaquina && $opdet->opdetmaquina->maquina) {
            $maquinaNombre = $opdet->opdetmaquina->maquina->nombre;
        }

        // Próxima etapa de producción
        $etapaActualOrden = $opdet->areaproduccionsucetapaprod->orden;
        foreach ($op->opdets as &$od) {
            $od->orden = $od->areaproduccionsucetapaprod->orden;
        }
        $siguienteOpdet = collect($op->opdets)
            ->where('orden', '>', $etapaActualOrden)
            ->sortBy('orden')
            ->first();
        $proximaEtapaNombre = $siguienteOpdet
            ? $siguienteOpdet->areaproduccionsucetapaprod->etapaprod->nombre
            : 'Última etapa (Bodega)';

        // Nombre del producto
        $productoData   = Producto::atributosProducto($produccion->producto_id);
        $productoNombre = $productoData['nombre'] ?? '—';

        return view('opdetregprodtempaprobsup.etiqueta-etapa', compact(
            'produccion', 'opdet', 'op', 'otdet', 'ot',
            'productoNombre', 'operarioNombre', 'maquinaNombre', 'proximaEtapaNombre'
        ));
    }

}
