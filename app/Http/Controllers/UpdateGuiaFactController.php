<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Comuna;
use App\Models\Giro;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;

class UpdateGuiaFactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-despacho-cerradas-edit-guia-fact');
        $giros = $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::areaproduccionxusuario();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $aux_verestado='3'; //3 muestra boton de editar Num Guia y Num Fact 
        $titulo = "Editar Número Guia o Factura";
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $selecmultprod = 1;
        return view('reportorddespguiafact.index', compact('giros','areaproduccions','tipoentregas','fechaAct','aux_verestado','titulo','tablashtml','selecmultprod'));
    }
}
