<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'USER';
    case INSTITUTION = 'INSTITUTION';
    case ADMIN = 'ADMIN';
}
