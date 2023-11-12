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
            //dd($detalle);
            foreach ($detalle->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto){
                $aux_stock = 0;
                //dd($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps);
                foreach ($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps as $invmovdet_bodsoldesp){
                    $aux_stock += $invmovdet_bodsoldesp->invmovdet["cant"];
                }
                //dd($aux_stock);
                //dd($detalle->despachoorddets);
                foreach($detalle->despachoorddets as $despachoorddet){
                    //dd($despachoorddet);
                    if($despachoorddet->id == 21365){
                        //dd($despachoorddet);
                    }
                    if($despachoorddet->despachoord->despachoordanul == null){
                        //dd($despachoorddet->despachoorddet_invbodegaproductos);
                        foreach($despachoorddet->despachoorddet_invbodegaproductos as $despachoorddet_invbodegaproducto){                        
                            //if($despachoorddet_invbodegaproducto->invbodegaproducto->invbodega->tipo == 1){
                                if($despachoorddet_invbodegaproducto->despachoorddet_id == 21365){
                                    //dd($despachoorddet_invbodegaproducto->invmovdet_bodorddesps);
                                }
    
                                foreach($despachoorddet_invbodegaproducto->invmovdet_bodorddesps as $invmovdet_bodorddesp){
                                    
                                    //dd($invmovdet_bodorddesp->invmovdet);
                                    if($invmovdet_bodorddesp->invmovdet->invbodegaproducto->invbodega->tipo == 1){
                                        if($invmovdet_bodorddesp->id == 15889){
                                            //dd($invmovdet_bodorddesp->invmovdet->cant);
                                            //dd($invmovdet_bodorddesp->invmovdet->invbodegaproducto->invbodega->tipo);
                                            //dd($invmovdet_bodorddesp);
                                        }
        
                                        //dd($invmovdet_bodorddesp->invmovdet);
                                        $aux_stock += $invmovdet_bodorddesp->invmovdet->cant;
                                        //dd($aux_stock1);
                                    }
    
                                }
                                //dd($aux_stock1);
                                /*
                                if(($despachoorddet_invbodegaproducto->cant * -1) > 0){
                                    $aux_stock -= $despachoorddet_invbodegaproducto->cant * -1;
                                }*/
                            //}
                        }
                        //dd($aux_stock1);
                    }
                    //dd($aux_stock);
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
                //dd($aux_stock);
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
                $arrayBodegasPicking[($invbodegaproductopicking[0]->id . "-". $detalle->id)] = [
                    "invbodegaproducto_id" => $invbodegaproductopicking[0]->id,
                    "producto_id" => $invbodegaproductopicking[0]->producto_id,
                    "invbodega_id" => $invbodegaproductopicking[0]->invbodega_id,
                    "sucursal_id" => $sucursal->id,
                    "stock" => $aux_stock
                ];
            }
        }
        //dd($arrayBodegasPicking);
        return $arrayBodegasPicking;
    }

}
