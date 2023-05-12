<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarClienteTemp;
use App\Models\ClienteTemp;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\Vendedor;
use Illuminate\Http\Request;

class ClienteTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cliente-temporal');
        $datas = ClienteTemp::orderBy('id')->get();
        return view('clientetemp.index', compact('datas'));
    }

    public function clientetemppage(){
        /*
        return datatables()
            ->eloquent(ClienteTemp::query())
            ->toJson();
        */
        $datas = ClienteTemp::consulta();

        //dd($datas);
        return datatables($datas)->toJson();
    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cliente-temporal');
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $tablas = array();
        //$tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        return view('clientetemp.crear', compact('tablas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarClienteTemp $request)
    {
        can('guardar-cliente-temporal');
        ClienteTemp::create($request->all());
        return redirect('clientetemp')->with('mensaje','ClienteTemp creado con exito');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-cliente-temporal');
        $data = ClienteTemp::findOrFail($id);
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $tablas = array();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        //$tablas['vendedores'] = Vendedor::orderBy('id')->get();
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];

        return view('clientetemp.editar', compact('data','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarClienteTemp $request, $id)
    {
        can('guardar-cliente-temporal');
        ClienteTemp::findOrFail($id)->update($request->all());
        return redirect('clientetemp')->with('mensaje','ClienteTemp actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('eliminar-cliente-temporal');
        if ($request->ajax()) {
            if (ClienteTemp::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
    
    public function buscarCliTemp(Request $request){
        if($request->ajax()){
            //dd($request);
            $clientetemp = ClienteTemp::join('cotizacion', 'clientetemp.id', '=', 'cotizacion.clientetemp_id')
                            ->join('vendedor', 'clientetemp.vendedor_id', '=', 'vendedor.id')
                            ->join('persona', 'vendedor.persona_id', '=', 'persona.id')
                            ->where('clientetemp.rut', $request->rut)
                            ->whereNull("clientetemp.deleted_at")
                            ->whereNull("cotizacion.deleted_at")
                            ->select([
                                'clientetemp.id',
                                'cotizacion.id as cotizacion_id',
                                'clientetemp.rut',
                                'clientetemp.razonsocial',
                                'clientetemp.direccion',
                                'clientetemp.telefono',
                                'clientetemp.email',
                                'clientetemp.vendedor_id',
                                'clientetemp.giro_id',
                                'clientetemp.giro',
                                'clientetemp.comunap_id',
                                'clientetemp.formapago_id',
                                'clientetemp.plazopago_id',
                                'clientetemp.contactonombre',
                                'clientetemp.contactoemail',
                                'clientetemp.contactotelef',
                                'clientetemp.finanzascontacto',
                                'clientetemp.finanzanemail',
                                'clientetemp.finanzastelefono',
                                'clientetemp.sucursal_id',
                                'clientetemp.observaciones',
                                'clientetemp.usuariodel_id',
                                'persona.nombre as vendedor_nombre',
                                'persona.apellido as vendedor_apellido'
                            ]);
            return response()->json($clientetemp->get());
        }
    }
}
