<?php

namespace App\Enums;

enum InstitutionRequestStatus: string
{
    case NONE = 'NONE';
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
}
