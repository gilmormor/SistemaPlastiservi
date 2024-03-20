<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ErrorCrearDTE
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $dte;
    public $xml;
    public $Carga_TXTDTE;
    public $aux_folio;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($dte,$xml,$Carga_TXTDTE,$aux_folio)
    {
        $this->dte = $dte;
        $this->xml = $xml;
        $this->Carga_TXTDTE = $Carga_TXTDTE;
        $this->aux_folio = $aux_folio;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
