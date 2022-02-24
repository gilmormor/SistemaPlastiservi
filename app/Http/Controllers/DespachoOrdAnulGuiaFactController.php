<?php

namespace App\Http\Controllers;

use App\Models\DespachoOrd;
use App\Models\DespachoOrdAnulGuiaFact;
use App\Models\InvBodegaProducto;
use App\Models\InvMov;
use App\Models\InvMovDet;
use Illuminate\Http\Request;

class DespachoOrdAnulGuiaFactController extends Controller
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

    public function guardaranularguia(Request $request)
    {
        if ($request->ajax()) {
            $despachoord = DespachoOrd::findOrFail($request->id);

            $aux_bandera = true;
            foreach ($despachoord->despachoorddets as $despachoorddet) {
                $aux_respuesta = InvBodegaProducto::validarExistenciaStock($despachoorddet->despachoorddet_invbodegaproductos,$request->invbodega_id);
                if($aux_respuesta["bandera"] == false){
                    $aux_bandera = $aux_respuesta["bandera"];
                    break;
                }
            }

            if($request->pantalla_origen == 1){

                if($aux_bandera){
                    $invmov_array = array();
                    $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                    $invmov_array["annomes"] = $aux_respuesta["annomes"];
                    $invmov_array["desc"] = "Salida de Bodega de Despacho / Orden Despacho Nro: " . $request->id;
                    $invmov_array["obs"] = "Salida de Bodega de Despacho por anular aprobación de Orden de despacho Nro: " . $request->id;
                    $invmov_array["invmovmodulo_id"] = 2;
                    $invmov_array["invmovtipo_id"] = 2;
                    $invmov_array["usuario_id"] = auth()->id();
                    $arrayinvmov_id = array();
                    
                    $invmov = InvMov::create($invmov_array);
                    array_push($arrayinvmov_id, $invmov->id);
                    foreach ($despachoord->despachoorddets as $despachoorddet) {
                        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                            $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                                ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $request->invbodega_id],
                                [
                                    'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                                    'invbodega_id' => $request->invbodega_id
                                ]
                            );

                            $array_invmovdet = $oddetbodprod->attributesToArray();
                            $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                            $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                            $array_invmovdet["invbodega_id"] = $request->invbodega_id;
                            $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                            $array_invmovdet["invmovtipo_id"] = 2;
                            $array_invmovdet["invmov_id"] = $invmov->id;
                            $invmovdet = InvMovDet::create($array_invmovdet);                              
                        }
                    }
                    $invmov_array = array();
                    $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                    $invmov_array["annomes"] = $aux_respuesta["annomes"];
                    $invmov_array["desc"] = "Entrada a Bodega Nro: " . $request->id;
                    $invmov_array["obs"] = "Entrada a Bodega por anular aprobacion de Orden de despacho Nro: " . $request->id;
                    $invmov_array["invmovmodulo_id"] = 2;
                    $invmov_array["invmovtipo_id"] = 1;
                    $invmov_array["usuario_id"] = auth()->id();
                    
                    $invmov = InvMov::create($invmov_array);
                    array_push($arrayinvmov_id, $invmov->id);
                    foreach ($despachoord->despachoorddets as $despachoorddet) {
                        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                            $array_invmovdet = $oddetbodprod->attributesToArray();
                            $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                            $array_invmovdet["invbodega_id"] = $oddetbodprod->invbodegaproducto->invbodega_id;
                            $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                            $array_invmovdet["invmovtipo_id"] = 1;
                            $array_invmovdet["cant"] = $array_invmovdet["cant"] * -1;
                            $array_invmovdet["invmov_id"] = $invmov->id;
                            $invmovdet = InvMovDet::create($array_invmovdet);                                
                        }
                    }
                }else{
                    return response()->json([
                        'mensaje' => 'MensajePersonalizado',
                        'menper' => "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"]
                    ]);
                }
                 
            }else{
                if($request->statusM == '1'){
                    $invmov_array = array();
                    $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                    $invmov_array["annomes"] = $aux_respuesta["annomes"];
                    $invmov_array["desc"] = "Entrada a Bodega Despacho por anulacion desde asignar Factura / Orden de despacho Nro:: " . $request->id;
                    $invmov_array["obs"] = "Entrada a Bodega Despacho por anulacion desde asignar Factura / Orden de despacho Nro: " . $request->id;
                    $invmov_array["invmovmodulo_id"] = 3;
                    $invmov_array["invmovtipo_id"] = 1;
                    $invmov_array["usuario_id"] = auth()->id();
                    
                    $invmov = InvMov::create($invmov_array);
                    foreach ($despachoord->despachoorddets as $despachoorddet) {
                        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                            $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                                ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $request->invbodega_id],
                                [
                                    'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                                    'invbodega_id' => $request->invbodega_id
                                ]
                            );
                            $array_invmovdet = $oddetbodprod->attributesToArray();
                            $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                            $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                            $array_invmovdet["invbodega_id"] = $oddetbodprod->invbodegaproducto->invbodega_id;
                            $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                            $array_invmovdet["invmovtipo_id"] = 1;
                            $array_invmovdet["cant"] = $array_invmovdet["cant"] * -1;
                            $array_invmovdet["invmov_id"] = $invmov->id;
                            $invmovdet = InvMovDet::create($array_invmovdet);                                
                        }
                    } 
                }else{
                    $invmov_array = array();
                    $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                    $invmov_array["annomes"] = $aux_respuesta["annomes"];
                    $invmov_array["desc"] = "Entrada a Bodega por anulacion desde asignar Factura / Orden de despacho Nro:: " . $request->id;
                    $invmov_array["obs"] = "Entrada a Bodega por anulacion desde asignar Factura / Orden de despacho Nro: " . $request->id;
                    $invmov_array["invmovmodulo_id"] = 3;
                    $invmov_array["invmovtipo_id"] = 1;
                    $invmov_array["usuario_id"] = auth()->id();
                    
                    $invmov = InvMov::create($invmov_array);
                    foreach ($despachoord->despachoorddets as $despachoorddet) {
                        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                            $array_invmovdet = $oddetbodprod->attributesToArray();
                            $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                            $array_invmovdet["invbodega_id"] = $oddetbodprod->invbodegaproducto->invbodega_id;
                            $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                            $array_invmovdet["invmovtipo_id"] = 1;
                            $array_invmovdet["cant"] = $array_invmovdet["cant"] * -1;
                            $array_invmovdet["invmov_id"] = $invmov->id;
                            $invmovdet = InvMovDet::create($array_invmovdet);                                
                        }
                    } 
                }
            }

            $despachoordanulguiafact = new DespachoOrdAnulGuiaFact();
            $despachoordanulguiafact->despachoord_id = $request->id;
            $despachoordanulguiafact->guiadespacho = $despachoord->guiadespacho;
            $despachoordanulguiafact->guiadespachofec = $despachoord->guiadespachofec;
            $despachoordanulguiafact->numfactura = $despachoord->numfactura;
            $despachoordanulguiafact->fechafactura = $despachoord->fechafactura;
            $despachoordanulguiafact->numfacturafec = $despachoord->numfacturafec;
            $despachoordanulguiafact->observacion = $request->observacion;
            $despachoordanulguiafact->usuario_id = auth()->id();
            $despachoordanulguiafact->status = $request->statusM;
            $despachoordanulguiafact->save();

            $despachoord->guiadespacho = NULL;
            $despachoord->guiadespachofec = NULL;
            if($request->statusM == '2'){ //Si status es = 1 solo borra la guia de despacho si es = 2 borra guia y factura
                $despachoord->guiadespacho = NULL;
                $despachoord->guiadespachofec = NULL;
                $despachoord->numfactura = NULL;
                $despachoord->fechafactura = NULL;
                $despachoord->numfacturafec = NULL;
                $despachoord->aprguiadesp = NULL;
            }
            if ($despachoord->save()) {
                return response()->json([
                                        'mensaje' => 'ok',
                                        'id' => $request->id,
                                        'nfila' => $request->nfila,
                                    ]);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }

        } else {
            abort(404);
        }
    }

}
