<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarNotaVentaCerrada;
use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteBloqueado;
use App\Models\ClienteSucursal;
use App\Models\CodigoDet;
use App\Models\Comuna;
use App\Models\DespachoOrd;
use App\Models\DespachoSol;
use App\Models\Giro;
use App\Models\NotaVentaCerrada;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaVentaCerradaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cerrar-nota-venta');
        return view('notaventacerrar.index');
    }

    public function notaventacerradapage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        
        return datatables()
            ->eloquent(NotaVentaCerrada::query()
            ->join('notaventa','notaventacerrada.notaventa_id','=','notaventa.id')
            ->join('codigodet','notaventacerrada.codigodet_id','=','codigodet.id')
            ->select([
                'notaventacerrada.id',
                'notaventacerrada.created_at',
                'notaventacerrada.notaventa_id',
                'notaventacerrada.observacion',
                'codigodet.descdet'
                ])
            ->whereIn('notaventa.sucursal_id', $sucurArray)
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
        can('crear-cerrar-nota-venta');

        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        $arrayvend = Vendedor::vendedores(); //Esto viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];
        $clientevendedorArray = $arrayvend['clientevendedorArray'];

        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaServ = [
                    'fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y"),
                    ];
        $productos = Producto::productosxUsuario();
        $comunas = Comuna::orderBy('id')->get();
        $codigodets = CodigoDet::orderBy('id')
                    ->where('codigodet.codigo_id', 1)
                    ->get();
        $aux_editar = 0;
        return view('notaventacerrar.crear', compact('aux_editar','clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','fechaServ','productos','comunas','codigodets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        can('guardar-cerrar-nota-venta');
        $despachosolcontroller = New DespachoSolController();
        $cont_soldesp = count($despachosolcontroller->consultarSolDesp($request));
        if($cont_soldesp > 0){
            return redirect('notaventacerrada')->with([
                'mensaje'=>'NV No fue cerrada, existen solicitudes activas, debe cerrarlas.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $despachoordAGD = DespachoOrd::consultaOrdDespxAsigGuiaDesp($request);
        $cont_orddesp = count($despachoordAGD);
        if($cont_orddesp > 0){
            return redirect('notaventacerrada')->with([
                'mensaje'=>'NV No fue cerrada, existen Ordenes de Despacho por asignar Guia de despacho.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $despachoordAF = DespachoOrd::consultaOrdDespxAsigFact($request);
        $cont_orddesp = count($despachoordAF);
        if($cont_orddesp > 0){
            return redirect('notaventacerrada')->with([
                'mensaje'=>'NV No fue cerrada, existen Ordenes de Despacho por asignar Factura.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $sql = "SELECT COUNT(*) as cont
        FROM notaventa
        where id = $request->notaventa_id
        and anulada is null
        and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        and notaventa.deleted_at is null;";
        $datas = DB::select($sql);
        if($datas[0]->cont > 0){
            $request->request->add(['usuario_id' => auth()->id()]);
            NotaVentaCerrada::create($request->all());
            return redirect('notaventacerrada')->with('mensaje','Creado con exito');
        }else{
            return redirect('notaventacerrada')->with('mensaje','Nota de venta no existe');
        }
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
        can('editar-cerrar-nota-venta');
        $data = NotaVentaCerrada::findOrFail($id);
        $codigodets = CodigoDet::orderBy('id')
            ->where('codigodet.codigo_id', 1)
            ->get();
        $aux_editar = 1;
        return view('notaventacerrar.editar', compact('data','aux_editar','codigodets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarNotaVentaCerrada $request, $id)
    {
        NotaVentaCerrada::findOrFail($id)->update($request->all());
        return redirect('notaventacerrada')->with('mensaje','Actualizado con exito');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = NotaVentaCerrada::findOrFail($id);
            $data->usuariodel_id = auth()->id();
            $data->save();
            if (NotaVentaCerrada::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}