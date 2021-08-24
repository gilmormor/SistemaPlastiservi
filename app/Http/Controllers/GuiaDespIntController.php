<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarGuiaDespInt;
use App\Models\Cliente;
use App\Models\ClienteInterno;
use App\Models\Comuna;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\GuiaDespInt;
use App\Models\GuiaDespIntDetalle;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\Provincia;
use App\Models\Region;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\UnidadMedida;
use App\Models\Vendedor;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class GuiaDespIntController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-guia-interna');
        return view('guiadespint.index');

    }

    public function guiadespintpage(){
        session(['aux_aprocot' => '0']);
        /*
        return datatables()
        ->eloquent(GuiaDespInt::query())
        ->toJson();
        */

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
            $aux_condvend = 'guiadespint.vendedor_id = ' . $vendedor_id;
        }else{
            $aux_condvend = 'true';
        }
        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = "SELECT guiadespint.id,DATE_FORMAT(guiadespint.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                    guiadespint.cli_rut,guiadespint.cli_nom,
                    aprobstatus,aprobobs,'' as pdf,
                    (SELECT COUNT(*) 
                    FROM guiadespintdetalle 
                    WHERE guiadespintdetalle.guiadespint_id=guiadespint.id and 
                    guiadespintdetalle.precioxkilo < guiadespintdetalle.precioxkiloreal) AS contador
                FROM guiadespint
                where $aux_condvend and (isnull(aprobstatus) or aprobstatus=0 or aprobstatus=4) 
                and isnull(guiadespint.deleted_at)
                ORDER BY guiadespint.id desc;";

        $datas = DB::select($sql);
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
        can('crear-guia-interna');
        //CLIENTES POR USUARIO. SOLO MUESTRA LOS CLIENTES QUE PUEDE VER UN USUARIO
        $tablas = array();
        //dd(ClienteInterno::clientesxUsuario());
        $clientesArray = ClienteInterno::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $tablas['vendedor_id'] = $clientesArray['vendedor_id'];
        $tablas['sucurArray'] = $clientesArray['sucurArray'];
        $fecha = date("d/m/Y");
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
        $aux_sta=1;
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        $productos = Producto::productosxUsuario();
        //dd($tablas['unidadmedida']);

        return view('guiadespint.crear',compact('clientes','fecha','productos','aux_sta','tablas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        can('guardar-guia-interna');
        $aux_rut=str_replace('.','',$request->rut);
        $request->rut = str_replace('-','',$aux_rut);
        //dd($request);
        if(!empty($request->razonsocialCTM)){
            $array_clientetemp = [
                'rut' => $request->rut,
                'razonsocial' => $request->razonsocial,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'vendedor_id' => $request->vendedor_id,
                'comunap_id' => $request->comunap_idCTM,
                'formapago_id' => $request->formapago_id,
                'plazopago_id' => $request->plazopago_id,
                'sucursal_id' => $request->sucursal_idCTM,
                'observaciones' => $request->observacionesCTM
            ];
            /*
            if(ClienteTemp::where('rut', $request->rut)->count()>0){
                $clientetemp = ClienteTemp::where('rut', $request->rut)->get();
                ClienteTemp::where('rut', $request->rut)->update($array_clientetemp);
                $request->request->add(['clientetemp_id' => $clientetemp[0]->id]);
            }else{
                $clientetemp = ClienteTemp::create($array_clientetemp);
                $request->request->add(['clientetemp_id' => $clientetemp->id]);
            }
            */
        }
        $hoy = date("Y-m-d H:i:s");
        $request->request->add(['fechahora' => $hoy]);
        /*
        $request->request->add(['cli_rut' => $request->rut]);
        $request->request->add(['cli_nom' => $request->razonsocial]);
        $request->request->add(['cli_dir' => $request->direccion]);
        $request->request->add(['cli_tel' => $request->telefono]);
        $request->request->add(['cli_email' => $request->email]);
        */
        
        $dateInput = explode('/',$request->plazoentrega);
        $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        //dd($request);
        $guiadespint = GuiaDespInt::create($request->all());
        $guiadespintid = $guiadespint->id;

        $cont_producto = count($request->producto_id);
        if($cont_producto>0){
            for ($i=0; $i < $cont_producto ; $i++){
                if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                    $producto = Producto::findOrFail($request->producto_id[$i]);
                    $guiadespintdetalle = new GuiaDespIntDetalle();
                    $guiadespintdetalle->guiadespint_id = $guiadespintid;
                    $guiadespintdetalle->producto_id = $request->producto_id[$i];
                    $guiadespintdetalle->cant = $request->cant[$i];
                    $guiadespintdetalle->unidadmedida_id = $request->unidadmedida_id[$i];
                    $guiadespintdetalle->preciounit = $request->preciounit[$i];
                    $guiadespintdetalle->peso = $producto->peso;
                    $guiadespintdetalle->precioxkilo = $request->precioxkilo[$i];
                    $guiadespintdetalle->precioxkiloreal = $request->precioxkiloreal[$i];
                    $guiadespintdetalle->totalkilos = $request->totalkilos[$i];
                    $guiadespintdetalle->subtotal = $request->subtotal[$i];
                    $guiadespintdetalle->producto_nombre = $producto->nombre;
                    $guiadespintdetalle->espesor = $request->espesor[$i];
                    $guiadespintdetalle->diametro = $producto->diametro;
                    $guiadespintdetalle->categoriaprod_id = $producto->categoriaprod_id;
                    $guiadespintdetalle->claseprod_id = $producto->claseprod_id;
                    $guiadespintdetalle->grupoprod_id = $producto->grupoprod_id;
                    $guiadespintdetalle->color_id = $producto->color_id;

                    $guiadespintdetalle->ancho = $request->ancho[$i];
                    $guiadespintdetalle->largo = $request->long[$i];
                    $guiadespintdetalle->obs = $request->obs[$i];

                    $guiadespintdetalle->save();
                    $idDireccion = $guiadespintdetalle->id;
                }
            }
        }
        //return redirect('cotizacion')->with('mensaje','Cotización creada con exito');
        return redirect('guiadespint')->with('mensaje','Guia Interna creada con exito!');
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
        can('editar-guia-interna');
        $data = GuiaDespInt::findOrFail($id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $guiadespintdetalles = $data->guiadespintdetalle()->get();
        $vendedor_id=$data->vendedor_id;
        $clienteselec = $data->clienteinterno()->get();
        //dd($clienteselec);

        $tablas = array();
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        
        $fecha = date("d/m/Y", strtotime($data->fechahora));

        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $tablas['vendedor_id'] = $clientesArray['vendedor_id'];
        $tablas['sucurArray'] = $clientesArray['sucurArray'];
        $productos = Producto::productosxUsuario();

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
        $aux_sta=2;

        return view('guiadespint.editar', compact('data','clienteselec','clientes','guiadespintdetalles','productos','fecha','aux_sta','aux_cont','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarGuiaDespInt $request, $id)
    {
        //dd($request);
        can('guardar-guia-interna');
        $guiadespint = GuiaDespInt::findOrFail($id);
        $request->request->add(['fechahora' => $guiadespint->fechahora]);
        $aux_plazoentrega= DateTime::createFromFormat('d/m/Y', $request->plazoentrega)->format('Y-m-d');
        $request->request->add(['plazoentrega' => $aux_plazoentrega]);
        //dd($request->plazoentrega);
        $guiadespint->update($request->all());
        $auxDet=GuiaDespIntDetalle::where('guiadespint_id',$id)->whereNotIn('id', $request->det_id)->pluck('id')->toArray(); //->destroy();
        for ($i=0; $i < count($auxDet) ; $i++){
            GuiaDespIntDetalle::destroy($auxDet[$i]);
        }
        $cont_cotdet = count($request->det_id);
        if($cont_cotdet>0){
            for ($i=0; $i < count($request->det_id) ; $i++){
                //$idcotizaciondet = $request->det_id[$i]; 
                $producto = Producto::findOrFail($request->producto_id[$i]);
                if( $request->det_id[$i] == '0' ){
                    $guiadespintdetalle = new GuiaDespIntDetalle();
                    $guiadespintdetalle->guiadespint_id = $id;
                    $guiadespintdetalle->producto_id = $request->producto_id[$i];
                    $guiadespintdetalle->cant = $request->cant[$i];
                    $guiadespintdetalle->unidadmedida_id = $request->unidadmedida_id[$i];
                    $guiadespintdetalle->preciounit = $request->preciounit[$i];
                    $guiadespintdetalle->peso = $request->peso[$i];
                    $guiadespintdetalle->precioxkilo = $request->precioxkilo[$i];
                    $guiadespintdetalle->precioxkiloreal = $request->precioxkiloreal[$i];
                    $guiadespintdetalle->totalkilos = $request->totalkilos[$i];
                    $guiadespintdetalle->subtotal = $request->subtotal[$i];

                    $guiadespintdetalle->producto_nombre = $producto->nombre;
                    $guiadespintdetalle->ancho = $request->ancho[$i];
                    $guiadespintdetalle->largo = $request->long[$i];
                    $guiadespintdetalle->espesor = $request->espesor[$i];
                    $guiadespintdetalle->diametro = $request->diametro[$i];
                    $guiadespintdetalle->categoriaprod_id = $producto->categoriaprod_id;
                    $guiadespintdetalle->claseprod_id = $producto->claseprod_id;
                    $guiadespintdetalle->grupoprod_id = $producto->grupoprod_id;
                    $guiadespintdetalle->color_id = $producto->color_id;    
                    $guiadespintdetalle->descuento = $request->descuento[$i];
                    $guiadespintdetalle->obs = $request->obs[$i];
                    $guiadespintdetalle->save();
                    //$idcotizaciondet = $guiadespintdetalle->id;
                    //dd($idDireccion);
                }else{
                    //dd($idDireccion);
                    DB::table('guiadespintdetalle')->updateOrInsert(
                        ['id' => $request->det_id[$i], 'guiadespint_id' => $id],
                        [
                            'producto_id' => $request->producto_id[$i],
                            'cant' => $request->cant[$i],
                            'unidadmedida_id' => $request->unidadmedida_id[$i],
                            'preciounit' => $request->preciounit[$i],
                            'peso' => $producto->peso,
                            'precioxkilo' => $request->precioxkilo[$i],
                            'precioxkiloreal' => $request->precioxkiloreal[$i],
                            'totalkilos' => $request->totalkilos[$i],
                            'subtotal' => $request->subtotal[$i],
                            'producto_nombre' => $producto->nombre,
                            'espesor' => $request->espesor[$i],
                            'ancho' => $request->ancho[$i],
                            'largo' => $request->long[$i],
                            'diametro' => $producto->diametro,
                            'categoriaprod_id' => $producto->categoriaprod_id,
                            'claseprod_id' => $producto->claseprod_id,
                            'grupoprod_id' => $producto->grupoprod_id,
                            'color_id' => $producto->color_id,
                            'descuento' => $request->descuento[$i],
                            'obs' => $request->obs[$i]
                        ]
                    );
                }
            }
        }
        return redirect('guiadespint')->with('mensaje','Guia despacho interna actualizada con exito!');
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

    public function exportPdf($id,$stareport = '1')
    {
        $guiadespint = GuiaDespInt::findOrFail($id);
        $guiadespintDetalles = $guiadespint->guiadespintdetalle()->get();
        $empresa = Empresa::orderBy('id')->get();
        $aux_razonsocial = $guiadespint->cli_nom;
        if(env('APP_DEBUG')){
            return view('guiadespint.listado', compact('guiadespint','guiadespintDetalles','empresa'));
        }
        $pdf = PDF::loadView('guiadespint.listado', compact('guiadespint','guiadespintDetalles','empresa'));
        //return $pdf->download('cotizacion.pdf');
        return $pdf->stream(str_pad($guiadespint->id, 5, "0", STR_PAD_LEFT) .' - '. $aux_razonsocial . '.pdf');
    }

}
