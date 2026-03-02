<?php

namespace App\Observers;
use App\Services\DocCompDetService;
use App\Services\DocInsumoDetService;

class NotaVentaDetalleObserver
{
    public function created($detalle)
    {
        //DocCompDetService::syncFromDetalle($detalle, 'NV');
        DocInsumoDetService::syncFromDetalle($detalle, 'NV');
    }

    public function updated($detalle)
    {
        if (
            $detalle->wasChanged('producto_id') ||
            $detalle->wasChanged('cant')
        ) {
            //DocCompDetService::syncFromDetalle($detalle, 'NV');
            DocInsumoDetService::syncFromDetalle($detalle, 'NV');
        }
    }

    public function deleted($detalle)
    {
        //DocCompDetService::deleteFromDetalle($detalle, 'NV');
        DocInsumoDetService::deleteFromDetalle($detalle, 'NV');
    }
}