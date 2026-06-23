<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LelangStatusUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lelangId;
    public $status;

    public function __construct($lelangId, $status)
    {
        $this->lelangId = $lelangId;
        $this->status   = $status;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('lelang-updates'),
            new Channel('lelang.' . $this->lelangId),
        ];
    }

    public function broadcastAs()
    {
        return 'lelang.status.updated';
    }
}
