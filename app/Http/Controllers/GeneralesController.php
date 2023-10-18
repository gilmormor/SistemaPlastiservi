<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generales_valpermiso(Request $request)
    {
        $array_resp = [
            'resp' => false,
            'mensaje' => "Usuario no tiene permiso.",
            'mensaje2' => "Pongase en contacto con el administrador del Sistema. " . $request->slug
        ];
        if(can($request->slug,false)){
            $array_resp = [
                'resp' => true,
                'mensaje' => "Acceso concedido"
            ];
        }
        return $array_resp; //response()->json($array_resp);
    }
}
