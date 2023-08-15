<?php

namespace TPG\PayFast\Enums;

enum SubscriptionFrequency: int
{
    case Monthly = 3;
    case Quarterly = 4;
    case Biannually = 5;
    case Annually = 6;
}
