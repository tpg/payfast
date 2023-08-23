<?php

declare(strict_types=1);

namespace TPG\PHPayfast\Enums;

enum PaymentStatus: string
{
    case Complete = 'COMPLETE';
    case Cancelled = 'CANCELLED';
}
