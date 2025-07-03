<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiceUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $diceId;
    public $diceTableId;
    public $rows;
    public $diceRowId;
    public $data;
    public $user;
    public $type;
    public $is_sr_act;
    public $srs;
    public $sri;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($rows, $diceId, $diceTableId, $data, $user, $diceRowId, $type, $is_sr_act = false, $srs = null,$sri = null)
    {
        $this->rows = $rows;
        $this->diceId = $diceId;
        $this->diceTableId = $diceTableId;
        $this->data = $data;
        $this->user = $user;
        $this->diceRowId = $diceRowId;
        $this->type = $type;
        $this->is_sr_act = $is_sr_act;
        $this->srs = $srs;
        $this->sri = $sri;

    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('dice.' . $this->diceId);
    }

    public function broadcastWith()
    {
        return [
            'rows' => $this->rows,
            'diceTableId' => $this->diceTableId,
            'diceRowId' => $this->diceRowId,
            'data' => $this->data,
            'user' => $this->user,
            'type' => $this->type,
            'is_sr_act' => $this->is_sr_act,
            'srs' => $this->srs,
            'sri' => $this->sri,
        ];
    }
}
