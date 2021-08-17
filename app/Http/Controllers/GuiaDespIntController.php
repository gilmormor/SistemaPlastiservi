<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return datatables()
        ->eloquent(GuiaDespInt::query())
        ->toJson();
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
        $dateInput = explode('/',$request->plazoentrega);
        $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        dd($request);
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
    public function edit($id)
    {
        //
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
