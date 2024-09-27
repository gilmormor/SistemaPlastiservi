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

class CotizacionAprobarAcuTecController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-aprobar-acuerdo-tecnico-cotizacion');
        session(['aux_aprocot' => '5']);
        return view('cotizacionaprobaracutec.index');
    }

    public function cotizacionaprobaracutecpage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);

        //session(['aux_aprocot' => '5']);
        session([
            'aux_aprocot' => '5',
            'paginaredirect' => 'cotizacionaprobaracutec'
        ]);

        $sql = "SELECT cotizacion.id,DATE_FORMAT(cotizacion.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                    if(isnull(cliente.razonsocial),clientetemp.razonsocial,cliente.razonsocial) as razonsocial,
                    concat(persona.nombre, ' ' ,persona.apellido) as vendedor_nombre,
                    aprobstatus,'1' as pdfcot,cotizacion.updated_at
                FROM cotizacion left join cliente
                on cotizacion.cliente_id = cliente.id
                left join clientetemp
                on cotizacion.clientetemp_id = clientetemp.id
                INNER JOIN vendedor
                ON cotizacion.vendedor_id = vendedor.id
                INNER JOIN persona
                ON vendedor.persona_id = persona.id
                where aprobstatus=5
                and cotizacion.deleted_at is null
                AND cotizacion.sucursal_id in ($sucurcadena);";
        //where usuario_id='.auth()->id();
        $datas = DB::select($sql);
        return datatables($datas)->toJson();  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function productobuscarpageid(Request $request){
        $datas = Producto::productosxCliente($request);
        return datatables($datas)->toJson();
    }

    public function clientebuscarpageid($id){
        $datas = Cliente::clientesxUsuarioSQL();
        return datatables($datas)->toJson();
    }

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
        session(['editaracutec' => '1']);
        session(['modulo_id' => '2']);
        return editar($id);
        /*
        $objeto = new CotizacionController();
        return $objeto->editaraat($id);
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
        session([
            'aux_aprocot' => '5',
            'aux_paginaredirect' => 'cotizacionaprobaracutec'
        ]);

        //dd(session('aux_paginaredirect'));
        $data = Cotizacion::findOrFail($id);
        // Verificar si el carácter '&' está presente en la cadena
        $cadena = $id;
        if (strpos($cadena, '&') !== false) {
            // Si '&' está presente, extraer los valores antes y después de '&'
            $posicion = strpos($cadena, '&');
            $id = substr($cadena, 0, $posicion);
            $updated_at = substr($cadena, $posicion + 1);
            if($updated_at != $data->updated_at){
                return redirect('cotizacionaprobaracutec')->with([
                    'mensaje'=>'Cotizacion fue modificada por otro usuario.',
                    'tipo_alert' => 'alert-error'
                ]);
            }
        }
        //dd("ID:" . $id . "\n updated_at: " . $updated_at);
        if($data->aprobstatus != 5){
            return redirect('cotizacionaprobaracutec')->with([
                'mensaje'=>'Cotizacion fue modificada por otro usuario.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        /* if($data->cliente_id){
            $request1 = new Request();
            $request1->merge(['cotizacion_id' => $data->id]);
            $request1->request->set('cotizacion_id', $data->id);
            $request1->merge(['modulo_id' => 25]);
            $request1->request->set('modulo_id', 25);
            $request1->merge(['deldesbloqueo' => 0]);
            $request1->request->set('deldesbloqueo', 0);
            $bloqcli = clienteBloqueado($data->cliente_id,0,$request1);
            if($bloqcli["bloqueo"]){
                return redirect('cotizacionaprobaracutec')->with([
                    'mensaje'=> "Condición financiera en revisión: \n" . $bloqcli["bloqueo"],
                    'tipo_alert' => 'alert-error'
                ]);
            }
        } */
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
        $tablas['modulo_id'] = 25;

        $aux_sta=2;

        return view('cotizacionaprobaracutec.editar', compact('data','clienteselec','cotizacionDetalles','fecha','aux_sta','aux_cont','tablas'));
}