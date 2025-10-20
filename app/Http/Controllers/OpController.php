<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Op;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class OpController extends Controller
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

    public function exportPdf($id)
    {
        if(can('ver-pdf-ot',false)){
            $op = Op::findOrFail($id);
            //dd($op);
            $opdets = $op->opdets()->get();
            //dd($opdets);
            $empresa = Empresa::orderBy('id')->get();
            $aux_rut = $op->otdet->ot->cliente->rut;
            $rut = number_format( substr ( $aux_rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $aux_rut, strlen($aux_rut) -1 , 1 );
            //dd($empresa[0]['iva']);
            $pdf = PDF::loadView('op.reporte', compact('op','opdets','empresa'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream(str_pad($op->id, 5, "0", STR_PAD_LEFT) .' - '. $op->otdet->ot->cliente->razonsocial . '.pdf');
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
    }
}
