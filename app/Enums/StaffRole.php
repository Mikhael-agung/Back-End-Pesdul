<?php

namespace App\Enums;

enum StaffRole: string
{
    case Kasir = 'kasir';
    case Dapur = 'dapur';
    case Admin = 'admin';
}