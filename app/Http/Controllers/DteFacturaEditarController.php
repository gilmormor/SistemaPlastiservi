<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarDTE;
use App\Http\Requests\ValidarDTEFacturaEditar;
use App\Models\AreaProduccion;
use App\Models\CentroEconomico;
use App\Models\Comuna;
use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Foliocontrol;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DteFacturaEditarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-dte-factura-editar');

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        return view('dtefacturaeditar.index', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }

    public function dtefacturaeditarpage(Request $request){
        //can('reporte-guia_despacho');
        //dd('entro');
        //$datas = GuiaDesp::reporteguiadesp($request);
        $datas = Dte::reportdtefac($request);
        return datatables($datas)->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-dte-factura-editar');
        $data = Dte::findOrFail($id);
        $sql = "SELECT oc_id,oc_folder,oc_file
            FROM dteoc
            WHERE dteoc.dte_id = $id
            AND ISNULL(dteoc.deleted_at)
            GROUP BY dteoc.oc_id;";
    //dd($sql);
        $tablas['dteoc'] =  DB::select($sql);
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        $tablas['foliocontrol'] = Foliocontrol::orderBy('id')->get();
        $tablas['empresa'] = Empresa::findOrFail(1);
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();

        //$centroeconomicos = CentroEconomico::orderBy('id')->get();
        $tablas['centroeconomicos'] = CentroEconomico::orderBy('id')->get(); //$data->sucursal->centroeconomicos;
        $tablas['sucursales'] = Sucursal::orderBy('id')
            ->whereIn('sucursal.id', $sucurArray)
            ->get();


        return view('dtefacturaeditar.editar', compact('data','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarDTEFacturaEditar $request, $id)
    {
        can('guardar-dte-factura-editar');
        $dte = Dte::findOrFail($id);

        if($request->updatededitar_at != $dte->updated_at){
            return response()->json([
                'error' => 1,
                'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                'tipo_alert' => 'error'
            ]);
        }
        //dd($request);
        $dte->update($request->all());
        return redirect('dtefacturaeditar')->with('mensaje','Actualizado con exito.');
    }

}
