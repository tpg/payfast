<?php

declare(strict_types=1);

namespace TPG\PayFast\Enums;

enum PaymentStatus: string
{
    case Complete = 'COMPLETE';
    case Cancelled = 'CANCELLED';
}
