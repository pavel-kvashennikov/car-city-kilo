<?php

namespace Src\Shared\Support\Enums;

enum SlotStatus: string
{
    case AVAILABLE = 'available';
    case BOOKED = 'booked';
    case BLOCKED = 'blocked';
}
