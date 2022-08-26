<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cliente-producto');
        return view('clienteproducto.index');
    }

    public function clienteproductopage(){
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
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
        $sucurArray = $user->sucursales->pluck('id')->toArray();

        return datatables()
            ->eloquent(Cliente::query()
            ->select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
            ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                    ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
            ->pluck('cliente_sucursal.cliente_id')->toArray())
            ->whereIn('cliente.id',$clientevendedorArray)
            )
            ->toJson();
    }

    public function productobuscarpage(Request $request){
        $datas = Producto::AsignarProductosAClientes($request);
        return datatables($datas)->toJson();
    }

    public function productobuscarpageid(Request $request){
        $datas = Producto::AsignarProductosAClientes($request);
        return datatables($datas)->toJson();
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        can('editar-cliente-producto');
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
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $clienteArray = Cliente::whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                    ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
                    ->pluck('cliente_sucursal.cliente_id')->toArray())
                    ->whereIn('cliente.id',$clientevendedorArray)
                    ->where('cliente.id','=',$id)
                    ->pluck('cliente.id')->toArray();
        //dd($clienteArray);
        if($clienteArray){
            $data = Cliente::findOrFail($id);
            $sql = 'SELECT vendedor.id,vendedor.persona_id,concat(nombre, " " ,apellido) AS nombre
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id';
            $vendedores = DB::select($sql);
            $sucursales = Sucursal::orderBy('id')->pluck('nombre', 'id')->toArray();
            $comunas = Comuna::orderBy('id')->get();
            /*
            foreach($data->productos as $producto){
                dd($producto->acuerdotecnico->id);
            }
            */
            return view('clienteproducto.editar', compact('data','sucursales','comunas','vendedores'));
        }else{
            return redirect('cliente')->with('mensaje','No tiene permiso para ver este cliente.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
}
