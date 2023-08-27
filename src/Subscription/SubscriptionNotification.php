<?php

declare(strict_types=1);

namespace TPG\PayFast\Subscription;

enum SubscriptionNotification: string
{
    case Email = 'email';
    case Webhook = 'webhook';
    case Both = 'both';
}
