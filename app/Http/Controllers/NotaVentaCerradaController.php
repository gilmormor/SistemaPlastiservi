<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarNotaVentaCerrada;
use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteBloqueado;
use App\Models\ClienteSucursal;
use App\Models\Comuna;
use App\Models\Giro;
use App\Models\NotaVentaCerrada;
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
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        
        $datas = NotaVentaCerrada::join('notaventa','notaventacerrada.notaventa_id','=','notaventa.id')
                ->select([
                    'notaventacerrada.id',
                    'notaventacerrada.notaventa_id',
                    'notaventacerrada.observacion',
                    'notaventacerrada.motcierre_id'
                    ])
                ->whereIn('notaventa.sucursal_id', $sucurArray)
                ->get();
        return view('notaventacerrar.index', compact('datas','sucursales'));
    }

    public function notaventacerradapage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        
        return datatables()
            ->eloquent(NotaVentaCerrada::query()
            ->join('notaventa','notaventacerrada.notaventa_id','=','notaventa.id')
            ->select([
                'notaventacerrada.id',
                'notaventacerrada.notaventa_id',
                'notaventacerrada.observacion',
                'notaventacerrada.motcierre_id',
                'notaventacerrada.notaventa_id'
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
        $user = Usuario::findOrFail(auth()->id());
        $arrayvend = Vendedor::vendedores(); //Esto viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];
        $clientevendedorArray = $arrayvend['clientevendedorArray'];

        $sucurArray = $user->sucursales->pluck('id')->toArray();
        //* Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado*/
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaServ = [
                    'fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y"),
                    ];
        //dd($fechaServ);
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
        //******************* */
        $productos = CategoriaProd::join('categoriaprodsuc', 'categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
        ->join('sucursal', 'categoriaprodsuc.sucursal_id', '=', 'sucursal.id')
        ->join('producto', 'categoriaprod.id', '=', 'producto.categoriaprod_id')
        ->join('claseprod', 'producto.claseprod_id', '=', 'claseprod.id')
        ->select([
                'producto.id',
                'producto.nombre',
                'claseprod.cla_nombre',
                'producto.codintprod',
                'producto.diamextmm',
                'producto.diamextpg',
                'producto.espesor',
                'producto.long',
                'producto.peso',
                'producto.tipounion',
                'producto.precioneto',
                'categoriaprod.precio',
                'categoriaprod.unidadmedida_id',
                'categoriaprodsuc.sucursal_id'
                ])
                ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
                ->get();
        $comunas = Comuna::orderBy('id')->get();
        $aux_editar = 0;
        return view('notaventacerrar.crear', compact('aux_editar','clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','fechaServ','productos','comunas'));
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
        $aux_editar = 1;
        return view('notaventacerrar.editar', compact('data','aux_editar'));
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