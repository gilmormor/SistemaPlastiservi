<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarInvEntSal;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\InvBodega;
use App\Models\InvBodegaProducto;
use App\Models\InvControl;
use App\Models\InvEntSal;
use App\Models\InvEntSalDet;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\InvMovTipo;
use App\Models\InvStock;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;


class InvEntSalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-entrada-salida-inventario');
        return view('inventsal.index');
    }

    public function inventsalpage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        return datatables()
            ->eloquent(InvEntSal::query()
                        ->whereNull('staaprob')
                        ->orWhere('staaprob', 3)
                        ->whereIn('inventsal.sucursal_id', $sucurArray)
                    )
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-entrada-salida-inventario');
        $invmovtipos = InvMovTipo::orderBy('id')->get();
        $productos = Producto::productosxUsuario();
        $clientesArray = Cliente::clientesxUsuario();
        $sucurArray = $clientesArray['sucurArray'];
        $tablas = array();
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        $selecmultprod = 0;
        return view('inventsal.crear',compact('invmovtipos','productos','tablas','selecmultprod'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarInvEntSal $request)
    {
        can('guardar-entrada-salida-inventario');
        $dateInput = explode('/',$request->fechahora);
        $request["fechahora"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0] . ' 06:00:00';
        //dd($request);

        $request->request->add(['invmovmodulo_id' => 1]);
        $request->request->add(['usuario_id' => auth()->id()]);
        /*
        $invmov = InvMov::create($request->all());
        $request->request->add(['invmov_id' => $invmov->id]);
        */
        //dd($request);
        $inventsal = InvEntSal::create($request->all());
        $inventsal_id = $inventsal->id;
        $cont_producto = count($request->producto_id);
        if($cont_producto>0){
            for ($i=0; $i < $cont_producto ; $i++){
                if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                    $producto = Producto::findOrFail($request->producto_id[$i]);
                    $invmovtipo = InvMovTipo::findOrFail($request->invmovtipo_idTD[$i]);
                    $inventsaldet = new InvEntSalDet();
                    $inventsaldet->inventsal_id = $inventsal_id;
                    $inventsaldet->producto_id = $request->producto_id[$i];
                    $inventsaldet->cant = $request->cant[$i] * $invmovtipo->tipomov;
                    $inventsaldet->cantgrupo = $request->cant[$i] * $invmovtipo->tipomov;
                    $inventsaldet->cantxgrupo = 1;
                    $inventsaldet->peso = $producto->peso;
                    $inventsaldet->cantkg = $request->totalkilos[$i] * $invmovtipo->tipomov;
                    $inventsaldet->unidadmedida_id = $request->unidadmedida_id[$i];
                    $inventsaldet->invbodega_id = $request->invbodega_idTD[$i];
                    $inventsaldet->invmovtipo_id = $request->invmovtipo_idTD[$i];
                    $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                        ['producto_id' => $inventsaldet->producto_id,'invbodega_id' => $inventsaldet->invbodega_id],
                        [
                            'producto_id' => $inventsaldet->producto_id,
                            'invbodega_id' => $inventsaldet->invbodega_id
                        ]
                    );
                    $inventsaldet->invbodegaproducto_id = $invbodegaproducto->id;
                    $inventsaldet->sucursal_id = $invbodegaproducto->invbodega->sucursal_id;
                    $inventsaldet->save();
                }
            }
        }
        return redirect('inventsal')->with('mensaje','Registro creado con exito.');
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
    public function editar($id)
    {
        can('editar-entrada-salida-inventario');
        $data = InvEntSal::findOrFail($id);
        $data->fechahora = $newDate = date("d/m/Y", strtotime($data->fechahora));
        //dd($data->inventsaldets);
        $invmovtipos = InvMovTipo::orderBy('id')->get();
        $productos = Producto::productosxUsuario();
        $clientesArray = Cliente::clientesxUsuario();
        $sucurArray = $clientesArray['sucurArray'];
        $tablas = array();
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        $selecmultprod = 0;
        return view('inventsal.editar', compact('data','invmovtipos','productos','tablas','selecmultprod'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(Request $request, $id)
    {
        can('guardar-entrada-salida-inventario');
        //dd($request->all());
        $inventsal = InvEntSal::findOrFail($id);
        $dateInput = explode('/',$request->fechahora);
        $request["fechahora"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0] . ' 06:00:00';
        if($inventsal->updated_at == $request->updated_at){
            $inventsal->updated_at = date("Y-m-d H:i:s");
            $inventsal->update($request->all());
            $auxNVDet=InvEntSalDet::where('inventsal_id',$id)->whereNotIn('id', $request->NVdet_id)->pluck('id')->toArray(); //->destroy();
            for ($i=0; $i < count($auxNVDet) ; $i++){
                InvEntSalDet::destroy($auxNVDet[$i]);
            }
            $cont_cotdet = count($request->NVdet_id);
            if($cont_cotdet>0){
                for ($i=0; $i < count($request->NVdet_id) ; $i++){
                    $invmovtipo = InvMovTipo::findOrFail($request->invmovtipo_idTD[$i]);
                    $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                        ['producto_id' => $request->producto_id[$i],'invbodega_id' => $request->invbodega_idTD[$i]],
                        [
                            'producto_id' => $request->producto_id[$i],
                            'invbodega_id' => $request->invbodega_idTD[$i]
                        ]
                    );
                    //$inventsaldet->invbodegaproducto_id = $invbodegaproducto->id;
                    $producto = Producto::findOrFail($request->producto_id[$i]);

                    DB::table('inventsaldet')->updateOrInsert(
                        ['id' => $request->NVdet_id[$i], 'inventsal_id' => $id],
                        [
                            'producto_id' => $request->producto_id[$i],
                            'unidadmedida_id' => $request->unidadmedida_id[$i],
                            'invbodega_id' => $request->invbodega_idTD[$i],
                            'cant' => $request->cant[$i] * $invmovtipo->tipomov,
                            'cantgrupo' => $request->cant[$i] * $invmovtipo->tipomov,
                            'cantxgrupo' => 1,
                            'peso' => $producto->peso,
                            'cantkg' => $request->totalkilos[$i] * $invmovtipo->tipomov,
                            'invmovtipo_id' => $request->invmovtipo_idTD[$i],
                            'invbodegaproducto_id' => $invbodegaproducto->id,
                            'sucursal_id' =>  $invbodegaproducto->invbodega->sucursal_id
                        ]
                    );
                }
            }
            return redirect('inventsal')->with([
                                                                'mensaje'=>'Registro Actualizado con exito.',
                                                                'tipo_alert' => 'alert-success'
                                                            ]);
        }else{
            return redirect('inventsal')->with([
                                                                'mensaje'=>'Registro no fue modificado. Registro fue Editado por otro usuario. Fecha Hora: '.$inventsal->updated_at,
                                                                'tipo_alert' => 'alert-error'
                                                            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request,$id)
    {
        can('eliminar-entrada-salida-inventario');
        //dd($request);
        if ($request->ajax()) {
            $inventsal = InvEntSal::findOrFail($id);
            if($request->updated_at == $inventsal->updated_at){
                if (InvEntSal::destroy($id)) {
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $inventsal = InvEntSal::withTrashed()->findOrFail($id);
                    $inventsal->usuariodel_id = auth()->id();
                    $inventsal->save();
                    //Eliminar detalle de cotizacion
                    InvEntSalDet::where('inventsal_id', $id)->update(['usuariodel_id' => auth()->id()]);
                    InvEntSalDet::where('inventsal_id', '=', $id)->delete();
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            }else{
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro no puede ser eliminado, fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
        } else {
            abort(404);
        }
    }


    public function enviaraprobarinventsal(Request $request)
    {
        if ($request->ajax()) {
            $inventsal = InvEntSal::findOrFail($request->id);
            if((($inventsal->staaprob == null) or ($inventsal->staaprob == 3)) and ($inventsal->staanul == null)){
                //VALIDAR SI EL REGISTRO YA FUE APROBADA O QUE FUE ELIMINADA O ANULADA
                $sql = "SELECT COUNT(*) AS cont
                    FROM inventsal
                    WHERE inventsal.id = $request->id
                    AND (isnull(inventsal.staaprob) or inventsal.staaprob = 3)
                    AND isnull(inventsal.staanul)
                    AND isnull(inventsal.deleted_at)";
                $cont = DB::select($sql);
                //if($inventsal->despachoords->count() == 0){
                if($cont[0]->cont == 1){
                    $aux_respuesta = InvBodegaProducto::validarExistenciaStock($inventsal->inventsaldets);
                    if($aux_respuesta["bandera"]){
                        $inventsal->staaprob = 1;
                        $inventsal->fechahoraaprob = date("Y-m-d H:i:s");
                        if($inventsal->save()){
                            return response()->json(['mensaje' => 'ok']);    
                        } else {
                            return response()->json(['mensaje' => 'ng']);
                        }
                    }else{
                        return response()->json([
                            'mensaje' => 'MensajePersonalizado',
                            'menper' => "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"]
                        ]);
                    }
                }else{
                    return response()->json([
                        'mensaje' => 'MensajePersonalizado',
                        'menper' => "Registro no fue procesado por alguna de las siguientes razones: Aprobado, Anulado o Eliminado previamente."
                    ]);
                }
            }else{
                return response()->json([
                    'mensaje' => 'MensajePersonalizado',
                    'menper' => "Registro no fue procesado por alguna de las siguientes razones: Aprobado o Anulado previamente."
                ]);
            }
        } else {
            abort(404);
        }
    }

    public function aprobinventsal(Request $request)
    {
        if ($request->ajax()) {
            $inventsal = InvEntSal::findOrFail($request->id);
            if(($inventsal->staaprob == 1) and ($inventsal->staanul == null)){
                //VALIDAR SI EL REGISTRO YA FUE APROBADA O QUE FUE ELIMINADA O ANULADA
                $sql = "SELECT COUNT(*) AS cont
                    FROM inventsal
                    WHERE inventsal.id = $request->id
                    AND inventsal.staaprob = 1
                    AND isnull(inventsal.staanul)
                    AND isnull(inventsal.deleted_at)";
                $cont = DB::select($sql);
                //if($inventsal->despachoords->count() == 0){
                if($cont[0]->cont == 1){
                    $aux_respuesta = InvBodegaProducto::validarExistenciaStock($inventsal->inventsaldets);
                    if($request->staaprob == 3){ //Cuando es rechazo no es necesario validar stock
                        //Se asigna true a la bandera para que haga el rechazo sin importar el stock
                        $aux_respuesta["bandera"] = true;
                    }
                    if($aux_respuesta["bandera"]){
                        if($inventsal->invmovtipo->stacieinimes == 1 and $request->staaprob == 2){
                            $mesAnterior = date("Ym",strtotime($inventsal->fechahora . "- 1 month"));
                            $invcontrolMesAnterior = InvControl::where('annomes','<=',$mesAnterior)
                                                    ->where('sucursal_id','=',$inventsal->sucursal_id);
                            if($invcontrolMesAnterior->count() > 0){
                                return response()->json([
                                    'resp' => 0,
                                    'tipmen' => 'error',
                                    'mensaje' => 'No puede hacer carga inicial. Debe cerrar mes anterior.'
                                ]);                
                            }
                        }
                        if($request->staaprob == 2){
                            $annomes = date("Ym", strtotime($inventsal->fechahora)); // date("Ym");
                            $inventsal->annomes = $annomes;
                            $array_inventsal = $inventsal->attributesToArray();
                            $array_inventsal['idmovmod'] = $array_inventsal['id'];
                            $invmov = InvMov::create($array_inventsal);
                            $inventsal->invmov_id = $invmov->id;
                        }
                        //$inventsal->staaprob = 2;
                        $inventsal->staaprob = $request->staaprob;
                        $inventsal->obsaprob = $request->obsaprob;
                        $inventsal->fechahoraaprob = date("Y-m-d H:i:s");
                        if($inventsal->save()){
                            if($request->staaprob == 2){
                                foreach ($inventsal->inventsaldets as $inventsaldet) {
                                    //$inventsaldet->save();
                                    $array_inventsaldet = $inventsaldet->attributesToArray();
                                    $array_inventsaldet["invmov_id"] = $invmov->id;
                                    $invmovdet = InvMovDet::create($array_inventsaldet);                                
                                }
                                if($inventsal->invmovtipo->stacieinimes == 1){
                                    $invcontrol = InvControl::where('annomes','=',date('Ym', strtotime($inventsal->fechahora)))
                                                            ->where('sucursal_id','=',$inventsal->sucursal_id);
                                    if($invcontrol->count() == 0){
                                        InvControl::create([
                                            'annomes' => date('Ym', strtotime($inventsal->fechahora)),
                                            'sucursal_id' => $inventsal->sucursal_id,
                                            'usuario_id' => auth()->id()
                                        ]);
                                    }
                                }
                            }
                            return response()->json([
                                'resp' => 1,
                                'tipmen' => 'success',
                                'mensaje' => 'Actualizado con exito.'
                            ]);
                        } else {
                            return response()->json([
                                'resp' => 0,
                                'tipmen' => 'error',
                                'mensaje' => 'Registro no fue actualizado.'
                            ]);
                        }
                    }else{
                        return response()->json([
                            'resp' => 0,
                            'tipmen' => 'error',
                            'mensaje' => "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"]
                        ]);
                    }
                }else{
                    return response()->json([
                        'resp' => 0,
                        'tipmen' => 'error',
                        'mensaje' => "Registro no fue procesado por alguna de las siguientes razones: Aprobado, Anulado o Eliminado previamente."
                    ]);
                }
            }else{
                return response()->json([
                    'resp' => 0,
                    'tipmen' => 'error',
                    'mensaje' => "Registro no fue procesado por alguna de las siguientes razones: Aprobado o Anulado previamente."
                ]);
            }
        } else {
            abort(404);
        }
    }

    public function exportPdf(Request $request)
    {
        if(can('ver-pdf-entrada-salida-inventario',false)){
            $datas = InvEntSal::findOrFail($request->id);

            $empresa = Empresa::orderBy('id')->get();
            $usuario = Usuario::findOrFail(auth()->id());
           
            if($datas){
                if(env('APP_DEBUG')){
                    return view('inventsal.listado', compact('datas','empresa','usuario','request'));
                }
                
                //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
                
                //$pdf = PDF::loadView('reportinvstock.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
                $pdf = PDF::loadView('inventsal.listado', compact('datas','empresa','usuario','request'));
                //return $pdf->download('cotizacion.pdf');
                //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
                return $pdf->stream("ReporteInvEntSal.pdf");
            }else{
                dd('Ningún dato disponible en esta consulta.');
            }
        }else{
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
    }

}
