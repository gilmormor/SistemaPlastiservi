<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Cotizacion;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\Provincia;
use App\Models\Region;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CotizacionAprobarClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-aprobar-cliente');
        //session(['aux_aprocot' => '0']) 0=Pantalla Normal CRUD de Cotizaciones
        //session(['aux_aprocot' => '1']) 1=Pantalla Solo para aprobar cotizacion para luego emitir la Nota de Venta
        session(['aux_aprocot' => '1']);
        $user = Usuario::findOrFail(auth()->id());
        //$vendedor_id=$user->persona->vendedor->id;

        //$aux_statusPant 0=Pantalla Normal CRUD de Cotizaciones
        //$aux_statusPant 1=Pantalla Solo para aprobar cotizacion para luego emitir la Nota de Venta
        $aux_statusPant = 1;

        $sql = 'SELECT cotizacion.id,cotizacion.fechahora,clientetemp.razonsocial,aprobstatus, 
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and cotizaciondetalle.precioxkilo<cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion inner join clientetemp
                on cotizacion.clientetemp_id = clientetemp.id
                where isnull(cliente_id)
                and (aprobstatus=1 or aprobstatus=2 or aprobstatus=3)
                and cotizacion.deleted_at is null;';
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);
        //dd($datas);
        
        //$datas = Cotizacion::where('usuario_id',auth()->id())->get();
        return view('cotizacionAprobarCliente.index', compact('datas'));
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
        can('editar-aprobar-cliente');
        //dd('entro');
        $data = Cotizacion::findOrFail($id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));;
        $cotizacionDetalles = $data->cotizaciondetalles()->get();
        $vendedor_id=$data->vendedor_id;
        if(empty($data->cliente_id)){
            $clienteselec = $data->clientetemp()->get();
        }else{
            $clienteselec = $data->cliente()->get();
        }
        //dd($clienteselec);

        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        //Aqui si estoy filtrando solo las categorias de asignadas al usuario logueado
        //******************* */
        $clientedirecs = Cliente::where('rut', $clienteselec[0]->rut)
                    ->join('clientedirec', 'cliente.id', '=', 'clientedirec.cliente_id')
                    ->select([
                                'cliente.id' => 'cliente_id',
                                'cliente.razonsocial',
                                'cliente.telefono',
                                'cliente.email',
                                'cliente.direccion',
                                'cliente.regionp_id',
                                'cliente.provinciap_id',
                                'cliente.comunap_id',
                                'clientedirec.id',
                                'clientedirec.direcciondetalle',
                            ])->get();
        //dd($clientedirecs);

        $clienteDirec = $data->clientedirec()->get();
        //dd($clienteDirec);
        $fecha = date("d/m/Y", strtotime($data->fechahora));
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $provincias = Provincia::orderBy('id')->get();
        $regiones = Region::orderBy('id')->get();
        $users = Usuario::findOrFail(auth()->id());

        $productos = Producto::productosxUsuario();
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $giros = Giro::orderBy('id')->get();
        $sucursales = Sucursal::orderBy('id')->get();

        $aux_sta=2;
        $aux_statusPant = 0;
        //dd($clientedirecs);
        return view('cotizacionAprobarCliente.editar', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','cotizacionDetalles','comunas','provincias','regiones','formapagos','plazopagos','vendedores','productos','fecha','empresa','tipoentregas','giros','sucurArray','sucursales','aux_sta','aux_cont','aux_statusPant'));
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
