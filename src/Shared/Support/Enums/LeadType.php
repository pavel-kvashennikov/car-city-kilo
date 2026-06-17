<?php

namespace Src\Shared\Support\Enums;

enum LeadType: string
{
    case TEST_DRIVE = 'test_drive';
    case CREDIT = 'credit';
    case TRADE_IN = 'trade_in';
    case CALLBACK = 'callback';
}
