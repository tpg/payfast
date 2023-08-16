<?php

declare(strict_types=1);

namespace TPG\PayFast\Enums;

enum PaymentMethod: string
{
    case EFT = 'eft';
    case CreditCard = 'cc';
    case DebitCard = 'dc';
    case MasterPass = 'mp';
    case MobiCred = 'mc';
    case SCode = 'sc';
    case SnapScan = 'ss';
    case Zapper = 'zp';
    case MoreTyme = 'mt';
    case StoreCard = 'rcs';
}
