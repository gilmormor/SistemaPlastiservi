<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarClienteDesbloqueadoPro;
use App\Models\Cliente;
use App\Models\ClienteDesbloqueadoPro;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteDesbloqueadoProController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cliente-desbloqueado-pro');
        return view('clientedesbloqueadopro.index');
    }

    public function clientedesbloqueadopropage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $arraySucFisxUsu = implode(",", sucFisXUsu($user->persona));

        $sql = "SELECT clientedesbloqueadopro.id,
            clientedesbloqueadopro.obs,
            clientedesbloqueadopro.cliente_id,cliente.rut,cliente.razonsocial
            FROM clientedesbloqueadopro INNER JOIN cliente
            ON clientedesbloqueadopro.cliente_id = cliente.id
            WHERE isnull(clientedesbloqueadopro.deleted_at) 
            AND isnull(cliente.deleted_at)
            AND cliente.id IN (SELECT cliente_sucursal.cliente_id 
                    FROM cliente_sucursal
                    WHERE cliente_sucursal.cliente_id  = cliente.id
                    AND cliente_sucursal.sucursal_id IN ($arraySucFisxUsu))
            GROUP BY clientedesbloqueadopro.id
        ";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
/*
        return datatables()
        ->eloquent(ClienteBloqueadoCliente::query()
        )->toJson();
        
        return datatables()
        ->collection(ClienteBloqueado::join('cliente', 'clientedesbloqueadopro.cliente_id', '=', 'cliente.id')
        )->toJson();*/
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cliente-desbloqueado-pro');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        return view('clientedesbloqueadopro.crear', compact('clientes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarClienteDesbloqueadoPro $request)
    {
        //dd($request);
        can('guardar-cliente-desbloqueado-pro');
        $clientedesbloqueadopro = ClienteDesbloqueadoPro::where("cliente_id",$request->cliente_id)
                                    ->get();
        if(count($clientedesbloqueadopro) > 0){
            $mensaje = 'Ya existe un desbloqueo TOTAL por RUT de cliente!';
            return redirect('clientedesbloqueadopro')->with([
                'mensaje' => $mensaje,
                'tipo_alert' => 'alert-error'
            ]);
        }
        $request->request->add(['usuario_id' => auth()->id()]);
        $clientedesbloqueadopro = ClienteDesbloqueadoPro::create($request->all());
        return redirect('clientedesbloqueadopro')->with('mensaje','Creado con exito!');
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
        can('editar-cliente-desbloqueado-pro');
        $data = ClienteDesbloqueadoPro::findOrFail($id);
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        return view('clientedesbloqueadopro.editar', compact('data','clientes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarClienteDesbloqueadoPro $request, $id)
    {
        ClienteDesbloqueadoPro::findOrFail($id)->update($request->all());
        return redirect('clientedesbloqueadopro')->with('mensaje','Actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-cliente-desbloqueado-pro',false)){
            if ($request->ajax()) {
                if (ClienteDesbloqueadoPro::destroy($request->id)) {
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $clientedesbloqueadopro = ClienteDesbloqueadoPro::withTrashed()->findOrFail($request->id);
                    $clientedesbloqueadopro->usuariodel_id = auth()->id();
                    $clientedesbloqueadopro->save();
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
            $datas = ClienteDesbloqueadoPro::where('cliente_id','=',$request->id);
    
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