<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaGrupoValMes;
use App\Models\CategoriaProd;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\InvBodega;
use App\Models\InvMov;
use App\Models\NotaVenta;
use App\Models\NotaVentaDetalle;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class ReportNVAgruxClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-nota-venta-agrupada-x-cliente');
        $giros = Giro::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::vendedores();
        $tablashtml['vendedores'] = $tablashtml['vendedores']['vendedores'];
        //dd($tablashtml['vendedores']);
        foreach ($tablashtml['vendedores'] as $vendedor){
            //dd($vendedor);
        }
        $tablashtml['categoriaprod'] = CategoriaProd::categoriasxUsuario();
        $user = Usuario::findOrFail(auth()->id());
        $tablashtml['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablashtml['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablashtml['sucurArray'])->get();
        //dd(count($tablashtml['sucursales']));
        $areaproduccions = AreaProduccion::areaproduccionxusuario();
        //dd($areaproduccions);
        //$areaproduccions = AreaProduccion::orderBy('id')->get();

        $selecmultprod = 1;
        return view('reportnvagruxcliente.index', compact('giros','areaproduccions','tipoentregas','comunas','fechaAct','tablashtml'));

    }
    public function reportnvagruxclientepage(Request $request){
        $request->merge(['order' => ""]);
        $request->merge(['group' => "GROUP BY cliente.razonsocial"]);
        $datas = NotaVenta::consulta($request,1);
        //dd($datas);

        return datatables($datas)->toJson();
    }

    public function totalizarRep(Request $request){
        //dd($request);
        $respuesta = array();
        if($request->ajax()){
            $request->merge(['order' => ""]);
            $request->merge(['group' => ""]);
            $datas = NotaVenta::consulta($request,1);
            $aux_totalkgpvc = 0;
            $aux_totalkg = 0;
            $aux_totaldinero = 0;
            foreach ($datas as $data) {
                $aux_totalkgpvc += $data->pvckg;
                $aux_totalkg += $data->cankg;
                $aux_totaldinero += $data->total;
            }
            $respuesta['aux_totalkgpvc'] = $aux_totalkgpvc;
            $respuesta['aux_totalkg'] = $aux_totalkg;
            $respuesta['aux_totaldinero'] = $aux_totaldinero;
            return $respuesta;
        }
    }
}