<?php

namespace App\Observers;

use App\Models\Cliente;
use App\Models\Empresa;

class ClienteObserver
{
    /**
     * Handle the cliente "created" event.
     *
     * @param  \App\Cliente  $cliente
     * @return void
     */
    public function created(Cliente $cliente)
    {
        $empresa = Empresa::findOrFail(1);
        if($empresa->actsiscob = 1){

        }
    }

    /**
     * Handle the cliente "updated" event.
     *
     * @param  \App\Cliente  $cliente
     * @return void
     */
    public function updated(Cliente $cliente)
    {
        $empresa = Empresa::findOrFail(1);
        if($empresa->actsiscob == 1){
            dd($cliente);
        }
    }

    /**
     * Handle the cliente "deleted" event.
     *
     * @param  \App\Cliente  $cliente
     * @return void
     */
    public function deleted(Cliente $cliente)
    {
        //
    }

    /**
     * Handle the cliente "restored" event.
     *
     * @param  \App\Cliente  $cliente
     * @return void
     */
    public function restored(Cliente $cliente)
    {
        //
    }

    /**
     * Handle the cliente "force deleted" event.
     *
     * @param  \App\Cliente  $cliente
     * @return void
     */
    public function forceDeleted(Cliente $cliente)
    {
        //
    }
}
