<?php

namespace App\Observers;

use App\Models\InvMovDet;
use Illuminate\Support\Facades\DB;

class InvMovDetObserver
{
    /**
     * Handle the inv mov det "created" event.
     *
     * @param  \App\InvMovDet  $invmovdet
     * @return void
     */
    public function created(InvMovDet $invmovdet)
    {
        DB::transaction(function () use ($invmovdet) {
            DB::table('invbodegaproducto')
                ->where('id', $invmovdet->invbodegaproducto_id)
                ->update([
                    'stockprueba' => DB::raw('stockprueba + ' . (float) $invmovdet->cant),
                    'stockkgprueba' => DB::raw('stockkgprueba + ' . (float) $invmovdet->cantkg),
                ]);
        });
    }

    /**
     * Handle the inv mov det "updated" event.
     *
     * @param  \App\InvMovDet  $invmovdet
     * @return void
     */
    public function updated(InvMovDet $invmovdet)
    {
        // Solo si cambiaron las cantidades
        if ($invmovdet->wasChanged('cant') || $invmovdet->wasChanged('cantkg') || $invmovdet->wasChanged('invbodegaproducto_id')) {
            DB::transaction(function () use ($invmovdet) {
                $original = $invmovdet->getOriginal();
                
                // Si cambió el producto de bodega
                if ($invmovdet->wasChanged('invbodegaproducto_id')) {
                    // Restar del producto anterior
                    DB::table('invbodegaproducto')
                        ->where('id', $original['invbodegaproducto_id'])
                        ->update([
                            'stockprueba' => DB::raw('stockprueba - ' . (float) $original['cant']),
                            'stockkgprueba' => DB::raw('stockkgprueba - ' . (float) $original['cantkg']),
                        ]);
                    
                    // Sumar al nuevo producto
                    DB::table('invbodegaproducto')
                        ->where('id', $invmovdet->invbodegaproducto_id)
                        ->update([
                            'stockprueba' => DB::raw('stockprueba + ' . (float) $invmovdet->cant),
                            'stockkgprueba' => DB::raw('stockkgprueba + ' . (float) $invmovdet->cantkg),
                        ]);
                } else {
                    // Solo cambiaron las cantidades
                    $diferenciaCant = $invmovdet->cant - $original['cant'];
                    $diferenciaCantKg = $invmovdet->cantkg - $original['cantkg'];
                    
                    if ($diferenciaCant != 0 || $diferenciaCantKg != 0) {
                        DB::table('invbodegaproducto')
                            ->where('id', $invmovdet->invbodegaproducto_id)
                            ->update([
                                'stockprueba' => DB::raw('stockprueba + ' . (float) $diferenciaCant),
                                'stockkgprueba' => DB::raw('stockkgprueba + ' . (float) $diferenciaCantKg),
                            ]);
                    }
                }
            });
        }
    }

    /**
     * Handle the inv mov det "deleted" event.
     *
     * @param  \App\InvMovDet  $invmovdet
     * @return void
     */
    public function deleted(InvMovDet $invmovdet)
    {
        DB::transaction(function () use ($invmovdet) {
            DB::table('invbodegaproducto')
                ->where('id', $invmovdet->invbodegaproducto_id)
                ->update([
                    'stockprueba' => DB::raw('stockprueba - ' . (float) $invmovdet->cant),
                    'stockkgprueba' => DB::raw('stockkgprueba - ' . (float) $invmovdet->cantkg),
                ]);
        });
    }

    /**
     * Handle the inv mov det "restored" event.
     *
     * @param  \App\InvMovDet  $invmovdet
     * @return void
     */
    public function restored(InvMovDet $invmovdet)
    {
        //
    }

    /**
     * Handle the inv mov det "force deleted" event.
     *
     * @param  \App\InvMovDet  $invmovdet
     * @return void
     */
    public function forceDeleted(InvMovDet $invmovdet)
    {
        //
    }
}
