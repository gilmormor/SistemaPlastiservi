<?php

namespace App\Http\Controllers;

use App\Models\InvBodegaProducto;
use App\Models\InvMov;
use Illuminate\Http\Request;

class InvBodegaProductoController extends Controller
{
    public function consexistencia(Request $request){
    //public function consexistencia($request){
            $existencia = InvBodegaProducto::existencia($request);
        /*
        $annomes = date("Ym");
        $existencia = InvMov::selectRaw('SUM(cant) as cant')
                    ->join("invmovdet","invmov.id","=","invmovdet.invmov_id")
                    ->where("annomes","=",$annomes)
                    ->where("invmovdet.producto_id","=",$request->producto_id)
                    ->where("invmovdet.invbodega_id","=",$request->invbodega_id)
                    ->groupBy("invmovdet.producto_id")
                    ->get();
        $array_stock = $existencia->toArray();
        $respuesta = array();
        if(count($array_stock) > 0){
            $respuesta['stock'] = $array_stock[0];
        }else{
            $respuesta['stock']['cant'] = 0;
        }
        //$respuesta['stock']['cant'] += $request->cant;
        $respuesta['cont'] = count($array_stock);
        */
        //dd($respuesta);
        return $existencia;
    }
}
