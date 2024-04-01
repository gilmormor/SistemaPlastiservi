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
    public $origenError;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($dte,$xml,$Carga_TXTDTE,$origenError)
    {
        $this->dte = $dte;
        $this->xml = $xml;
        $this->Carga_TXTDTE = $Carga_TXTDTE;
        $this->origenError = $origenError;
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
