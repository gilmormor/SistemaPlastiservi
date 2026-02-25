<?php
namespace App\Services;
use App\Models\DocCompDet;
use App\Models\Producto;
use App\Models\ProductoComp;
use Illuminate\Support\Facades\DB;

class DocCompDetService
{
    public static function syncFromDetalle($detalle, $tipo)
    {
        DB::transaction(function () use ($detalle, $tipo) {

            // 1️⃣ Borrar snapshot anterior
            DocCompDet::where('origentipo', $tipo)
                ->where('origendet_id', $detalle->id)
                ->delete();

            // 2️⃣ Buscar componentes estructurales
            $componentes = ProductoComp::where('producto_id', $detalle->producto_id)->get();
            $cantidadDetalle = self::getCantidad($detalle);

            foreach ($componentes as $componente) {

                $productoComp = Producto::find($componente->productocomp_id);

                if (!$productoComp) continue;

                DocCompDet::create([
                    'origentipo' => $tipo,
                    'origendet_id' => $detalle->id,

                    'productocomp_id' => $componente->id,
                    'productopadre_id' => $detalle->producto_id,
                    'producto_id' => $componente->productocomp_id,

                    'cant' => $cantidadDetalle * $componente->cant,
                    'cantxcomp' => $componente->cant,
                    'precio' => $productoComp->precioneto,

                    'usuario_id' => auth()->id()
                ]);
            }
        });
    }

    public static function deleteFromDetalle($detalle, $tipo)
    {
        DocCompDet::where('origentipo', $tipo)
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