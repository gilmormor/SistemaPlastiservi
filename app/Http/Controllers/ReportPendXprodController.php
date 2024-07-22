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
use App\Models\NotaVentaDetalle;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class ReportPendXprodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-pendiente-por-producto');
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
        return view('reportpendxprod.index', compact('giros','areaproduccions','tipoentregas','comunas','fechaAct','tablashtml'));

    }
    public function reportpendxprodpage(Request $request){
        return Producto::pendxprod($request);
    }

    public function exportPdf(Request $request)
    {
        $datas = Dte::reportdtefac($request);
        //dd($datas);

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $request->merge(['sucursal_nombre' => "Todos"]);
        }else{
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            $aux_sucursalNombre = $sucursal->nombre;
            $request->merge(['sucursal_nombre' => $sucursal->nombre]);
        }
        if($datas){
            
            if(env('APP_DEBUG')){
                return view('reportdtefac.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'));
            //$pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');

            return $pdf->stream("reportdtefac.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }    
}