<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;

class ProductoCostoService
{
    public static function recalcular($producto_id)
    {

        $total = DB::table('productoinsumo as pi')
        ->join('insumo as i','i.id','=','pi.insumo_id')
        ->where('pi.producto_id',$producto_id)
        ->selectRaw('
            SUM(
                (pi.cant * i.costounitario)
                / pi.unidadesproducto
            ) total
        ')
        ->value('total');

        DB::table('producto')
        ->where('id',$producto_id)
        ->where('costobloqueado', '=', 1)
        ->update([
            'costototal'=>$total
            //'costo_actualizado_en'=>now()
        ]);
    }

}