<?php

namespace App\Observers;
use App\Services\DocCompDetService;
use App\Services\DocInsumoDetService;

class CotizacionDetalleObserver
{
    public function created($detalle)
    {
        DocCompDetService::syncFromDetalle($detalle, 'COT');
        DocInsumoDetService::syncFromDetalle($detalle, 'COT');
    }

    public function updated($detalle)
    {
        if (
            $detalle->wasChanged('producto_id') ||
            $detalle->wasChanged('cant')
        ) {
            DocCompDetService::syncFromDetalle($detalle, 'COT');
            DocInsumoDetService::syncFromDetalle($detalle, 'COT');
        }
    }

    public function deleted($detalle)
    {
        DocCompDetService::deleteFromDetalle($detalle, 'COT');
        DocInsumoDetService::deleteFromDetalle($detalle, 'COT');
    }
}