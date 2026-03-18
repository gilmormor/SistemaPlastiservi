<?php
namespace App\Services;
use App\Models\DocInsumoDet;
use App\Models\ProductoInsumo;
use Illuminate\Support\Facades\DB;

class DocInsumoDetService
{
    public static function syncFromDetalle($detalle, $tipo)
    {
        DB::transaction(function () use ($detalle, $tipo) {
            //dd($detalle);
            // 1️⃣ Borrar snapshot anterior
            DocInsumoDet::where('origentipo', $tipo)
                ->where('origendet_id', $detalle->id)
                ->delete();

            // 2️⃣ Buscar insumos estructurales
            $productoinsumos = ProductoInsumo::where('producto_id', $detalle->producto_id)->get();
            $cantidadDetalle = self::getCantidad($detalle);

            foreach ($productoinsumos as $productoinsumo) {



                DocInsumoDet::create([
                    'origentipo' => $tipo,
                    'origendet_id' => $detalle->id,

                    'insumo_id' => $productoinsumo->insumo_id,

                    'cant' => $cantidadDetalle,
                    //'cant' => $productoinsumo->cant,
                    'costounitario' => $productoinsumo->insumo->costounitario/$productoinsumo->unidadesproducto,

                    'usuario_id' => auth()->id()
                ]);
            }
        });
    }

    public static function deleteFromDetalle($detalle, $tipo)
    {
        DocInsumoDet::where('origentipo', $tipo)
            ->where('origendet_id', $detalle->id)
            ->delete();
    }

    private static function getCantidad($detalle)
    {
        if (isset($detalle->cant)) return $detalle->cant;
        if (isset($detalle->qtyitem)) return $detalle->qtyitem;

        return 0;
    }
}