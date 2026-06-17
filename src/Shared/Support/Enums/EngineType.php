<?php

namespace Src\Shared\Support\Enums;

enum EngineType: string
{
    case PETROL = 'petrol';
    case DIESEL = 'diesel';
    case HYBRID = 'hybrid';
    case ELECTRIC = 'electric';
    case GAS = 'gas';
}
