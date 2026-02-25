<?php

namespace App\Observers;
use App\Services\DocCompDetService;

class CotizacionDetalleObserver
{
    public function created($detalle)
    {
        DocCompDetService::syncFromDetalle($detalle, 'COT');
    }

    public function updated($detalle)
    {
        if (
            $detalle->wasChanged('producto_id') ||
            $detalle->wasChanged('cant')
        ) {
            DocCompDetService::syncFromDetalle($detalle, 'COT');
        }
    }

    public function deleted($detalle)
    {
        DocCompDetService::deleteFromDetalle($detalle, 'COT');
    }
}