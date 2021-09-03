<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\NotaVenta;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\UnidadMedida;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaventaAprobarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-aprobar-notaventa');
        //session(['aux_aproNV' => '0']) 0=Pantalla Normal CRUD de Nota de Venta
        //session(['aux_aproNV' => '1']) 1=Pantalla Solo para aprobar Nota de Venta para luego emitir Guia de Despacho
        /*
        session(['aux_aproNV' => '1']);
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
        FROM vendedor INNER JOIN persona
        ON vendedor.persona_id=persona.id and vendedor.deleted_at is null
        INNER JOIN usuario 
        ON persona.usuario_id=usuario.id and persona.deleted_at is null
        WHERE usuario.id=' . auth()->id() . ';';
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $aux_condvend = 'notaventa.vendedor_id = ' . $vendedor_id;
            $aux_condvendcot = 'cotizacion.vendedor_id = ' . $vendedor_id;
        }else{
            $aux_condvend = 'true';
            $aux_condvendcot = 'true';
        }

        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = "SELECT notaventa.id,notaventa.fechahora,notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs, 
                    (SELECT COUNT(*) 
                    FROM notaventadetalle 
                    WHERE notaventadetalle.notaventa_id=notaventa.id and 
                    notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
                FROM notaventa inner join cliente
                on notaventa.cliente_id = cliente.id
                where $aux_condvend
                and anulada is null
                and aprobstatus=2
                and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
                and notaventa.deleted_at is null;";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);

        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = "SELECT cotizacion.id,cotizacion.fechahora,razonsocial,aprobstatus,aprobobs,total, 
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and 
                    cotizaciondetalle.precioxkilo < cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion inner join cliente
                on cotizacion.cliente_id = cliente.id
                where $aux_condvendcot and (aprobstatus=1 or aprobstatus=3) and 
                cotizacion.id not in (SELECT cotizacion_id from notaventa WHERE !(cotizacion_id is NULL) and (anulada is null))
                and cotizacion.deleted_at is null;";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $cotizaciones = DB::select($sql);
        $aux_statusPant = 0; //Estatus para validar loq ue se muestra en la pantalla
        */
        
        //dd($cotizaciones);
        //$datas = Cotizacion::where('usuario_id',auth()->id())->get();
        //return view('notaventaAprobar.index', compact('datas','cotizaciones','aux_statusPant'));
        return view('notaventaAprobar.index');
    }

    function notaventaaprobarpage(){
        session(['aux_aproNV' => '1']);
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
        FROM vendedor INNER JOIN persona
        ON vendedor.persona_id=persona.id and vendedor.deleted_at is null
        INNER JOIN usuario 
        ON persona.usuario_id=usuario.id and persona.deleted_at is null
        WHERE usuario.id=' . auth()->id() . ';';
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $aux_condvend = 'notaventa.vendedor_id = ' . $vendedor_id;
        }else{
            $aux_condvend = 'true';
        }

        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = "SELECT notaventa.id,DATE_FORMAT(notaventa.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                    notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs,oc_file,oc_id,'' as pdfnv, 
                    (SELECT COUNT(*) 
                    FROM notaventadetalle 
                    WHERE notaventadetalle.notaventa_id=notaventa.id and 
                    notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
                FROM notaventa inner join cliente
                on notaventa.cliente_id = cliente.id
                where $aux_condvend
                and anulada is null
                and aprobstatus=2
                and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
                and notaventa.deleted_at is null;";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);
        
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
        can('editar-aprobar-notaventa');
        $data = NotaVenta::findOrFail($id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));;
        $detalles = $data->notaventadetalles()->get();
        $vendedor_id=$data->vendedor_id;
        $clienteselec = $data->cliente()->get();
        session(['aux_aprocot' => '2']);
        //dd($clienteselec[0]->rut);

        //$clientesArray = Cliente::clientesxUsuario();
        $clientesArray = Cliente::clientesxUsuario('0',$data->cliente_id); //Paso vendedor en 0 y el id del cliente para que me traiga las Sucursales que coinciden entre el vendedor y el cliente

        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        //dd($sucurArray);
        //Aqui si estoy filtrando solo las categorias de asignadas al usuario logueado
        //******************* */
        $clientedirecs = Cliente::where('rut', $clienteselec[0]->rut)
        ->join('clientedirec', 'cliente.id', '=', 'clientedirec.cliente_id')
        ->join('cliente_sucursal', 'cliente.id', '=', 'cliente_sucursal.cliente_id')
        ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->select([
                    'cliente.id as cliente_id',
                    'cliente.razonsocial',
                    'cliente.telefono',
                    'cliente.email',
                    'cliente.regionp_id',
                    'cliente.provinciap_id',
                    'cliente.comunap_id',
                    'cliente.contactonombre',
                    'cliente.direccion',
                    'clientedirec.id',
                    'clientedirec.direcciondetalle'
                ])->get();
        //dd($clientedirecs);
        $clienteDirec = $data->clientedirec()->get();
        $fecha = date("d/m/Y", strtotime($data->fechahora));
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();

        $productos = Producto::productosxUsuario();

        $vendedores1 = Usuario::join('sucursal_usuario', function ($join) {
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
            ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
                    })
            ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
            ->join('vendedor', function ($join) {
                $join->on('persona.id', '=', 'vendedor.persona_id')
                    ->where('vendedor.sta_activo', '=', 1);
            })
            ->select([
                'vendedor.id',
                'persona.nombre',
                'persona.apellido'
            ])
            ->get();

        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $giros = Giro::orderBy('id')->get();
        $aux_sta=3;
        $aux_statusPant = 0;
        $tablas = array();
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['sucursales'] = $clientesArray['sucursales'];

        //dd($clientedirecs);
        return view('notaventaAprobar.editar', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant','tablas'));
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
