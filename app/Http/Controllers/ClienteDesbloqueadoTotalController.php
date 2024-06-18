<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarClienteDesbloqueadoTotal;
use App\Models\Cliente;
use App\Models\ClienteDesbloqueadoTotal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteDesbloqueadoTotalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cliente-desbloqueado-total');
        return view('clientedesbloqueadototal.index');
    }

    public function clientedesbloqueadototalpage(){
        $sql = "SELECT clientedesbloqueadototal.id,
            clientedesbloqueadototal.obs,
            clientedesbloqueadototal.cliente_id,cliente.rut,cliente.razonsocial
            from clientedesbloqueadototal inner join cliente
            on clientedesbloqueadototal.cliente_id = cliente.id
            where isnull(clientedesbloqueadototal.deleted_at) 
            and isnull(cliente.deleted_at)
            GROUP BY clientedesbloqueadototal.id
        ";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
/*
        return datatables()
        ->eloquent(ClienteBloqueadoCliente::query()
        )->toJson();
        
        return datatables()
        ->collection(ClienteBloqueado::join('cliente', 'clientedesbloqueadototal.cliente_id', '=', 'cliente.id')
        )->toJson();*/
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cliente-desbloqueado-total');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        return view('clientedesbloqueadototal.crear', compact('clientes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarClienteDesbloqueadoTotal $request)
    {
        //dd($request);
        can('guardar-cliente-desbloqueado-total');
        $clientedesbloqueadototal = ClienteDesbloqueadoTotal::where("cliente_id",$request->cliente_id)
                                    ->get();
        if(count($clientedesbloqueadototal) > 0){
            $mensaje = 'Ya existe un desbloqueo TOTAL por RUT de cliente!';
            return redirect('clientedesbloqueadototal')->with([
                'mensaje' => $mensaje,
                'tipo_alert' => 'alert-error'
            ]);
        }
        $request->request->add(['usuario_id' => auth()->id()]);
        $clientedesbloqueadototal = ClienteDesbloqueadoTotal::create($request->all());
        return redirect('clientedesbloqueadototal')->with('mensaje','Creado con exito!');
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
        can('editar-cliente-desbloqueado-total');
        $data = ClienteDesbloqueadoTotal::findOrFail($id);
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        return view('clientedesbloqueadototal.editar', compact('data','clientes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarClienteDesbloqueadoTotal $request, $id)
    {
        ClienteDesbloqueadoTotal::findOrFail($id)->update($request->all());
        return redirect('clientedesbloqueadototal')->with('mensaje','Actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-cliente-desbloqueado-total',false)){
            if ($request->ajax()) {
                if (ClienteDesbloqueadoTotal::destroy($request->id)) {
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $clientedesbloqueadototal = ClienteDesbloqueadoTotal::withTrashed()->findOrFail($request->id);
                    $clientedesbloqueadototal->usuariodel_id = auth()->id();
                    $clientedesbloqueadototal->save();
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

    public function buscarclidesbloq(Request $request)
    {
        if ($request->ajax()) {
            $datas = ClienteDesbloqueadoTotal::where('cliente_id','=',$request->id);
    
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