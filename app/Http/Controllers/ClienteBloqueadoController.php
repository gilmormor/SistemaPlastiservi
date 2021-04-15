<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarClienteBloqueado;
use App\Mail\MailClienteBloqueado;
use App\Models\Cliente;
use App\Models\ClienteBloqueado;
use App\Models\ClienteBloqueadoCliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Notificaciones;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables\Editor;

class ClienteBloqueadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cliente-bloqueado');
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $datas = ClienteBloqueado::whereIn('clientebloqueado.cliente_id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->get();
        $aux_razonsocial = $datas[0]->cliente->razonsocial;
        //dd($aux_razonsocial);
        //dd($datas[0]->cliente->id);
        //$datas = ClienteBloqueado::orderBy('id')->get();
        //dd($datas[0]->cliente);
        return view('clientebloqueado.index', compact('datas','sucursales'));
    }

    public function clientebloqueadopage(){
        $sql = "SELECT clientebloqueado.id,clientebloqueado.descripcion,clientebloqueado.cliente_id,
            cliente.razonsocial
            from clientebloqueado inner join cliente
            on clientebloqueado.cliente_id = cliente.id
            where isnull(clientebloqueado.deleted_at) 
            and isnull(cliente.deleted_at);
        ";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
/*
        return datatables()
        ->eloquent(ClienteBloqueadoCliente::query()
        )->toJson();
        
        return datatables()
        ->collection(ClienteBloqueado::join('cliente', 'clientebloqueado.cliente_id', '=', 'cliente.id')
        )->toJson();*/
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cliente-bloqueado');
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
        return view('clientebloqueado.crear', compact('clientes','aux_editar'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarClienteBloqueado $request)
    {
        can('guardar-cliente-bloqueado');
        //dd($request);
        $request->request->add(['usuario_id' => auth()->id()]);
        $clientebloqueado = ClienteBloqueado::create($request->all());
        if($clientebloqueado){
            $asunto = 'Cliente Bloqueado';
            $cuerpo = "Cliente Bloqueado: Id $request->cliente_id";
            //$cliente = Cliente::findOrFail($request->cliente_id);
            foreach ($clientebloqueado->cliente->vendedores as $vendedor) {
                $notificaciones = new Notificaciones();
                $notificaciones->usuarioorigen_id = auth()->id();
                $notificaciones->usuariodestino_id = $vendedor->persona->usuario->id;
                $notificaciones->vendedor_id = $vendedor->id;
                $notificaciones->status = 1;                    
                $notificaciones->nombretabla = 'clientebloqueado';
                $notificaciones->mensaje = 'Cliente Bloqueado RUT: '.$clientebloqueado->cliente->rut;
                $notificaciones->mensajetitle = $clientebloqueado->descripcion;
                $notificaciones->nombrepantalla = 'clientebloqueado.index';
                $notificaciones->rutaorigen = 'clientebloqueado/crear';
                $notificaciones->rutadestino = 'reportclientes';
                $notificaciones->tabla_id = $clientebloqueado->id;
                $notificaciones->accion = 'Cliente Bloqueado.';
                $notificaciones->icono = 'fa fa-fw fa-lock text-red';
                $notificaciones->save();
    
                $nombrevendedor = $vendedor->persona->nombre . ' ' . $vendedor->persona->apellido;
                Mail::to($vendedor->persona->usuario->email)->send(new MailClienteBloqueado($clientebloqueado,$asunto,$cuerpo,$nombrevendedor));
            }

            

        }

        return redirect('clientebloqueado')->with('mensaje','Creado con exito');
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
        can('editar-cliente-bloqueado');
        $data = ClienteBloqueado::findOrFail($id);
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
        ->get();
        */
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];

        $aux_editar = 1;
        return view('clientebloqueado.editar', compact('data','clientes','aux_editar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarClienteBloqueado $request, $id)
    {
        $clientebloqueado = ClienteBloqueado::findOrFail($id);
        ClienteBloqueado::findOrFail($id)->update($request->all());
        $asunto = 'Cliente Bloqueado';
        $cuerpo = "Cliente Bloqueado: Id $request->cliente_id";
        foreach ($clientebloqueado->cliente->vendedores as $vendedor) {
            $notificaciones = new Notificaciones();
            $notificaciones->usuarioorigen_id = auth()->id();
            $notificaciones->usuariodestino_id = $vendedor->persona->usuario->id;
            $notificaciones->vendedor_id = $vendedor->id;
            $notificaciones->status = 1;                    
            $notificaciones->nombretabla = 'clientebloqueado';
            $notificaciones->mensaje = 'Cliente Bloqueado RUT: '.$clientebloqueado->cliente->rut;
            $notificaciones->mensajetitle = $clientebloqueado->descripcion;
            $notificaciones->nombrepantalla = 'clientebloqueado.index';
            $notificaciones->rutaorigen = 'clientebloqueado/crear';
            $notificaciones->rutadestino = 'reportclientes';
            $notificaciones->tabla_id = $clientebloqueado->id;
            $notificaciones->accion = 'Cliente Bloqueado.';
            $notificaciones->icono = 'fa fa-fw fa-lock text-red';
            $notificaciones->save();

            $nombrevendedor = $vendedor->persona->nombre . ' ' . $vendedor->persona->apellido;
            Mail::to($vendedor->persona->usuario->email)->send(new MailClienteBloqueado($clientebloqueado,$asunto,$cuerpo,$nombrevendedor));
        }
        return redirect('clientebloqueado')->with('mensaje','Actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-cliente-bloqueado',false)){
            if ($request->ajax()) {
                if (ClienteBloqueado::destroy($request->id)) {
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $clientebloqueado = ClienteBloqueado::withTrashed()->findOrFail($request->id);
                    $clientebloqueado->usuariodel_id = auth()->id();
                    $clientebloqueado->save();
                    $asunto = 'Cliente Desbloqueado';
                    $cuerpo = "Cliente Desbloqueado: Id $clientebloqueado->cliente_id";
                    foreach ($clientebloqueado->cliente->vendedores as $vendedor) {
                        $notificaciones = new Notificaciones();
                        $notificaciones->usuarioorigen_id = auth()->id();
                        $notificaciones->usuariodestino_id = $vendedor->persona->usuario->id;
                        $notificaciones->vendedor_id = $vendedor->id;
                        $notificaciones->status = 1;                    
                        $notificaciones->nombretabla = 'clientebloqueado';
                        $notificaciones->mensaje = 'Cliente Desbloq Rut: '.$clientebloqueado->cliente->rut;
                        $notificaciones->mensajetitle = $clientebloqueado->cliente->razonsocial;
                        $notificaciones->nombrepantalla = 'clientebloqueado.index';
                        $notificaciones->rutaorigen = 'clientebloqueado/crear';
                        $notificaciones->rutadestino = 'reportclientes';
                        $notificaciones->tabla_id = $clientebloqueado->id;
                        $notificaciones->accion = 'Cliente Desbloqueado.';
                        $notificaciones->icono = 'fa fa-fw fa-unlock text-green';
                        $notificaciones->save();

                        $nombrevendedor = $vendedor->persona->nombre . ' ' . $vendedor->persona->apellido;
                        Mail::to($vendedor->persona->usuario->email)->send(new MailClienteBloqueado($clientebloqueado,$asunto,$cuerpo,$nombrevendedor));
                    }
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
            $datas = ClienteBloqueado::where('cliente_id','=',$request->id);
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
