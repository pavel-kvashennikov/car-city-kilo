<?php

declare(strict_types=1);

namespace Src\MarketMap\Domain\Models;

enum LocationType: string
{
    case PAVILION = 'pavilion';
    case BOX = 'box';
    case OUTDOOR = 'outdoor_spot';
}
