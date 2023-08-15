<?php

namespace TPG\PayFast\Enums;

enum SubscriptionStatus: int
{
    case Active = 1;
    case Cancelled = 2;
    case Paused = 3;
}
