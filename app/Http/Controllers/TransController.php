<?php

namespace App\Http\Controllers;

use App\Models\TransChofer;
use App\Models\TransEmp;
use App\Models\TransVeh;
use Illuminate\Http\Request;

class TransController extends Controller
{
    public function buscarTransportista(Request $request){
        if($request->ajax()){
            $rut = $this->limpiarRut($request->rut); // Limpiar puntos y guión
            $transportista = TransEmp::where('rut', $rut)->first();
            
            if ($transportista) {
                return response()->json([
                    'existe' => true,
                    'nombre' => $transportista->nombre
                ]);
            } else {
                return response()->json(['existe' => false]);
            }
        }
    }
    public function buscarChofer(Request $request){
        if($request->ajax()){
            $rut = $this->limpiarRut($request->rut); // Limpiar puntos y guión
            $chofer = TransChofer::where('rut', $rut)->first();
            
            if ($chofer) {
                return response()->json([
                    'existe' => true,
                    'nombre' => $chofer->nombre
                ]);
            } else {
                return response()->json(['existe' => false]);
            }
        }
    }
    public function buscarPatente(Request $request){
        if($request->ajax()){
            $chofer = TransVeh::where('patente', $request->patente)->first();

            if ($chofer) {
                return response()->json([
                    'existe' => true
                ]);
            } else {
                return response()->json(['existe' => false]);
            }
        }
    }

    public function buscarTransportista1(Request $request)
    {
        dd($request);
        if ($request->ajax()) {
            $rut = $this->limpiarRut($request->rut); // Limpiar puntos y guión
            $transportista = TransEmp::where('rut', $rut)->first();
            
            if ($transportista) {
                return response()->json([
                    'existe' => true,
                    'nombre' => $transportista->nombre
                ]);
            } else {
                return response()->json(['existe' => false]);
            }
        }
        abort(404);
    }

    public function buscarChofer1(Request $request)
    {
        if ($request->ajax()) {
            $rut = $this->limpiarRut($request->rut);
            $chofer = TransChofer::where('rut', $rut)->first();
            
            if ($chofer) {
                return response()->json([
                    'existe' => true,
                    'nombre_completo' => $chofer->nombre_completo
                ]);
            } else {
                return response()->json(['existe' => false]);
            }
        }
        abort(404);
    }

    public function validarPatente(Request $request)
    {
        if ($request->ajax()) {
            $patente = strtoupper(trim($request->patente));
            $vehiculo = TransVeh::where('patente', $patente)->first();
            
            if ($vehiculo) {
                return response()->json(['existe' => true]);
            } else {
                return response()->json(['existe' => false]);
            }
        }
        abort(404);
    }

    // Función auxiliar para limpiar RUT
    private function limpiarRut($rut)
    {
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        return strtoupper($rut);
    }
}
