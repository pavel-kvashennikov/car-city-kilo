<?php

namespace Src\Shared\Support\Enums;

enum CompanyStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case REJECTED = 'rejected';
}
