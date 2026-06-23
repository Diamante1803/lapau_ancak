<?php
// app/Events/PenawaranBaru.php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PenawaranBaru implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int    $lelangId,
        public float  $hargaTertinggi,
        public string $hargaFormatted,
        public float  $minBerikutnya,
        public int    $jumlahPenawaran,
    ) {}

    public function broadcastOn(): array
    {
        // Public channel — siapa saja bisa dengar
        return [
            new Channel('lelang.' . $this->lelangId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'penawaran.baru';
    }
}