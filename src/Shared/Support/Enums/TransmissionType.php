<?php

namespace Src\Shared\Support\Enums;

enum TransmissionType: string
{
    case MANUAL = 'manual';
    case AUTOMATIC = 'automatic';
    case ROBOT = 'robot';
    case CVT = 'cvt';
}
