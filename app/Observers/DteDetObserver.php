<?php

namespace App\Observers;
use App\Services\DocCompDetService;
use App\Services\DocInsumoDetService;

class DteDetObserver
{
    public function created($detalle)
    {
        DocCompDetService::syncFromDetalle($detalle, 'DTE');
        DocInsumoDetService::syncFromDetalle($detalle, 'DTE');
    }

    public function updated($detalle)
    {
        if (
            $detalle->wasChanged('producto_id') ||
            $detalle->wasChanged('qtyitem')
        ) {
            DocCompDetService::syncFromDetalle($detalle, 'DTE');
            DocInsumoDetService::syncFromDetalle($detalle, 'DTE');
        }
    }

    public function deleted($detalle)
    {
        DocCompDetService::deleteFromDetalle($detalle, 'DTE');
        DocInsumoDetService::deleteFromDetalle($detalle, 'DTE');
    }
}