<?php

namespace App\Http\Controllers;

use App\Models\InvStock;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;

class InvStockController extends Controller
{
    public function consexistencia(Request $request){
        //if($request->ajax()){
            $invstock = InvStock::where('producto_id','=',$request->producto_id)
                                ->where('invbodega_id','=',$request->invbodega_id);
            $array_invstock = $invstock->get()->toArray();
            $respuesta = array();
            if(count($array_invstock) > 0){
                $respuesta['invstock'] = $array_invstock[0];
            }
            $respuesta['cont'] = count($array_invstock);
            return $respuesta;
        //}
    }
}
