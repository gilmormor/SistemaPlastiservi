<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarClienteDesBloqueado;
use App\Models\Cliente;
use App\Models\ClienteDesBloqueado;
use App\Models\ClienteDesbloqueadoModulo;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteDesBloqueadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cliente-desbloqueado');
        return view('clientedesbloqueado.index');
    }

    public function clientedesbloqueadopage(){
        $sql = "SELECT clientedesbloqueado.id,clientedesbloqueado.notaventa_id,clientedesbloqueado.cotizacion_id,
            clientedesbloqueado.obs,
            clientedesbloqueado.cliente_id,cliente.rut,cliente.razonsocial,
            GROUP_CONCAT(DISTINCT modulo.nombre ORDER BY modulo.orden) AS modulo_nombre
            from clientedesbloqueado inner join cliente
            on clientedesbloqueado.cliente_id = cliente.id
            LEFT JOIN clientedesbloqueadomodulo
            ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id
            LEFT JOIN modulo
            ON modulo.id = clientedesbloqueadomodulo.modulo_id
            where isnull(clientedesbloqueado.deleted_at) 
            and isnull(cliente.deleted_at)
            GROUP BY clientedesbloqueado.id
        ";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
/*
        return datatables()
        ->eloquent(ClienteBloqueadoCliente::query()
        )->toJson();
        
        return datatables()
        ->collection(ClienteBloqueado::join('cliente', 'clientedesbloqueado.cliente_id', '=', 'cliente.id')
        )->toJson();*/
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear($id)
    {
        can('crear-cliente-desbloqueado');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $modulos = Modulo::orderBy('orden')
                    ->where("stamodapl","=",$id)
                    ->get();
        $aux_sta = $id;
        return view('clientedesbloqueado.crear', compact('clientes','modulos','aux_sta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarClienteDesBloqueado $request)
    {
        //dd($request);
        can('guardar-cliente-desbloqueado');
        if(isset($request->notaventa_id)){
            $clientedesbloqueado = ClienteDesBloqueado::where("cliente_id",$request->cliente_id)
            ->where("notaventa_id",$request->notaventa_id)
            ->get();
        }else{
            if(isset($request->cotizacion_id)){
                $clientedesbloqueado = ClienteDesBloqueado::where("cliente_id",$request->cliente_id)
                ->where("cotizacion_id",$request->cotizacion_id)
                ->get();
            }else{
                $clientedesbloqueado = ClienteDesBloqueado::where("cliente_id",$request->cliente_id)
                ->whereNull('notaventa_id')
                ->whereNull('cotizacion_id')
                ->get();
            }    
        }
        if(count($clientedesbloqueado) > 0){
            if(isset($request->notaventa_id)){
                $mensaje = 'Ya existe un desbloqueo por Id Nota de Venta!';
            }else{
                if(isset($request->cotizacion_id)){
                    $mensaje = 'Ya existe desbloqueo Cotizacion Nro: ' . $request->cotizacion_id;
                }else{
                    $mensaje = 'Ya existe desbloqueo por RUT de cliente!';
                }
            }
            return redirect('clientedesbloqueado')->with([
                'mensaje' => $mensaje,
                'tipo_alert' => 'alert-error'
            ]);
        }
        $request->request->add(['usuario_id' => auth()->id()]);
        $clientedesbloqueado = ClienteDesBloqueado::create($request->all());
        $clientedesbloqueado->modulos()->sync($request->modulo_id);
        return redirect('clientedesbloqueado')->with('mensaje','Creado con exito');
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
        can('editar-cliente-desbloqueado');
        $data = ClienteDesBloqueado::findOrFail($id);
        //dd(count($data->clientedesbloqueadomodulos));
        $aux_sta = 0;
        if($data->notaventa_id != null){
            $aux_sta = 1;
        }else{
            if($data->cotizacion_id != null){
                $aux_sta = 2;
            }
        }
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $modulos = Modulo::orderBy('orden')
                    ->where("stamodapl","=",$aux_sta)
                    ->get();
        return view('clientedesbloqueado.editar', compact('data','clientes','aux_sta','modulos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarClienteDesBloqueado $request, $id)
    {
        $clientedesbloqueado = ClienteDesBloqueado::findOrFail($id);
        ClienteDesBloqueado::findOrFail($id)->update($request->all());
        $clientedesbloqueado->modulos()->sync($request->modulo_id);
        return redirect('clientedesbloqueado')->with('mensaje','Actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-cliente-desbloqueado',false)){
            if ($request->ajax()) {
                if (ClienteDesBloqueado::destroy($request->id)) {
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    ClienteDesbloqueadoModulo::where("clientedesbloqueado_id",$request->id)
                                                ->delete();
                    $clientedesbloqueado = ClienteDesBloqueado::withTrashed()->findOrFail($request->id);
                    $clientedesbloqueado->usuariodel_id = auth()->id();
                    $clientedesbloqueado->save();
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }    
            } else {
                abort(404);
            }
    
        }else{
            return response()->json(['mensaje' => 'ne']);
        }

    }

    public function buscarclibloq(Request $request)
    {
        if ($request->ajax()) {
            $datas = ClienteDesBloqueado::where('cliente_id','=',$request->id)
            ->whereNull('notaventa_id')
            ->whereNull('cotizacion_id');
            //dd($datas->count());
    
            $aux_contRegistos = $datas->count();
            //dd($aux_contRegistos);
            if($aux_contRegistos > 0){
                return response()->json(['mensaje' => 'ok']);
            }else{
                return response()->json(['mensaje' => 'ng']);   
            }
        } else {
            abort(404);
        }
    }
}