<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarInvEntSal;
use App\Models\Cliente;
use App\Models\InvBodega;
use App\Models\InvBodegaProducto;
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
        return datatables()
            ->eloquent(InvEntSal::query()->whereNull('staaprob'))
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

        return view('inventsal.crear',compact('invmovtipos','productos','tablas'));
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
        $hoy = date("Y-m-d H:i:s");
        $request->request->add(['fechahora' => $hoy]);
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
                    $invmovtipo = InvMovTipo::findOrFail($request->invmovtipo_idTD[$i]);
                    $inventsaldet = new InvEntSalDet();
                    $inventsaldet->inventsal_id = $inventsal_id;
                    $inventsaldet->producto_id = $request->producto_id[$i];
                    $inventsaldet->cant = $request->cant[$i] * $invmovtipo->tipomov;
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
        //dd($data->inventsaldets);
        $invmovtipos = InvMovTipo::orderBy('id')->get();
        $productos = Producto::productosxUsuario();
        $clientesArray = Cliente::clientesxUsuario();
        $sucurArray = $clientesArray['sucurArray'];
        $tablas = array();
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        return view('inventsal.editar', compact('data','invmovtipos','productos','tablas'));
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


                    DB::table('inventsaldet')->updateOrInsert(
                        ['id' => $request->NVdet_id[$i], 'inventsal_id' => $id],
                        [
                            'producto_id' => $request->producto_id[$i],
                            'unidadmedida_id' => $request->unidadmedida_id[$i],
                            'invbodega_id' => $request->invbodega_idTD[$i],
                            'cant' => $request->cant[$i] * $invmovtipo->tipomov,
                            'cantkg' => $request->totalkilos[$i] * $invmovtipo->tipomov,
                            'invmovtipo_id' => $request->invmovtipo_idTD[$i],
                            'invbodegaproducto_id' => $invbodegaproducto->id
                        ]
                    );
                }
            }
            return redirect('inventsal/'.$id.'/editar')->with([
                                                                'mensaje'=>'Registro Actualizado con exito.',
                                                                'tipo_alert' => 'alert-success'
                                                            ]);
        }else{
            return redirect('inventsal/'.$id.'/editar')->with([
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
    public function destroy($id)
    {
        //
    }

    public function aprobinventsal(Request $request)
    {
        if ($request->ajax()) {
            $inventsal = InvEntSal::findOrFail($request->id);
            if(($inventsal->staaprob == null) and ($inventsal->staanul == null)){
                //VALIDAR SI EL REGISTRO YA FUE APROBADA O QUE FUE ELIMINADA O ANULADA
                $sql = "SELECT COUNT(*) AS cont
                    FROM inventsal
                    WHERE inventsal.id = $request->id
                    AND isnull(inventsal.staaprob)
                    AND isnull(inventsal.staanul)
                    AND isnull(inventsal.deleted_at)";
                $cont = DB::select($sql);
                //if($inventsal->despachoords->count() == 0){
                if($cont[0]->cont == 1){
                    $aux_respuesta = InvBodegaProducto::validarExistenciaStock($inventsal->inventsaldets);
                    if($aux_respuesta["bandera"]){
                        $annomes = date("Ym");
                        $inventsal->annomes = $annomes;
                        $array_inventsal = $inventsal->attributesToArray();
                        $invmov = InvMov::create($array_inventsal);
                        $inventsal->staaprob = 1;
                        $inventsal->fechahoraaprob = date("Y-m-d H:i:s");
                        $inventsal->invmov_id = $invmov->id;
                        if($inventsal->save()){
                            foreach ($inventsal->inventsaldets as $inventsaldet) {
                                //$inventsaldet->save();
                                $array_inventsaldet = $inventsaldet->attributesToArray();
                                $array_inventsaldet["invmov_id"] = $invmov->id;
                                $invmovdet = InvMovDet::create($array_inventsaldet);                                
                            }
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


}
