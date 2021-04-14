<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarColor;
use App\Models\AcuerdoTecTemp;
use App\Models\CategoriaProd;
use App\Models\Certificado;
use App\Models\ClienteDirec;
use App\Models\Color;
use App\Models\FormaPago;
use App\Models\MatFabr;
use App\Models\PlazoPago;
use App\Models\Seguridad\Usuario;
use App\Models\UnidadMedida;
use App\User;
use Illuminate\Http\Request;

class AcuerdoTecTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-acuerdo-tecnico-temp');
        $datas = AcuerdoTecTemp::orderBy('id')->get();
        return view('acuerdotectemp.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-acuerdo-tecnico-temp');
        $clientedirecs = ClienteDirec::orderBy('id')->get();
        /*
        foreach($clientedirecs as $clientedirec){
            dd($clientedirec->cliente->razonsocial);
        }
        */
        //$clientedirecs = ClienteDirec::findOrFail(1);
        //dd($clientedirecs->cliente->razonsocial);
        /*
        foreach($clientedirecs as $clientedirec){
            dd($clientedirec->cliente->razonsocial);
        }*/
        $matfabrs = MatFabr::orderBy('id')->get();
        $categoriaprods = CategoriaProd::orderBy('id')->get(); //Aqui no estoy filtrando solo las categorias de sucursal del usuario logueado
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
        //******************* */
        $categoriaprods = CategoriaProd::join('categoriaprodsuc', 'categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
        ->join('sucursal', 'categoriaprodsuc.sucursal_id', '=', 'sucursal.id')
        ->select(['categoriaprod.id as id',
                'categoriaprod.nombre as nombre',
                'categoriaprodsuc.sucursal_id as sucursal_id',
                'sucursal.nombre as nombre_suc'
                ])
                ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
                ->get();
        //****************** */

        $unidadmedidas = UnidadMedida::orderBy('id')->get();
        $nosis = [
            'No',
            'Si'
        ];
        $colores = Color::orderBy('id')->get();
        $transparencias = [
            ['id' => 1,
            'nombre' => 'No translucido'],
            ['id' => 2,
            'nombre' => 'Opaco semi translucido'],
            ['id' => 3,
            'nombre' => 'Alta Transparencia']
        ];
        $certificados = Certificado::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $plazopagos = PlazoPago::orderBy('id')->get();
        //dd($transparencias);
        $aux_sta = 1;
        return view('acuerdotectemp.crear',compact('clientedirecs','matfabrs','categoriaprods','unidadmedidas','nosis','colores','transparencias','certificados','plazopagos','aux_sta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarColor $request)
    {
        can('guardar-acuerdo-tecnico-temp');
        AcuerdoTecTemp::create($request->all());
        return redirect('acuerdotectemp')->with('mensaje','Acuerdo Técnico Temp creado con exito');
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
        can('editar-acuerdo-tecnico-temp');
        $data = AcuerdoTecTemp::findOrFail($id);
        return view('acuerdotectemp.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarColor $request, $id)
    {
        AcuerdoTecTemp::findOrFail($id)->update($request->all());
        return redirect('acuerdotectemp')->with('mensaje','Acuerdo Técnico Temp actualizado con exito');
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
            if (AcuerdoTecTemp::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}