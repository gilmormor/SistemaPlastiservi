<?php

namespace App\Http\Controllers;

use App\Models\AcuerdoTecnicoTemp;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class AcuerdoTecnicoTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function exportPdf(Request $request)
    {
        if(can('ver-pdf-acuerdo-tecnico',false)){
            $aux_tituloreportte = "";
            $cliente = Cliente::findOrFail($request->cliente_id);
            $acuerdotecnico = AcuerdoTecnicoTemp::findOrFail($request->id);
            $categoria_nombre = $acuerdotecnico->cotizaciondetalle->producto->categoriaprod->nombre;
            //dd($categoria_nombre);
            $aux_tituloreporte = "Temporal";
            /*
            $notaventa = NotaVenta::findOrFail($id);
            $notaventaDetalles = $notaventa->notaventadetalles()->get();
            */
            $empresa = Empresa::orderBy('id')->get();
            $rut = number_format( substr ( $cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $cliente->rut, strlen($cliente->rut) -1 , 1 );

            //dd($empresa[0]['iva']);
            //return view('generales.acuerdotecnicopdf', compact('acuerdotecnico','cliente','empresa'));
            if(env('APP_DEBUG')){
                return view('generales.acuerdotecnicopdf', compact('acuerdotecnico','cliente','empresa'));
            }
            $pdf = PDF::loadView('generales.acuerdotecnicopdf', compact('acuerdotecnico','cliente','empresa','aux_tituloreporte','categoria_nombre'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream(str_pad($acuerdotecnico->id, 5, "0", STR_PAD_LEFT) .' - '. $cliente->razonsocial . '.pdf');
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
        
        
    }

}
