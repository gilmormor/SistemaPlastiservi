<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class InvBodegaProducto extends Model
{
    use SoftDeletes;
    protected $table = "invbodegaproducto";
    protected $fillable = [
        'producto_id',
        'invbodega_id'
    ];

    //RELACION INVERSA Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    //RELACION INVERSA InvBodega
    public function invbodega()
    {
        return $this->belongsTo(InvBodega::class);
    }
    
    public static function existencia($request){
        //$request["tipo"] es el valor que define que tipo de bodega se consulta, 1=Bodega normal de almacenaje. 2=Bodega de despago, de esta bodega sale el producto para la calle a traves de la Guia de despacho
        $annomes = date("Ym");
        if($request["invbodegaproducto_id"]){
            $existencia = InvMov::selectRaw('SUM(cant) as cant')
                            ->join("invmovdet","invmov.id","=","invmovdet.invmov_id")
                            ->where("annomes","=",$annomes)
                            ->where("invmovdet.invbodegaproducto_id","=",$request["invbodegaproducto_id"])
                            ->join('invbodega', 'invmovdet.invbodega_id', '=', 'invbodega.id')
                            ->whereNull('staanul')
                            ->whereNull('invmovdet.deleted_at')
                            ->groupBy("invmovdet.invbodegaproducto_id")
                            ->get();
        }else{
            //dd($request["invbodega_id"]);
            $existencia = InvMov::selectRaw('SUM(cant) as cant')
                            ->join("invmovdet","invmov.id","=","invmovdet.invmov_id")
                            ->where("annomes","=",$annomes)
                            ->where("invmovdet.producto_id","=",$request["producto_id"])
                            ->where("invmovdet.invbodega_id","=",$request["invbodega_id"])
                            ->join('invbodega', 'invmovdet.invbodega_id', '=', 'invbodega.id')
                            ->whereNull('staanul')
                            ->whereNull('invmovdet.deleted_at')
                            ->groupBy("invmovdet.invbodegaproducto_id")
                            ->get();
        }
        $array_stock = $existencia->toArray();
        //dd($array_stock);
        $respuesta = array();
        if(count($array_stock) > 0){
            $respuesta['stock'] = $array_stock[0];
        }else{
            $respuesta['stock']['cant'] = 0;
        }
        $respuesta['cont'] = count($array_stock);
        return $respuesta;
    }

    public static function validarExistenciaStock($inventsaldets,$codBodegaDespacho = false){
        $respuesta = array();
        $respuesta["bandera"] = true;
        $aux_ban = true;
        foreach ($inventsaldets as $inventsaldet) {
            //Se crea variable de request
            //$invbodegaproducto = new InvBodegaProductoController();
            $annomes = date("Ym");
            $respuesta["annomes"] = $annomes;
            $request = new Request();
            if(isset($inventsaldet->invbodegaproducto_id)){
                if($codBodegaDespacho == false){
                    $request["invbodegaproducto_id"] = $inventsaldet->invbodegaproducto_id;
                }else{
                    $invbodegaproducto = InvBodegaProducto::findOrFail($inventsaldet->invbodegaproducto_id);
                    $invbodegaproducto = InvBodegaProducto::where("producto_id","=",$invbodegaproducto->producto_id)
                                        ->where("invbodega_id","=",$codBodegaDespacho)
                                        ->get();
                    $request["invbodegaproducto_id"] = $invbodegaproducto[0]->id;
                }
            }else{
                $request["producto_id"] = $inventsaldet->producto_id;
                $request["invbodega_id"] = $inventsaldet->invbodega_id;    
            }
            $request["cant"] = $inventsaldet->cant;
            $request["tipo"] = 2;
            $request["annomes"] = $annomes;
            //$invbodegaproductoexistencia = $invbodegaproducto->consexistencia($request);
            $invbodegaproductoexistencia = InvBodegaProducto::existencia($request);
            //dd($invbodegaproductoexistencia);
            $saldoexistencia =  $invbodegaproductoexistencia["stock"]["cant"] + $inventsaldet->cant; 
            if($saldoexistencia < 0){
                $respuesta["bandera"] = false;
                $respuesta["producto_id"] = $inventsaldet->invbodegaproducto->producto_id;
                $respuesta["producto_nombre"] = "";
                if(isset($inventsaldet->producto->nombre)){
                    $respuesta["producto_nombre"] = $inventsaldet->producto->nombre;
                }
                if(isset($inventsaldet->invbodegaproducto->producto->nombre)){
                    $respuesta["producto_nombre"] = $inventsaldet->invbodegaproducto->producto->nombre;
                }
                $respuesta["stock"] = $invbodegaproductoexistencia["stock"]["cant"];
                break;
            }
        }
        return $respuesta;
    }

    public static function crearBodegasPorCategoria($invbodega){
        foreach ($invbodega->categoriaprods as $categoriaprod) {
            foreach ($categoriaprod->productos as $producto) {
                if($producto->id == 141){
                    //dd("bodega_id: " . $invbodega->id . " Producto_id: " . $producto->id);
                }
                //dd($producto->id . " : " .  $invbodega->id);
                $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                    ['producto_id' => $producto->id,'invbodega_id' => $invbodega->id],
                    [
                        'producto_id' => $producto->id,
                        'invbodega_id' => $invbodega->id
                    ]
                );
            }
        }
    }

    public static function existenciaxSolDespOrdDesp($despachosolorddets){
        foreach ($despachosolorddets as $despachoSolOrddet) { //RECIBO EL OBJETO DE SOLICITUD U ORDEN DE DESPACHO
            if($despachoSolOrddet->despachosoldet_invbodegaproductos){ //SI ES SOLICITUD DE DESPACHO ASIGNO EL DETALLE DE LAS BODEGAS INVOLUCRADAS EN CADA ITEN DEL DETALLE DE LA SOLICITUD DE DESPACHO
                $despachoSolOrddet_invbodegaproductos = $despachoSolOrddet->despachosoldet_invbodegaproductos;
            }else{ //SI NO ES ORDEN DE DESPACHO ENTONCES ASIGNO EL DETALLE DE LAS BODEGAS INVOLUCRADAS EN CADA ITEN DEL DETALLE DE LA ORDEN DE DESPACHO
                $despachoSolOrddet_invbodegaproductos = $despachoSolOrddet->despachoorddet_invbodegaproductos;
            }
            foreach ($despachoSolOrddet_invbodegaproductos as $oddetbodprod) {
                $arrayStock = InvBodegaProducto::existencia([
                    "invbodegaproducto_id" => $oddetbodprod->invbodegaproducto_id
                ]);
                $saldoStock = $arrayStock["stock"]["cant"] + $oddetbodprod->cant;
                if($saldoStock < 0){
                    return [
                        'status' => "0",
                        'title' => "Bodega sin stock!",
                        'mensaje' => "Bodega: " . $oddetbodprod->invbodegaproducto->invbodega->nombre . ".\nSucursal: " . $oddetbodprod->invbodegaproducto->invbodega->sucursal->nombre . ".\nIdProd: " . $oddetbodprod->invbodegaproducto->producto_id . "\nNombre: " . $oddetbodprod->invbodegaproducto->producto->nombre. "\nCantidad movimiento: " . $oddetbodprod->cant . "\nStock actual: " . $arrayStock["stock"]["cant"],
                        'tipo_alert' => 'error'
                    ];
                }
            }
        }
        return [
            'status' => "1",
        ];

    }
}
