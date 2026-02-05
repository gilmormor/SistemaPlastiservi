<?php

namespace App\Http\Controllers;

use App\Events\AvisoRevisionAcuTec;
use App\Http\Requests\ValidarCotizacion;
use App\Models\AcuerdoTecnicoTemp;
use App\Models\Certificado;
use App\Models\Cliente;
use App\Models\Color;
use App\Models\Comuna;
use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
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

class CotizacionAtFirmadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cargar-at-firmado-cotizacion');
        session(['aux_aprocot' => '6']);
        return view('cotizacionatfirmado.index');
    }

    public function cotizacionatfirmadopage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);

        //session(['aux_aprocot' => '5']);
        session([
            'aux_aprocot' => '6',
            'paginaredirect' => 'cotizacionatfirmado'
        ]);

        $sql = "SELECT cotizacion.id,DATE_FORMAT(cotizacion.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                    if(isnull(cliente.razonsocial),clientetemp.razonsocial,cliente.razonsocial) as razonsocial,
                    concat(persona.nombre, ' ' ,persona.apellido) as vendedor_nombre,
                    aprobstatus,'1' as pdfcot,cotizacion.updated_at,
                    SUM(CASE WHEN acuerdotecnicotemp.at_firmado IS NULL THEN 0 ELSE 1 END) AS contconfirm,
                    SUM(CASE WHEN acuerdotecnicotemp.at_firmado IS NULL THEN 1 ELSE 0 END) AS contsinfirm
                FROM cotizacion left join cliente
                on cotizacion.cliente_id = cliente.id
                left join clientetemp
                on cotizacion.clientetemp_id = clientetemp.id
                INNER JOIN vendedor
                ON cotizacion.vendedor_id = vendedor.id
                INNER JOIN persona
                ON vendedor.persona_id = persona.id
                INNER JOIN cotizaciondetalle
                ON cotizacion.id = cotizaciondetalle.cotizacion_id
                INNER JOIN producto
                ON cotizaciondetalle.producto_id = producto.id
                LEFT JOIN acuerdotecnicotemp
                ON cotizaciondetalle.id = acuerdotecnicotemp.at_cotizaciondetalle_id
                where (aprobstatus=6 or aprobstatus=8)
                and cotizacion.deleted_at is null
                AND cotizacion.sucursal_id in ($sucurcadena)
                AND producto.tipoprod = 1
                GROUP BY cotizacion.id;";
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
    public function actualizar(ValidarCotizacion $request, $id)
    {
        can('guardar-cotizacion');
        //dd($request);
        DB::beginTransaction();
        try {
            $cotizacion = Cotizacion::findOrFail($id);
            if($cotizacion->updated_at != $request->updated_at){
                return redirect('cotizacion')->with([
                    'mensaje' => 'No se pudo modificar. Registro Editado por otro usuario. Fecha Hora: '.$cotizacion->updated_at,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            $cont_cotdet = count($request->cotdet_id);
            if($cont_cotdet <=0 ){
                return redirect('notaventa')->with([
                    'mensaje'=>'Cotización sin items, no se actualizó.',
                    'tipo_alert' => 'alert-error'
                ]);
            }
            //$cotizacion->update($request->all());
            if($cont_cotdet>0){
                for ($i=0; $i < count($request->cotdet_id) ; $i++){
                    $at_imagen = "at_imagen" . ($i+1);
                    $imagen = "imagen" . ($i+1);
                    $producto = Producto::findOrFail($request->producto_id[$i]);
                    $cotizaciondetalle = CotizacionDetalle::findOrFail($request->cotdet_id[$i]);
                    if(($producto->tipoprod == 1) and ($request->acuerdotecnico[$i] != "null")){
                        if($cotizaciondetalle->acuerdotecnicotemp_id != null){
                            /* AcuerdoTecnicoTemp::where("id","=",$cotizaciondetalle->acuerdotecnicotemp_id)
                            ->update($arrayAT); */
                            $data = AcuerdoTecnicoTemp::findOrFail($cotizaciondetalle->acuerdotecnicotemp_id);
                            //dd($data->at_impresofoto);
                            if ($foto = AcuerdoTecnicoTemp::setAtFirmado($request->$at_imagen,$cotizaciondetalle->acuerdotecnicotemp_id,$request,$at_imagen,$request->$imagen,$data->at_impresofoto)){
                                if($foto=="del"){
                                    $foto = null;
                                }
                                $data->at_firmado = $foto;
                                $data->save();
                            }
                        }
                    }
                }
            }
            $cotizacion->updated_at = now();
            $cotizacion->save();
            DB::commit();
            return redirect('cotizacionatfirmado')->with('mensaje','Cotización actualizada con exito!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('mensaje', 'Error: ' . $e->getMessage())->withInput();
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

    public function enviarrev(Request $request)
    {
        //dd($request);
        can('guardar-cargar-at-firmado-cotizacion');
        if ($request->ajax()) {
            $cotizacion = Cotizacion::findOrFail($request->id);
            if($request->updated_at != $cotizacion->updated_at){
                return response()->json([
                    'error' => 1,
                    'mensaje' => "No se actualizaron los datos, registro fue modificado por otro usuario!",
                    'tipo_alert' => "error"
                ]);
            }
            $aux_firmado = true;
            foreach ($cotizacion->cotizaciondetalles as $cotizaciondetalle) {
                if(isset($cotizaciondetalle->acuerdotecnicotempunoauno)){
                    if($cotizaciondetalle->acuerdotecnicotempunoauno->at_firmado == null){
                        $aux_firmado = false;
                        break;
                    }
                }
            }
            if($aux_firmado == false){
                return response()->json([
                    'error' => 1,
                    'mensaje' => "No se puede enviar a revisión, Acuerdo Técnico no está firmado.",
                    'tipo_alert' => "error"
                ]);
            }
            DB::beginTransaction();
            try {
                $cotizacion->updated_at = now();
                $cotizacion->aprobstatus = 5; //Se envia cotizacion a pantalla revision At
                $cotizacion->aprobusu_id = auth()->id();
                $cotizacion->aprobfechahora = date("Y-m-d H:i:s");
                $cotizacion->aprobobs = 'Se envio cotizacion y Acuerdo Tecnico (AcuTec) para Revisado';
                $cotizacion->save();
                Event(new AvisoRevisionAcuTec($cotizacion));
                DB::commit();
                return response()->json([
                    'error' => 0,
                    'aprobstatus' => $cotizacion->aprobstatus,
                    'mensaje' => "El registro fue procesado con exito.",
                    'tipo_alert' => "success"
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 1,
                    'mensaje' => 'Error: ' . $e->getMessage(),
                    'tipo_alert' => "success"
                ]);
            }
        } else {
            abort(404);
        }

    }

    public function devolver(Request $request)
    {
        //dd($request);
        can('guardar-cargar-at-firmado-cotizacion');
        if ($request->ajax()) {
            $cotizacion = Cotizacion::findOrFail($request->id);
            if($request->updated_at != $cotizacion->updated_at){
                return response()->json([
                    'error' => 1,
                    'mensaje' => "No se actualizaron los datos, registro fue modificado por otro usuario!",
                    'tipo_alert' => "error"
                ]);
            }
            DB::beginTransaction();
            try {
                $cotizacion->updated_at = now();
                $cotizacion->aprobstatus = null; //Se devuelve cotizacion a crear cotizacion
                $cotizacion->aprobusu_id = null;
                $cotizacion->aprobfechahora = null;
                $cotizacion->aprobobs = 'Devuelto de pantalla subir Acuerdo Tec por Vendedor Usuario Id: ' . auth()->id() . " Fecha: " . date("Y-m-d H:i:s");
                $cotizacion->save();
                /* foreach ($cotizacion->cotizaciondetalles as $cotizaciondetalle) {
                    if(isset($cotizaciondetalle->acuerdotecnicotempunoauno)){
                        $cotizaciondetalle->acuerdotecnicotempunoauno->at_firmado = null;
                        $cotizaciondetalle->acuerdotecnicotempunoauno->save();
                    }
                } */
                DB::commit();
                return response()->json([
                    'error' => 0,
                    'aprobstatus' => $cotizacion->aprobstatus,
                    'mensaje' => "El registro fue procesado con exito.",
                    'tipo_alert' => "success"
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 1,
                    'mensaje' => 'Error: ' . $e->getMessage(),
                    'tipo_alert' => "success"
                ]);
            }
        } else {
            abort(404);
        }

    }
}

function editar($id){
    can('editar-cargar-at-firmado-cotizacion');
        /* session([
            'aux_aprocot' => '5',
            'aux_paginaredirect' => 'cotizacionatfirmado'
        ]); */
        session(['editaracutec' => '1']);
        session([
            'aux_aprocot' => '0',
            'aux_paginaredirect' => 'cotizacion'
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
                return redirect('cotizacionatfirmado')->with([
                    'mensaje'=>'Cotizacion fue modificada por otro usuario.',
                    'tipo_alert' => 'alert-error'
                ]);
            }
        }
        //dd("ID:" . $id . "\n updated_at: " . $updated_at);
        if($data->aprobstatus != 6 and $data->aprobstatus != 8){
            return redirect('cotizacionatfirmado')->with([
                'mensaje'=>'Cotizacion cambio de estatus.',
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
                return redirect('cotizacionatfirmado')->with([
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
        $tablas['usuario'] = Usuario::findOrFail(auth()->id());
        $tablas['modulo_id'] = 25;

        $aux_sta=2;

        return view('cotizacionatfirmado.editar', compact('data','clienteselec','cotizacionDetalles','fecha','aux_sta','tablas'));
}