<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarClienteInterno;
use App\Models\ClienteInterno;
use App\Models\ClienteInternoSucursal;
use App\Models\ClienteInternoVendedor;
use App\Models\Comuna;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\PlazoPago;
use App\Models\Provincia;
use App\Models\Region;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteInternoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cliente-interno');
        return view('clienteinterno.index');
    }

    public function clienteinternopage(){
        /*6
        return datatables()
            ->eloquent(ClienteInterno::query())
            ->toJson();
        */
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
            $clientevendedorArray = ClienteInternoVendedor::where('vendedor_id',$vendedor_id)->pluck('clienteinterno_id')->toArray();
        }else{
            $clientevendedorArray = ClienteInternoVendedor::pluck('clienteinterno_id')->toArray();
        }
        $sucurArray = $user->sucursales->pluck('id')->toArray();

        return datatables()
            ->eloquent(ClienteInterno::query()
            ->select(['clienteinterno.id','clienteinterno.rut','clienteinterno.razonsocial','clienteinterno.direccion','clienteinterno.telefono'])
            ->whereIn('clienteinterno.id' , ClienteInternoSucursal::select(['clienteinterno_sucursal.clienteinterno_id'])
                                    ->whereIn('clienteinterno_sucursal.sucursal_id', $sucurArray)
            ->pluck('clienteinterno_sucursal.clienteinterno_id')->toArray())
            ->whereIn('clienteinterno.id',$clientevendedorArray)
            )
            ->toJson();
    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cliente-interno');
        $regiones = Region::orderBy('id')->get();
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $sucursales = Sucursal::orderBy('id')->pluck('nombre', 'id')->toArray();
        //$vendedores = Vendedor::orderBy('id')->get();
        $sql = 'SELECT vendedor.id,vendedor.persona_id,concat(nombre, " " ,apellido) AS nombre
                FROM vendedor INNER JOIN persona
                ON vendedor.persona_id=persona.id';

        $vendedores = DB::select($sql);
        $provincias = Provincia::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        return view('clienteinterno.crear',compact('regiones','sucursales','formapagos','plazopagos','vendedores','provincias','comunas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarClienteInterno $request)
    {
        can('guardar-cliente-interno');
        //DD($request);
        $cliente = ClienteInterno::create($request->all());
        $cliente->vendedores()->sync($request->vendedor_id);
        $cliente->sucursales()->sync($request->sucursalp_id);
        $clienteid = $cliente->id;
        return redirect('clienteinterno')->with('mensaje','Cliente Interno creado con exito.');
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
        can('editar-cliente-interno');
        $data = ClienteInterno::findOrFail($id);
        //dd($data);
        $sucursales = Sucursal::orderBy('id')->pluck('nombre', 'id')->toArray();
        $comunas = Comuna::orderBy('id')->get();
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $sql = 'SELECT vendedor.id,vendedor.persona_id,concat(nombre, " " ,apellido) AS nombre
                FROM vendedor INNER JOIN persona
                ON vendedor.persona_id=persona.id';

        $vendedores = DB::select($sql);
        //dd($clientedirec);
        return view('clienteinterno.editar', compact('data','sucursales','comunas','formapagos','plazopagos','vendedores'));
    
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
        if(can('guardar-cliente-interno',false) == true){
            $clienteinterno = ClienteInterno::findOrFail($id);
            $clienteinterno->update($request->all());
            $clienteinterno->vendedores()->sync($request->vendedor_id);
            $clienteinterno->sucursales()->sync($request->sucursalp_id);
            return redirect('clienteinterno')->with('mensaje','Cliente actualizado con exito!');
        }else{
            return redirect('clienteinterno')->with('mensaje','No tiene permiso para editar!');
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

    public function buscarCli(Request $request){
        if($request->ajax()){
            $clienteinterno = ClienteInterno::where('rut', $request->rut);
            //dd(response()->json($clienteinterno->get()));
            return response()->json($clienteinterno->get());
        }
    }
}
