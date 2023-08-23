<?php

namespace TPG\PHPayfast\Enums;

enum SubscriptionFrequency: int
{
    case Monthly = 3;
    case Quarterly = 4;
    case Biannually = 5;
    case Annually = 6;
}
