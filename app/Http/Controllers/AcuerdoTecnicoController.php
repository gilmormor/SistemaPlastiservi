<?php

namespace App\Http\Controllers;

use App\Models\AcuerdoTecnico;
use App\Models\AcuerdoTecnicoTemp;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;


class AcuerdoTecnicoController extends Controller
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

    public function buscaratxcampos(Request $request)
    {
        if($request->ajax()){
            //dd($request);
            $aux_ta_fuelleCond = " true ";
            if(!is_null($request->at_fuelle)){
                $aux_ta_fuelleCond = "if(isnull(at_fuelle),'',at_fuelle) = $request->at_fuelle";
            }
            $aux_ta_largoCond = " true ";
            if(!is_null($request->at_fuelle)){
                $aux_ta_largoCond = "if(isnull(at_largo),'',at_largo) = $request->at_largo";
            }
            $aux_at_feunidxpaq = $request->at_feunidxpaq;
            if(is_null($request->at_feunidxpaq)){
                $aux_at_feunidxpaq = "";
            }
            $aux_at_feunidxcont = $request->at_feunidxcont;
            if(is_null($request->at_feunidxcont)){
                $aux_at_feunidxcont = "";
            }
            $aux_at_feunitxpalet = $request->at_feunitxpalet;
            if(is_null($request->at_feunitxpalet)){
                $aux_at_feunitxpalet = "";
            }
            $aux_Condat_formatofilm = "at_formatofilm = $request->at_formatofilm";
            if(is_null($request->at_formatofilm) or empty($request->at_formatofilm) or $request->at_formatofilm == ""){
                $aux_Condat_formatofilm = "at_formatofilm = 0";
            }
            $json = json_decode($request->objtxt);
            $sql = "SELECT acuerdotecnico.*, producto.nombre as producto_nombre
            FROM acuerdotecnico INNER JOIN producto
            on acuerdotecnico.producto_id = producto.id
            WHERE at_claseprod_id = $request->at_claseprod_id
            and at_materiaprima_id = $request->at_materiaprima_id
            and at_color_id = $request->at_color_id
            and at_pigmentacion = $request->at_pigmentacion
            and at_translucidez = $request->at_translucidez
            and at_uv = $request->at_uv
            and at_antideslizante = $request->at_antideslizante
            and at_antiestatico = $request->at_antiestatico
            and at_antiblock = $request->at_antiblock
            and at_aditivootro = $request->at_aditivootro
            and at_ancho = $request->at_ancho
            and $aux_ta_fuelleCond
            and $aux_ta_largoCond
            and at_espesor = $request->at_espesor
            and at_impreso = $request->at_impreso
            and at_tiposello_id = '$request->at_tiposello_id'
            and if(isnull(at_feunidxpaq),'',at_feunidxpaq) = '$aux_at_feunidxpaq' 
            and if(isnull(at_feunidxcont),'',at_feunidxcont) = '$aux_at_feunidxcont' 
            and if(isnull(at_feunitxpalet),'',at_feunitxpalet) = '$aux_at_feunitxpalet' 
            and at_unidadmedida_id = $request->at_unidadmedida_id
            and $aux_Condat_formatofilm
            and at_etiqplastiservi = $request->at_etiqplastiservi
            and isnull(acuerdotecnico.deleted_at)";
            $datas = DB::select($sql);
            //dd($datas);
            return $datas;
            //return datatables($datas)->toJson();
    

            //return $respuesta;
            //return response()->json($productos->get());
        }
    }
/*
    public function exportPdf($id,$stareport = '1')
    {
        if(can('ver-pdf-acuerdo-tecnico',false)){
            $aux_tituloreportte = "";
            if($stareport == '1'){
                $acuerdotecnico = AcuerdoTecnico::findOrFail($id);
            }else{
                $acuerdotecnico = AcuerdoTecnicoTemp::findOrFail($id);
                $aux_tituloreportte = "Temporal";
            }
            $empresa = Empresa::orderBy('id')->get();
            $rut = number_format( substr ( $acuerdotecnico->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $notaventa->cliente->rut, strlen($notaventa->cliente->rut) -1 , 1 );

            //dd($empresa[0]['iva']);
            if(env('APP_DEBUG')){
                return view('general.acuerdotecnicopdf', compact('acuerdotecnico','empresa'));
            }
            $pdf = PDF::loadView('general.acuerdotecnicopdf', compact('notaventa','notaventaDetalles','empresa'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
        
        
    }
*/
    public function exportPdf(Request $request)
    {
        if(can('ver-pdf-acuerdo-tecnico',false)){
            //dd($request);
            $aux_tituloreportte = "";
            //dd($request->cliente_id);
            $aux_nombreCliente = "";
            $cliente = "";
            if($request->cliente_id != 0){
                $cliente = Cliente::findOrFail($request->cliente_id);
                $aux_nombreCliente = ' - '. $cliente->razonsocial;
                $rut = number_format( substr ( $cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $cliente->rut, strlen($cliente->rut) -1 , 1 );
            }
            $acuerdotecnico = AcuerdoTecnico::findOrFail($request->id);
            //dd($acuerdotecnico);
            $categoria_nombre = $acuerdotecnico->producto->categoriaprod->nombre;
            //dd($categoria_nombre);
            $aux_tituloreporte = "";
            /*
            $notaventa = NotaVenta::findOrFail($id);
            $notaventaDetalles = $notaventa->notaventadetalles()->get();
            */
            $empresa = Empresa::orderBy('id')->get();

            //dd($empresa[0]['iva']);
            //return view('generales.acuerdotecnicopdf', compact('acuerdotecnico','cliente','empresa'));
            if(env('APP_DEBUG')){
                return view('generales.acuerdotecnicopdf', compact('acuerdotecnico','cliente','empresa','aux_tituloreporte','categoria_nombre','request'));
            }
            $pdf = PDF::loadView('generales.acuerdotecnicopdf', compact('acuerdotecnico','cliente','empresa','aux_tituloreporte','categoria_nombre','request'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream(str_pad("IdProd_" . $acuerdotecnico->producto_id . " IdAT_" . $acuerdotecnico->id, 5, "0", STR_PAD_LEFT) . $aux_nombreCliente . '.pdf');
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
        
        
    }

}