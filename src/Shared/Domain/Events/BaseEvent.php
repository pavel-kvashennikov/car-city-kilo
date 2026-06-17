<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class BaseEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public readonly string $occurredAt;

    public function __construct()
    {
        $this->occurredAt = now()->toIso8601String();
    }
}
