<?php

namespace App\Http\Controllers;

use App\Models\EtapaProd;
use App\Models\OpDet;
use App\Models\OpDetRegProd;
use App\Models\OpDetRegProdTemp;
use App\Models\Produccion;
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
                cliente.razonsocial,
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
                $opdetregprodtemp->aprobstatus = $request->staaprob;
                if($request->obsaprob){
                    //$opdetregprodtemp->aprobobs = ($request->obsaprob ?? "") . $request->obsaprob ?? " / Usuario: " . auth()->id() . " " . $usuario->nombre . " " . date("d-m-Y H:i:s");
                    $opdetregprodtemp->aprobobs = $request->obsaprob;
                    if($opdetregprodtemp->aprobstatus == 3){
                        $opdetregprodtemp->aprobobs .= " / Usuario: " . auth()->id() . " " . $usuario->nombre . " " . date("d-m-Y H:i:s");
                    }
                }
                $opdetregprodtemp->save();
                if($opdetregprodtemp->aprobstatus == 2){
                    // Obtenemos la siguiente etapa de producción (la de mayor orden inmediato)
                    $siguienteEtapa = collect($op->opdets)
                        ->where('orden', '>', $aux_etapaprod_orden)
                        ->sortBy('orden')
                        ->first();

                    if ($siguienteEtapa) {
                        // Aquí ya tienes la siguiente etapa
                        // Ejemplo: $siguienteEtapa->id o $siguienteEtapa->orden
                        // Le asigno los kg y cant producidos a la siguiente etapa
                        $opdet = OpDet::findOrFail($siguienteEtapa->id);
                        //dd($opdet);
                        if($opdet->kgrec == 0){
                            $opdet->saldokg = $opdetregprodtemp->kg;
                        }else{
                            $opdet->saldokg += $opdetregprodtemp->kg;
                        }
                        $opdet->cantrec += $opdetregprodtemp->cant;
                        $opdet->kgrec += $opdetregprodtemp->kg; 
                        $opdet->save();
                        //dd($siguienteEtapa);+
                    } else {
                        dd($op->otdet->otdetnvdet);
                        dd("Fin de la producción");
                        // No hay una siguiente etapa (es la última)
                        //dd('No hay siguiente etapa');
                        //Aqui deberia abrir la ventana de produccion finalizada y abrir una pantalla de inventario para asignar el producto final a un bodega y actualizar inventarios. 
                        //Tambien debe generarse automaticamente un movimiento de inventario de entrada.
                        //Tambien se debe activar el item en notaventadetalle para que pueda ser preparado por el modulo de solicitud de despacho e iniciar el proceso de despacho.
                    }

                    // 2️⃣ Copiar todos los atributos
                    $data = $opdetregprodtemp->toArray();

                    // 3️⃣ Eliminar campos que no deben copiarse (como el id o timestamps si no los necesitas)
                    unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);

                    // 4️⃣ Asignar los valores personalizados
                    $data['opdetregprodtemp_id'] = $opdetregprodtemp->id;
                    $data['usuario_id'] = auth()->id();

                    // 5️⃣ Crear el nuevo registro en la tabla produccion

                    $produccion = OpDetRegProd::create($data);                    
                }
                
                DB::commit();
                return response()->json([
                    'resp' => 1,
                    'tipmen' => 'success',
                    'mensaje' => 'Actualizado con exito.'
                ]);

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
    
}
