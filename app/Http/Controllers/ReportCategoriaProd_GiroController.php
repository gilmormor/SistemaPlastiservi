<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportCategoriaProd_GiroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-categoriaprod-giro');
        return view('reportcategoriaprod_giro.index', compact('giros','areaproduccions','tipoentregas','comunas','fechaAct','tablashtml'));

    }
    public function reportcategoriaprod_giropage(){
        $datas = consulta();
        return datatables($datas)->toJson();
    }
}


function consulta(){
    $sql = "SELECT categoriaprod_giro.categoriaprod_id , categoriaprod_giro.id,categoriaprod.nombre,
	(SELECT preciokg FROM categoriaprod_giro WHERE categoriaprod_giro.categoriaprod_id=categoriaprod.id AND categoriaprod_giro.giro_id = 2) AS distribuidor,
	(SELECT preciokg FROM categoriaprod_giro WHERE categoriaprod_giro.categoriaprod_id=categoriaprod.id AND categoriaprod_giro.giro_id = 1) AS comercializadora,
	(SELECT preciokg FROM categoriaprod_giro WHERE categoriaprod_giro.categoriaprod_id=categoriaprod.id AND categoriaprod_giro.giro_id = 3) AS clientefinal,
	(SELECT preciokg FROM categoriaprod_giro WHERE categoriaprod_giro.categoriaprod_id=categoriaprod.id AND categoriaprod_giro.giro_id = 4) AS meson
	FROM giro INNER JOIN categoriaprod_giro
	ON giro.id = categoriaprod_giro.giro_id AND ISNULL(categoriaprod_giro.deleted_at)
	INNER JOIN categoriaprod
	ON categoriaprod.id = categoriaprod_giro.categoriaprod_id
	WHERE giro.id > 0
	GROUP BY categoriaprod_giro.categoriaprod_id
	ORDER BY categoriaprod.nombre;";
    $datas = DB::select($sql);
    if(count($datas)>0){
        $aux_contreg = count($datas)>0 ? count($datas) : 1;
        $datas[0]->datosAdicionales = [
            'fecha' => date("d/m/Y"),
            'hora' => date("h:i:s A")
        ];     
    }

    return $datas;
}