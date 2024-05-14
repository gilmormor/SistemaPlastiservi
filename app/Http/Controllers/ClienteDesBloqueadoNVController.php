<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarClienteDesBloqueadoNV;
use App\Models\Cliente;
use App\Models\ClienteDesBloqueadoNV;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteDesBloqueadoNVController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cliente-desbloqueado-nota-venta');
        return view('clientedesbloqueadonv.index');
    }

    public function clientedesbloqueadonvpage(){
        $sql = "SELECT clientedesbloqueadonv.id,clientedesbloqueadonv.obs,clientedesbloqueadonv.cliente_id,
            cliente.rut,cliente.razonsocial
            from clientedesbloqueadonv inner join cliente
            on clientedesbloqueadonv.cliente_id = cliente.id
            where isnull(clientedesbloqueadonv.deleted_at) 
            and isnull(cliente.deleted_at);
        ";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
/*
        return datatables()
        ->eloquent(ClienteBloqueadoCliente::query()
        )->toJson();
        
        return datatables()
        ->collection(ClienteBloqueado::join('cliente', 'clientedesbloqueadonv.cliente_id', '=', 'cliente.id')
        )->toJson();*/
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cliente-desbloqueado-nota-venta');
        /*
        $user = Usuario::findOrFail(auth()->id());
        //$vendedor_id=$user->persona->vendedor->id;
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
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
    
        // Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->whereNotIn('cliente.id', ClienteBloqueado::pluck('cliente_id')->toArray())
        ->get();
        */
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];

        $aux_editar = 0;
        return view('clientedesbloqueadonv.crear', compact('clientes','aux_editar'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarClienteDesBloqueadoNV $request)
    {
        can('guardar-cliente-desbloqueado-nota-venta');
        //dd($request);
        $request->request->add(['usuario_id' => auth()->id()]);
        $clientedesbloqueadonv = ClienteDesBloqueadoNV::create($request->all());
        return redirect('clientedesbloqueadonv')->with('mensaje','Creado con exito');
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
        can('editar-cliente-desbloqueado-nota-venta');
        $data = ClienteDesBloqueadoNV::findOrFail($id);
        
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];

        $aux_editar = 1;
        return view('clientedesbloqueadonv.editar', compact('data','clientes','aux_editar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarClienteDesBloqueadoNV $request, $id)
    {
        $clientedesbloqueadonv = ClienteDesBloqueadoNV::findOrFail($id);
        ClienteDesBloqueadoNV::findOrFail($id)->update($request->all());
        return redirect('clientedesbloqueadonv')->with('mensaje','Actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-cliente-desbloqueado-nota-venta',false)){
            if ($request->ajax()) {
                if (ClienteDesBloqueadoNV::destroy($request->id)) {
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $clientedesbloqueadonv = ClienteDesBloqueadoNV::withTrashed()->findOrFail($request->id);
                    $clientedesbloqueadonv->usuariodel_id = auth()->id();
                    $clientedesbloqueadonv->save();
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
            $datas = ClienteDesBloqueadoNV::where('cliente_id','=',$request->id);
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