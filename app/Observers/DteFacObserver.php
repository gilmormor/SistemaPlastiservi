<?php

namespace App\Observers;

use App\Http\Controllers\SoapController;
use App\Models\Dte;
use App\Models\DteFac;
use App\Models\Empresa;

class DteFacObserver
{
    /**
     * Handle the dte fac "created" event.
     *
     * @param  \App\DteFac  $dteFac
     * @return void
     */
    public function created(DteFac $dteFac)
    {
        /* if($dteFac->dte->foliocontrol->tipodocto == 33 or $dteFac->dte->foliocontrol->tipodocto == 34){
            $empresa = Empresa::findOrFail(1);
            if($empresa->actsiscob == 1){
                //dd("entro en obser DteFac");
                $soap = new SoapController();
                $xmlcliente = Dte::xmlcliente($dteFac->dte);
                //dd($xmlcliente);
                $comando03creaclientes = $soap->Comando03CreaClientes($xmlcliente);
                //dd($comando03creaclientes);
                $xmlcobranza = Dte::xmlcobranza($dteFac->dte);
                //dd($xmlcobranza);
                $cargadocumentoscobranza = $soap->Comando01CargaDocumentos($xmlcobranza);
                //dd($cargadocumentoscobranza);
            }    
        } */
    }

    /**
     * Handle the dte fac "updated" event.
     *
     * @param  \App\DteFac  $dteFac
     * @return void
     */
    public function updated(DteFac $dteFac)
    {
        //
    }

    /**
     * Handle the dte fac "deleted" event.
     *
     * @param  \App\DteFac  $dteFac
     * @return void
     */
    public function deleted(DteFac $dteFac)
    {
        //
    }

    /**
     * Handle the dte fac "restored" event.
     *
     * @param  \App\DteFac  $dteFac
     * @return void
     */
    public function restored(DteFac $dteFac)
    {
        //
    }

    /**
     * Handle the dte fac "force deleted" event.
     *
     * @param  \App\DteFac  $dteFac
     * @return void
     */
    public function forceDeleted(DteFac $dteFac)
    {
        //
    }
}
