<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarPesaje;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\InvBodega;
use App\Models\InvBodegaProducto;
use App\Models\InvControl;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\InvMovTipo;
use App\Models\Pesaje;
use App\Models\PesajeCarro;
use App\Models\PesajeDet;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\Turno;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class PesajeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-pesaje');
        return view('pesaje.index');
    }

    public function pesajepage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        return datatables()
            ->eloquent(Pesaje::query()
                        ->whereNull('staaprob')
                        ->orWhere('staaprob', 3)
                        ->whereIn('pesaje.sucursal_id', $sucurArray)
                    )
            ->toJson();
    }

    public function productobuscarpageid(Request $request){
        $datas = Producto::productosxClienteTemp($request);
        return datatables($datas)->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-pesaje');
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $tablas = array();
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        $tablas['turnos'] = Turno::orderBy('id')->get();
        return view('pesaje.crear',compact('tablas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarPesaje $request)
    {
        can('guardar-pesaje');
        //dd($request);
        $dateInput = explode('/',$request->fechahora);
        $request["fechahora"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0] . ' 06:00:00';
        $request["annomes"] = date("Ym", strtotime($request["fechahora"])); // date("Ym");
        $request->request->add(['invmovmodulo_id' => 7]);
        $request->request->add(['usuario_id' => auth()->id()]);
        $request->request->add(['invmovtipo_id' => 1]);
        $invbodega = InvBodega::where("sucursal_id","=",$request->sucursal_id)
                        ->where("tipo","=",6)
                        ->get();
        if(count($invbodega) == 0){
            return redirect('pesaje')->with('mensaje','Bodega no existe. Debe crear bodega de Pesaje.');
        }
        if(count($invbodega) > 1){
            return redirect('pesaje')->with('mensaje','Existen ' . count($invbodega) . ' bodegas de Pesaje. Solo debe existir 1');
        }
        $pesaje = Pesaje::create($request->all());
        $pesaje_id = $pesaje->id;
        $cont_producto = count($request->producto_id);
        if($cont_producto>0){
            for ($i=0; $i < $cont_producto ; $i++){
                if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false && is_null($request->pesobaltotal[$i])==false){
                    $producto = Producto::findOrFail($request->producto_id[$i]);
                    $invmovtipo = InvMovTipo::findOrFail(1);
                    $pesajecarro = PesajeCarro::findOrFail($request->pesajecarro_id[$i]);
                    $pesajedet = new PesajeDet();
                    $pesajedet->pesaje_id = $pesaje_id;
                    $pesajedet->producto_id = $request->producto_id[$i];
                    $pesajedet->invbodega_id = $invbodega[0]->id;
                    $pesajedet->cant = $request->cant[$i] * $invmovtipo->tipomov;
                    $pesajedet->cantgrupo = $request->cant[$i] * $invmovtipo->tipomov;
                    $pesajedet->cantxgrupo = 1;
                    $pesajedet->peso = $producto->peso;
                    $pesajedet->cantkg = ($request->cant[$i] * $producto->peso) * $invmovtipo->tipomov;
                    $pesajedet->unidadmedida_id = $producto->categoriaprod->unidadmedida_id;
                    $pesajedet->invmovtipo_id = $invmovtipo->id;
                    $pesajedet->turno_id = $request->turno_id[$i];
                    $pesajedet->pesajecarro_id = $request->pesajecarro_id[$i];
                    $pesajedet->areaproduccionsuclinea_id = $request->areaproduccionsuclinea_id[$i];
                    $pesajedet->pesounitnom = $producto->peso;
                    $pesajedet->tara = $pesajecarro->tara;
                    $pesajedet->pesobaltotal = $request->pesobaltotal[$i];
                    $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                        ['producto_id' => $pesajedet->producto_id,'invbodega_id' => $pesajedet->invbodega_id],
                        [
                            'producto_id' => $pesajedet->producto_id,
                            'invbodega_id' => $pesajedet->invbodega_id
                        ]
                    );
                    $pesajedet->invbodegaproducto_id = $invbodegaproducto->id;
                    $pesajedet->sucursal_id = $invbodegaproducto->invbodega->sucursal_id;
                    $pesajedet->save();
                }
            }
        }
        return redirect('pesaje')->with('mensaje','Registro creado con exito.');
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
        can('editar-pesaje');
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $data = Pesaje::findOrFail($id);
        $data->fechahora = $newDate = date("d/m/Y", strtotime($data->fechahora));
        $tablas = array();
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        $tablas['turnos'] = Turno::orderBy('id')->get();
        $tablas['pesajecarros'] = PesajeCarro::whereIn("sucursal_id",$sucurArray)
                                    ->where("activo",1)->get();
        return view('pesaje.editar', compact('data','tablas'));
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
        can('guardar-pesaje');
        //dd($request->all());
        $pesaje = Pesaje::findOrFail($id);
        if($pesaje->updated_at != $request->updated_at){
            return redirect('pesaje')->with([
                'mensaje'=>'Registro no fue modificado. Registro fue Editado por otro usuario. Fecha Hora: '.$pesaje->updated_at,
                'tipo_alert' => 'alert-error'
            ]);
        }
        $dateInput = explode('/',$request->fechahora);
        $request["fechahora"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0] . ' 06:00:00';
        $request["annomes"] = date("Ym", strtotime($request["fechahora"])); // date("Ym");
        $request->request->add(['invmovmodulo_id' => 7]);
        $request->request->add(['usuario_id' => auth()->id()]);
        $request->request->add(['invmovtipo_id' => 1]);
        $invbodega = InvBodega::where("sucursal_id","=",$request->sucursal_id)
                        ->where("tipo","=",6)
                        ->get();
        if(count($invbodega) == 0){
            return redirect('pesaje')->with('mensaje','Bodega no existe. Debe crear bodega de Pesaje.');
        }
        if(count($invbodega) > 1){
            return redirect('pesaje')->with('mensaje','Existen ' . count($invbodega) . ' bodegas de Pesaje. Solo debe existir 1');
        }

        $pesaje->updated_at = date("Y-m-d H:i:s");
        $pesaje->update($request->all());
        $auxpesajedet_id = PesajeDet::where('pesaje_id',$id)->whereNotIn('id', $request->pesajedet_id)->pluck('id')->toArray(); //->destroy();
        for ($i=0; $i < count($auxpesajedet_id) ; $i++){
            PesajeDet::destroy($auxpesajedet_id[$i]);
        }
        $cont_pesajedet = count($request->pesajedet_id);
        if($cont_pesajedet>0){
            for ($i=0; $i < count($request->pesajedet_id) ; $i++){
                $invmovtipo = InvMovTipo::findOrFail(1);
                $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                    ['producto_id' => $request->producto_id[$i],'invbodega_id' => $invbodega[0]->id],
                    [
                        'producto_id' => $request->producto_id[$i],
                        'invbodega_id' => $invbodega[0]->id
                    ]
                );
                //$pesajedet->invbodegaproducto_id = $invbodegaproducto->id;
                $producto = Producto::findOrFail($request->producto_id[$i]);
                $pesajecarro = PesajeCarro::findOrFail($request->pesajecarro_id[$i]);

                DB::table('pesajedet')->updateOrInsert(
                    ['id' => $request->pesajedet_id[$i], 'pesaje_id' => $id],
                    [
                        'producto_id' => $request->producto_id[$i],
                        'unidadmedida_id' => $producto->categoriaprod->unidadmedida_id,
                        'invbodega_id' => $invbodega[0]->id,
                        'cant' => $request->cant[$i] * $invmovtipo->tipomov,
                        'cantgrupo' => $request->cant[$i] * $invmovtipo->tipomov,
                        'cantxgrupo' => 1,
                        'peso' => $producto->peso,
                        'cantkg' => $request->totalkilos[$i] * $invmovtipo->tipomov,
                        'invmovtipo_id' => $invmovtipo->id,
                        'invbodegaproducto_id' => $invbodegaproducto->id,
                        'sucursal_id' =>  $invbodegaproducto->invbodega->sucursal_id,
                        'turno_id' => $request->turno_id[$i],
                        'pesajecarro_id' => $request->pesajecarro_id[$i],
                        'areaproduccionsuclinea_id' => $request->areaproduccionsuclinea_id[$i],
                        'pesounitnom' => $producto->peso,
                        'tara' => $pesajecarro->tara,
                        'pesobaltotal' => $request->pesobaltotal[$i]
                    ]
                );
            }
        }
        return redirect('pesaje')->with([
                                            'mensaje'=>'Registro Actualizado con exito.',
                                            'tipo_alert' => 'alert-success'
                                        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request,$id)
    {
        can('eliminar-pesaje');
        //dd($request);
        if ($request->ajax()) {
            $pesaje = Pesaje::findOrFail($id);
            if($request->updated_at != $pesaje->updated_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro no puede ser eliminado, fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);

            }
            if (Pesaje::destroy($id)) {
                //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                $pesaje = Pesaje::withTrashed()->findOrFail($id);
                $pesaje->usuariodel_id = auth()->id();
                $pesaje->save();
                //Eliminar detalle de cotizacion
                PesajeDet::where('pesaje_id', $id)->update(['usuariodel_id' => auth()->id()]);
                PesajeDet::where('pesaje_id', '=', $id)->delete();
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }


    public function enviaraprobarpesaje(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            $pesaje = Pesaje::findOrFail($request->id);
            if($pesaje->updated_at != $request->updated_at){
                return response()->json([
                    'mensaje' => 'MensajePersonalizado',
                    'menper' => "Registro no fue procesado, fue modificado por otro usuario ."
                ]);
            }
    
            if((($pesaje->staaprob == null) or ($pesaje->staaprob == 3)) and ($pesaje->staanul == null)){
                //VALIDAR SI EL REGISTRO YA FUE APROBADA O QUE FUE ELIMINADA O ANULADA
                $sql = "SELECT COUNT(*) AS cont
                    FROM pesaje
                    WHERE pesaje.id = $request->id
                    AND (isnull(pesaje.staaprob) or pesaje.staaprob = 3)
                    AND isnull(pesaje.staanul)
                    AND isnull(pesaje.deleted_at)";
                $cont = DB::select($sql);
                //if($pesaje->despachoords->count() == 0){
                if($cont[0]->cont == 1){
                    $pesaje->staaprob = 1;
                    $pesaje->fechahoraaprob = date("Y-m-d H:i:s");
                    if($pesaje->save()){
                        return response()->json(['mensaje' => 'ok']);    
                    } else {
                        return response()->json(['mensaje' => 'ng']);
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

    public function aprobpesaje(Request $request)
    {
        if ($request->ajax()) {
            $pesaje = Pesaje::findOrFail($request->id);
            if($pesaje->updated_at != $request->updated_at){
                return response()->json([
                    'resp' => 0,
                    'tipmen' => 'error',
                    'mensaje' => "Registro no fue procesado, fue modificado por otro usuario ."
                ]);
            }
            //dd($request->all());
            if(($pesaje->staaprob == 1) and ($pesaje->staanul == null)){
                //VALIDAR SI EL REGISTRO YA FUE APROBADA O QUE FUE ELIMINADA O ANULADA
                $sql = "SELECT COUNT(*) AS cont
                    FROM pesaje
                    WHERE pesaje.id = $request->id
                    AND pesaje.staaprob = 1
                    AND isnull(pesaje.staanul)
                    AND isnull(pesaje.deleted_at)";
                $cont = DB::select($sql);
                //if($pesaje->despachoords->count() == 0){
                if($cont[0]->cont == 1){
                    if($pesaje->invmovtipo->stacieinimes == 1 and $request->staaprob == 2){
                        $mesAnterior = date("Ym",strtotime($pesaje->fechahora . "- 1 month"));
                        $invcontrolMesAnterior = InvControl::where('annomes','<=',$mesAnterior)
                                                ->where('sucursal_id','=',$pesaje->sucursal_id);
                        if($invcontrolMesAnterior->count() > 0){
                            return response()->json([
                                'resp' => 0,
                                'tipmen' => 'error',
                                'mensaje' => 'No puede hacer carga inicial. Debe cerrar mes anterior.'
                            ]);                
                        }
                    }
                    if($request->staaprob == 2){
                        $annomes = date("Ym", strtotime($pesaje->fechahora)); // date("Ym");
                        $pesaje->annomes = $annomes;
                        $array_pesaje = $pesaje->attributesToArray();
                        $array_pesaje['idmovmod'] = $array_pesaje['id'];
                        $invmov = InvMov::create($array_pesaje);
                        $pesaje->invmov_id = $invmov->id;
                    }
                    //$pesaje->staaprob = 2;
                    $pesaje->staaprob = $request->staaprob;
                    $pesaje->obsaprob = $request->obsaprob;
                    $pesaje->fechahoraaprob = date("Y-m-d H:i:s");
                    if($pesaje->save()){
                        if($request->staaprob == 2){
                            foreach ($pesaje->pesajedets as $pesajedet) {
                                //$pesajedet->save();
                                $array_pesajedet = $pesajedet->attributesToArray();
                                $array_pesajedet["invmov_id"] = $invmov->id;
                                $invmovdet = InvMovDet::create($array_pesajedet);                                
                            }
                            if($pesaje->invmovtipo->stacieinimes == 1){
                                $invcontrol = InvControl::where('annomes','=',date('Ym', strtotime($pesaje->fechahora)))
                                                        ->where('sucursal_id','=',$pesaje->sucursal_id);
                                if($invcontrol->count() == 0){
                                    InvControl::create([
                                        'annomes' => date('Ym', strtotime($pesaje->fechahora)),
                                        'sucursal_id' => $pesaje->sucursal_id,
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
        if(can('ver-pdf-pesaje',false)){
            $datas = Pesaje::findOrFail($request->id);

            $empresa = Empresa::orderBy('id')->get();
            $usuario = Usuario::findOrFail(auth()->id());
           
            if($datas){
                if(env('APP_DEBUG')){
                    return view('pesaje.listado', compact('datas','empresa','usuario','request'));
                }
                
                //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
                
                //$pdf = PDF::loadView('reportinvstock.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
                $pdf = PDF::loadView('pesaje.listado', compact('datas','empresa','usuario','request'));
                //return $pdf->download('cotizacion.pdf');
                //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
                return $pdf->stream("ReportePesaje.pdf");
            }else{
                dd('Ningún dato disponible en esta consulta.');
            }
        }else{
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
    }

}