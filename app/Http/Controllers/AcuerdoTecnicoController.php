<?php

namespace App\Http\Controllers;

use App\Models\AcuerdoTecnico;
use App\Models\AcuerdoTecnicoCValAtDet;
use App\Models\AcuerdoTecnicoTemp;
use App\Models\Certificado;
use App\Models\Cliente;
use App\Models\CValAt;
use App\Models\CValAtDet;
use App\Models\Empresa;
use App\Models\MateriaPrima;
use App\Models\Producto;
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
            /* $sql = "SELECT acuerdotecnico.*, producto.nombre as producto_nombre
                        FROM acuerdotecnico INNER JOIN producto
                        on acuerdotecnico.producto_id = producto.id
                        and isnull(acuerdotecnico.deleted_at)
                        WHERE acuerdotecnico.id IN (1362,1429,1430);";
                        $datas = DB::select($sql);
                        //dd($datas);
            return $datas; */
            //dd($request->at_impreso);
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

            $aux_Condacuerdotecnico_idEditAct = " true";
            if(isset($request->acuerdotecnico_idEditAct) and !is_null($request->acuerdotecnico_idEditAct) and !empty($request->acuerdotecnico_idEditAct) and $request->acuerdotecnico_idEditAct != ""){
                $aux_Condacuerdotecnico_idEditAct = " acuerdotecnico.id != $request->acuerdotecnico_idEditAct";
            }

            $json = json_decode($request->objtxt);
            $sql = "SELECT acuerdotecnico.*, producto.glosa as producto_nombre
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
            and isnull(acuerdotecnico.deleted_at)
            and $aux_Condacuerdotecnico_idEditAct;";
            
            $datas = DB::select($sql);
            $dataresultado = [];
            //dd($datas);
            foreach ($datas as &$data) {
                //$producto = Producto::find($data->producto_id);
                //$data->producto_nombre = $producto->glosa;

                // 1. Obtener TODOS los registros del acuerdo técnico
                $registros = AcuerdoTecnicoCValAtDet::where('acuerdotecnico_id', $data->id)->get();
                // 2. Obtener todos los campos at_cvalatdet del request
                $camposCValAtDet = $this->obtenerCamposCValAtDet($request);

                // 3. CASO 1: No hay registros en BD
                if ($registros->isEmpty()) {
                    // Verificar si TODOS los campos del request son "0"
                    $todosSonCero = $this->todosLosCamposSonCero($camposCValAtDet);
                    $data->coincide = $todosSonCero;
                    if($data->coincide){
                        $dataresultado[] = $data;
                    }
                    continue;
                    //return $todosSonCero;
                }

                // 4. CASO 2: Hay registros en BD
                $totalRegistros = $registros->count();
                $matchCount = 0;

                foreach ($registros as $registro) {
                    $fieldName = 'at_cvalatdet' . $registro->cvalatdet_id;

                    // Verificar si el campo existe en el request
                    if ($request->has($fieldName)) {
                        // Comparar el valor del request con el valor en BD
                        if ($request->get($fieldName) == $registro->valor) {
                            $matchCount++;
                        }
                    }
                }
                //dd($data);
                // 5. Si todos los registros coinciden, retornar true
                if ($matchCount === $totalRegistros){
                    $data->coincide = true;
                } else {
                    $data->coincide = false;
                }
                if($data->coincide == true){
                    $dataresultado[] = $data;
                }
            }
            //dd($dataresultado);
            // Unificado 2026-07-24: respuesta envuelta (consumida por cotizacion/cotizacionatfirmado/cotizacionaprobaracutec/atfirmadosubir)
            // con el array ya filtrado por coincidencia de CValAtDet (antes en $dataresultado, usado por ateditar).
            $respuesta["acuerdotecnico"] = $dataresultado;
            $respuesta["producto"]["nombre"] = nombreProductoAT($request);
            $at = new AcuerdoTecnico();
            $at->producto = Producto::findOrFail($request->producto_id);
            $at->at_formatofilm = $request->at_formatofilm;
            $at->at_unidadmedida_id = $request->at_unidadmedida_id;
            $at->at_espesor = $request->at_espesor;
            $at->at_peso = 0;
            $at->at_ancho = $request->at_ancho;
            $at->at_largo = $request->at_largo;
            $at->at_espesor = $request->at_espesor;
            $at->materiaprima = MateriaPrima::findOrFail($request->at_materiaprima_id);
            $respuesta["producto"]["peso"] = pesounitat($at);
            //dd($respuesta);
            return $respuesta;
            //return datatables($datas)->toJson();
    

            //return $respuesta;
            //return response()->json($productos->get());
        }
    }
    /**
     * Método auxiliar: Obtener todos los campos at_cvalatdet del request
     */
    private function obtenerCamposCValAtDet(Request $request): array
    {
        $campos = [];
        
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'at_cvalatdet')) {
                $campos[$key] = $value;
            }
        }
        
        return $campos;
    }
    
    /**
     * Método auxiliar: Verificar si TODOS los campos son "0"
     */
    private function todosLosCamposSonCero(array $campos): bool
    {
        if (empty($campos)) {
            return true; // Si no hay campos, se considera válido
        }
        
        foreach ($campos as $valor) {
            if ($valor != "0") {
                return false;
            }
        }
        
        return true;
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
            $tablas['certificado'] = Certificado::orderBy('id')->get();
            $sql = "SELECT certificado.descripcion
            FROM certificado
            where certificado.id in ($acuerdotecnico->at_certificados)
            order by certificado.id asc;";
            $aux_certificados = DB::select($sql);
            $certificado_array = [];
            foreach($aux_certificados as $aux_certificado){
                $certificado_array[] = $aux_certificado->descripcion;
            }
            $tablas['certificados'] = implode(", ", $certificado_array);
            $tablas['cvalats'] = CValAt::orderBy('orden')->get();

            //dd($tablas['certificado']);
            //return view('generales.acuerdotecnicopdf', compact('acuerdotecnico','cliente','empresa'));
            if(env('APP_DEBUG')){
                //return view('generales.acuerdotecnicopdf', compact('acuerdotecnico','cliente','empresa','aux_tituloreporte','categoria_nombre','tablas','request'));
            }
            $pdf = PDF::loadView('generales.acuerdotecnicopdf', compact('acuerdotecnico','cliente','empresa','aux_tituloreporte','categoria_nombre','tablas','request'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream(str_pad("IdProd_" . $acuerdotecnico->producto_id . " IdAT_" . $acuerdotecnico->id, 5, "0", STR_PAD_LEFT) . $aux_nombreCliente . '.pdf');
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
        
        
    }

    public function obtenerCamposValidacion(Request $request)
    {
        //$items = CValAtDet::orderBy('orden')->get(['id','cvalat_id', 'nombre']);
        $items = CValAt::with(['cvalatdets' => function($query) {
                $query->select('id', 'cvalat_id', 'nombre', 'orden')
                    ->orderBy('orden'); // Ordena los hijos por 'orden'
            }])
            ->orderBy('orden') // Ordena los padres por 'orden'
            ->get(['id', 'nombre', 'orden']);

        //dd($items);
        return response()->json($items);
    }

}