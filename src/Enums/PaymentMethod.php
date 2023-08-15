<?php

declare(strict_types=1);

namespace TPG\PayFast\Enums;

enum PaymentMethod: string
{
    case All = 'all';
    case EFT = 'eft';
    case CC = 'cc';
    case DC = 'dc';
    case MP = 'mp';
    case MC = 'mc';
    case SC = 'sc';
}
