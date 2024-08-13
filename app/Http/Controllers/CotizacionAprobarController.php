<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Cliente;
use App\Models\Color;
use App\Models\Comuna;
use App\Models\Cotizacion;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\GrupoCatProm;
use App\Models\MateriaPrima;
use App\Models\Moneda;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\Provincia;
use App\Models\Region;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\TipoSello;
use App\Models\UnidadMedida;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CotizacionAprobarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        can('listar-aprobar-cotizacion');
        //session(['aux_aprocot' => '0']) 0=Pantalla Normal CRUD de Cotizaciones
        //session(['aux_aprocot' => '1']) 1=Pantalla Solo para aprobar cotizacion para luego emitir la Nota de Venta
        session(['aux_aprocot' => '1']);
        /*
        $user = Usuario::findOrFail(auth()->id());
        //$vendedor_id=$user->persona->vendedor->id;

        //$aux_statusPant 0=Pantalla Normal CRUD de Cotizaciones
        //$aux_statusPant 1=Pantalla Solo para aprobar cotizacion para luego emitir la Nota de Venta
        $aux_statusPant = 1;

        $sql = 'SELECT cotizacion.id,cotizacion.fechahora,
                    if(isnull(cliente.razonsocial),clientetemp.razonsocial,cliente.razonsocial) as razonsocial,
                    aprobstatus, 
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and cotizaciondetalle.precioxkilo<cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion left join cliente
                on cotizacion.cliente_id = cliente.id
                left join clientetemp
                on cotizacion.clientetemp_id = clientetemp.id
                where aprobstatus=2
                and cotizacion.deleted_at is null;';
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);
        */
        //dd($datas);
        
        //$datas = Cotizacion::where('usuario_id',auth()->id())->get();
        //return view('cotizacionaprobar.index', compact('datas'));
        return view('cotizacionaprobar.index');
    }

    public function cotizacionaprobarpage(){
        session(['aux_aprocot' => '1']);
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);

        //$vendedor_id=$user->persona->vendedor->id;

        //$aux_statusPant 0=Pantalla Normal CRUD de Cotizaciones
        //$aux_statusPant 1=Pantalla Solo para aprobar cotizacion para luego emitir la Nota de Venta
        $aux_statusPant = 1;
        //$arraySucFisxUsu Ubicacion de Sucursal fisica de usuario
        //Valido con las ubicaciones fisicas de Usuario que creo el registro
        //Esto para solo mostrar los registros correspondientes a la Sucursal del Usuario que se loguio
        $arraySucFisxUsu = implode(",", sucFisXUsu($user->persona));
        $sql = "SELECT cotizacion.id,DATE_FORMAT(cotizacion.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                    if(isnull(cliente.razonsocial),clientetemp.razonsocial,cliente.razonsocial) as razonsocial,
                    aprobstatus,'1' as pdfcot,
                    concat(persona.nombre, ' ' ,persona.apellido) as vendedor_nombre,
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and cotizaciondetalle.precioxkilo<cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion left join cliente
                on cotizacion.cliente_id = cliente.id
                left join clientetemp
                on cotizacion.clientetemp_id = clientetemp.id
                INNER JOIN vendedor
                ON cotizacion.vendedor_id = vendedor.id
                INNER JOIN persona
                ON vendedor.persona_id = persona.id
                INNER JOIN vista_sucfisxusu
                ON cotizacion.usuario_id = vista_sucfisxusu.usuario_id and vista_sucfisxusu.sucursal_id IN ($arraySucFisxUsu)
                where aprobstatus=2
                and cotizacion.deleted_at is null
                AND cotizacion.sucursal_id in ($sucurcadena)
                GROUP BY cotizacion.id;";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);
        return datatables($datas)->toJson();  
    }

    public function productobuscarpage(Request $request){
        $datas = Producto::productosxCliente($request);
        return datatables($datas)->toJson();
    }

    public function clientebuscarpage(){
        //$datas = Cliente::clientesxUsuarioSQLTemp();
        $datas = Cliente::clientesxUsuarioSQL();
        return datatables($datas)->toJson();
    }

    public function productobuscarpageid(Request $request){
        //$datas = Producto::productosxClienteTemp($request);
        $datas = Producto::productosxCliente($request);
        return datatables($datas)->toJson();
    }

    public function clientebuscarpageid($id){
        //$datas = Cliente::clientesxUsuarioSQLTemp();
        $datas = Cliente::clientesxUsuarioSQL();
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
        session(['editaracutec' => '0']);
        session([
            'aux_aprocot' => '1',
            'aux_paginaredirect' => 'cotizacionaprobar'
        ]);
        return editar($id);
        /*
        can('editar-cotizacion');
        $data = Cotizacion::findOrFail($id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $cotizacionDetalles = $data->cotizaciondetalles()->get();

        $vendedor_id=$data->vendedor_id;
        if(empty($data->cliente_id)){
            $clienteselec = $data->clientetemp()->get();
        }else{
            $clienteselec = $data->cliente()->get();
        }
        //VENDEDORES POR SUCURSAL
        $tablas = array();
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        
        $fecha = date("d/m/Y", strtotime($data->fechahora));
        $user = Usuario::findOrFail(auth()->id());
        if(isset($user->persona->vendedor->id)){
            $tablas['vendedor_id'] = $user->persona->vendedor->id;
        }else{
            $tablas['vendedor_id'] = "0";
        }
        $tablas['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];


        $tablas['formapagos'] = FormaPago::orderBy('id')->get();
        $tablas['plazopagos'] = PlazoPago::orderBy('id')->get();
        $tablas['comunas'] = Comuna::orderBy('id')->get();
        $tablas['provincias'] = Provincia::orderBy('id')->get();
        $tablas['regiones'] = Region::orderBy('id')->get();
        $tablas['tipoentregas'] = TipoEntrega::orderBy('id')->get();
        $tablas['giros'] = Giro::orderBy('id')->get();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablas['sucurArray'])->get();

        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['materiPrima'] = MateriaPrima::orderBy('id')->get();
        $tablas['color'] = Color::orderBy('id')->get();
        $tablas['certificado'] = Certificado::orderBy('id')->get();

        $aux_sta=2;

        return view('cotizacion.editar', compact('data','clienteselec','cotizacionDetalles','fecha','aux_sta','aux_cont','tablas'));
        //Santa Ester
        return view('cotizacion.editar', compact('data','clienteselec','clientes','cotizacionDetalles','productos','fecha','aux_sta','aux_cont','tablas'));
        //Fin Santa Ester
        */
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

function editar($id){
    can('editar-cotizacion');
        //dd(session('aux_paginaredirect'));
        $data = Cotizacion::findOrFail($id);
        /* if($data->cliente_id){
            $request1 = new Request();            
            $request1->merge(['modulo_id' => 26]);
            $request1->request->set('modulo_id', 26);
            $request1->merge(['cotizacion_id' => $data->id]);
            $request1->request->set('cotizacion_id', $data->id);
            $request1->merge(['deldesbloqueo' => 0]);
            $request1->request->set('deldesbloqueo', 0);
            $bloqcli = clienteBloqueado($data->cliente_id,0,$request1);
            if($bloqcli["bloqueo"]){
                return redirect('cotizacionaprobar')->with([
                    'mensaje'=> "Cliente bloqueado: \n" . $bloqcli["bloqueo"],
                    'tipo_alert' => 'alert-error'
                ]);
            }    
        } */
        if($data->aprobstatus != 2){
            return redirect('cotizacionaprobar')->with([
                'mensaje'=>'Cotizacion fue modificada por otro usuario.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $cotizacionDetalles = $data->cotizaciondetalles()->get();


        $vendedor_id=$data->vendedor_id;
        if(empty($data->cliente_id)){
            $clienteselec = $data->clientetemp()->get();
        }else{
            $clienteselec = $data->cliente()->get();
        }
        //VENDEDORES POR SUCURSAL
        $tablas = array();
        $vendedor_id = Vendedor::vendedor_id();
        $tablas['vendedor_id'] = $vendedor_id["vendedor_id"];
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        
        $fecha = date("d/m/Y", strtotime($data->fechahora));
        $user = Usuario::findOrFail(auth()->id());
        //$clientesArray = Cliente::clientesxUsuario('0',$data->cliente_id); //Paso vendedor en 0 y el id del cliente para que me traiga las Sucursales que coinciden entre el vendedor y el cliente
        //$clientes = $clientesArray['clientes'];
        //$tablas['vendedor_id'] = $clientesArray['vendedor_id'];

        $tablas['formapagos'] = FormaPago::orderBy('id')->get();
        $tablas['plazopagos'] = PlazoPago::orderBy('id')->get();
        $tablas['comunas'] = Comuna::orderBy('id')->get();
        $tablas['provincias'] = Provincia::orderBy('id')->get();
        $tablas['regiones'] = Region::orderBy('id')->get();
        $tablas['tipoentregas'] = TipoEntrega::orderBy('id')->get();
        $tablas['giros'] = Giro::orderBy('id')->get();
        $tablas['sucurArray'] = $user->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablas['sucurArray'])->get();
        //$tablas['sucursales'] = $clientesArray['sucursales'];
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['unidadmedidaAT'] = UnidadMedida::orderBy('id')->get();
        $tablas['materiPrima'] = MateriaPrima::orderBy('id')->get();
        $tablas['color'] = Color::orderBy('id')->get();
        $tablas['certificado'] = Certificado::orderBy('id')->get();
        $tablas['tipoSello'] = TipoSello::orderBy('id')->get();
        $tablas['moneda'] = Moneda::orderBy('id')->get();
        $tablas['grupocatproms'] = GrupoCatProm::arraygrupocatprom();

        $tablas['modulo_id'] = 26;
        $aux_sta=2;

        return view('cotizacionaprobar.editar', compact('data','clienteselec','cotizacionDetalles','fecha','aux_sta','aux_cont','tablas'));
}