<?php

declare(strict_types=1);

namespace TPG\PayFast;

class PaymentMethod
{
    public const ALL = null;
    public const EFT = 'eft';
    public const CC = 'cc';
    public const DC = 'dc';
    public const MP = 'mp';
    public const MC = 'mc';
    public const SC = 'sc';
}
