<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class XMLCargaDocManager
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $dte;
    public $xml;
    public $xmlcli;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($dte,$xml,$xmlcli)
    {
        $this->dte = $dte;
        $this->xml = $xml;
        $this->xmlcli = $xmlcli;
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
