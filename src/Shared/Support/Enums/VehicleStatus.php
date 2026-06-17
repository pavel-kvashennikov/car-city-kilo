<?php

namespace Src\Shared\Support\Enums;

enum VehicleStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case SOLD = 'sold';
    case ARCHIVED = 'archived';
    case REJECTED = 'rejected';
}
