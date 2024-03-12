<?php

namespace App\Observers;

use App\Http\Controllers\SoapController;
use App\Models\Dte;
use App\Models\Empresa;

class DteObserver
{
    /**
     * Handle the dte "created" event.
     *
     * @param  \App\Dte  $dte
     * @return void
     */
    public function created(Dte $dte)
    {
/*         if($dte->foliocontrol->tipodocto == 33 or $dte->foliocontrol->tipodocto == 34){
            $empresa = Empresa::findOrFail(1);
            if($empresa->actsiscob == 1){
                $soap = new SoapController();
                $xmlcliente = Dte::xmlcliente($dte);
                $Comando03CreaClientes = $soap->Comando03CreaClientes($xmlcliente);
                $xmlcobranza = Dte::xmlcobranza($dte);
                $CargaDocumentosCobranza = $soap->Comando01CargaDocumentos($xmlcobranza);
            }    
        }
 */    }

    /**
     * Handle the dte "updated" event.
     *
     * @param  \App\Dte  $dte
     * @return void
     */
    public function updated(Dte $dte)
    {
        //
    }

    /**
     * Handle the dte "deleted" event.
     *
     * @param  \App\Dte  $dte
     * @return void
     */
    public function deleted(Dte $dte)
    {
        //
    }

    /**
     * Handle the dte "restored" event.
     *
     * @param  \App\Dte  $dte
     * @return void
     */
    public function restored(Dte $dte)
    {
        //
    }

    /**
     * Handle the dte "force deleted" event.
     *
     * @param  \App\Dte  $dte
     * @return void
     */
    public function forceDeleted(Dte $dte)
    {
        //
    }
}
