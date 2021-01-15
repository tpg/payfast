<?php

declare(strict_types=1);

namespace TPG\PayFast;

class PaymentMethod
{
    protected const ALL = null;
    protected const EFT = 'eft';
    protected const CC = 'cc';
    protected const DC = 'dc';
    protected const MP = 'mp';
    protected const MC = 'mc';
    protected const SC = 'sc';
}
