<?php

declare(strict_types=1);

namespace TPG\PHPayfast\Enums;

enum SubscriptionType: int
{
    case Subscription = 1;
    case Tokenization = 2;
}
