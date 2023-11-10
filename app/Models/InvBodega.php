<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvBodega extends Model
{
    use SoftDeletes;
    protected $table = "invbodega";
    protected $fillable = [
        'nombre',
        'nomabre',
        'desc',
        'activo',
        'tipo',
        'orden',
        'sucursal_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A VARIOS invbodegaproducto
    public function invbodegaproductos()
    {
        return $this->hasMany(InvBodegaProducto::class);
    }

    //RELACION INVERSA Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    
    public function categoriaprods()
    {
        return $this->belongsToMany(CategoriaProd::class, 'categoriaprod_invbodega','invbodega_id','categoriaprod_id')->withTimestamps();
    }

    public static function llenarArrayBodegasPickingSolDesp($detalles){
        $arrayBodegasPicking = [];
        foreach ($detalles as $detalle) {
            foreach ($detalle->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto){
                $aux_stock = 0;
                foreach ($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps as $invmovdet_bodsoldesp){
                    $aux_stock += $invmovdet_bodsoldesp->invmovdet["cant"];
                }
                foreach($detalle->despachoorddets as $despachoorddet){
                    //dd($despachoorddet);
    
                    if($despachoorddet->despachoord->despachoordanul == null){
                        foreach($despachoorddet->despachoorddet_invbodegaproductos as $despachoorddet_invbodegaproducto){
                            foreach($despachoorddet_invbodegaproducto->invmovdet_bodorddesps as $invmovdet_bodorddesp){
                                if($invmovdet_bodorddesp->invmovdet->invbodegaproducto->invbodega->tipo == 1){
                                    $aux_stock += $invmovdet_bodorddesp->invmovdet->cant;
                                }
                            }
                            /*
                            if($despachoorddet_invbodegaproducto->invbodegaproducto->invbodega->tipo == 1){
                                if(($despachoorddet_invbodegaproducto->cant *-1) > 0){
                                    $aux_stock -= $despachoorddet_invbodegaproducto->cant *-1;
                                }
                            }
                            */
                        }
                    }
    
    /*
                    if($despachoorddet->despachoord->despachoordanul == null and $despachoorddet->despachoord->aprguiadesp != 1){
                        foreach($despachoorddet->despachoorddet_invbodegaproductos as $despachoorddet_invbodegaproducto){
                            if($despachosoldet_invbodegaproducto->invbodegaproducto_id == $despachoorddet_invbodegaproducto->invbodegaproducto_id){
                                $aux_stock -= $despachoorddet_invbodegaproducto->cant *-1;
                            }
    
                            //$aux_stock -= $despachoorddet_invbodegaproducto->cant *-1;
                        }
                    }*/
                }
                $sucursal = $despachosoldet_invbodegaproducto->invbodegaproducto->invbodega->sucursal;
                $producto = $despachosoldet_invbodegaproducto->invbodegaproducto->producto;
                $invbodegaproducto = $despachosoldet_invbodegaproducto->invbodegaproducto;
                $invbodega = InvBodega::where("sucursal_id","=",$sucursal->id)
                            ->where("tipo","=",1)
                            ->whereNull('deleted_at')
                            ->get();
                if(count($invbodega) == 0){
                    return redirect('despachoord/listarsoldesp')->with([
                        'mensaje' => 'Sucursal ' . $sucursal->nombre . ", no tiene bodega picking. Debe crear una.",
                        'tipo_alert' => 'alert-error'
                    ]);    
                }
                if(count($invbodega) > 1){
                    return redirect('despachoord/listarsoldesp')->with([
                        'mensaje'=> "Sucursal " . $sucursal->nombre . ", tiene " . strval(count($invbodega)) . " bodegas de picking, solo debe tener 1.",
                        'tipo_alert' => 'alert-error'
                    ]);
                }
                $invbodegaproductopicking = InvBodegaProducto::where("producto_id","=",$producto->id)
                                    ->where("invbodega_id","=",$invbodega[0]->id)
                                    ->whereNull('deleted_at')
                                    ->get();
                if(count($invbodegaproductopicking) == 0){
                    return redirect('despachoord/listarsoldesp')->with([
                        'mensaje'=> "Falta crear item o registro en tabla invbodegaproducto. Producto: " . $producto->id . " " . $producto->nombre . " Bodega: " . $invbodega[0]->nombre . " Sucursal: " . $sucursal->nombre,
                        'tipo_alert' => 'alert-error'
                    ]);
                }
                if(count($invbodegaproductopicking) > 1){
                    return redirect('despachoord/listarsoldesp')->with([
                        'mensaje'=> "Se debe eliminar 1 registro. Existen " . strval(count($invbodegaproductopicking)) . " registros en tabla invbodegaproducto. Solo debe existir 1. Producto: " . $producto->id . " " . $producto->nombre . " Bodega: " . $invbodega[0]->nombre . " Sucursal: " . $sucursal->nombre,
                        'tipo_alert' => 'alert-error'
                    ]);
                }
                $arrayBodegasPicking[$invbodegaproductopicking[0]->id] = [
                    "despachosoldet_invbodegaproducto_id" => $despachosoldet_invbodegaproducto->id,
                    "invbodegaproducto_idOrig" => $despachosoldet_invbodegaproducto->invbodegaproducto_id,
                    "invbodegaproducto_id" => $invbodegaproductopicking[0]->id,
                    "producto_id" => $invbodegaproductopicking[0]->producto_id,
                    "invbodega_id" => $invbodegaproductopicking[0]->invbodega_id,
                    "sucursal_id" => $sucursal->id,
                    "stock" => $aux_stock
                ];
            }
        }
        return $arrayBodegasPicking;
    }

}
