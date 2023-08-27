<?php

declare(strict_types=1);

namespace TPG\PHPayfast\Subscription;

use TPG\PHPayfast\Attributes;
use TPG\PHPayfast\Enums\SubscriptionFrequency;
use TPG\PHPayfast\Enums\SubscriptionType;
use TPG\PHPayfast\Money;

readonly class Subscription
{
    public function __construct(
        public SubscriptionType $type = SubscriptionType::Subscription,
        public SubscriptionFrequency $frequency = SubscriptionFrequency::Monthly,
        public int $cycles = 0,
        public ?\DateTime $billingDate = null,
        public ?int $amount = null,
        public ?bool $notifyBuyer = null,
    ) {
    }

    public function toArray(): array
    {
        return (new Attributes())->prep([
            'subscription_type' => $this->type,
            'billing_date' => $this->billingDate->format('Y-m-d'),
            'recurring_amount' => $this->amount
                ? (new Money($this->amount))->format()
                : null,
            'frequency' => $this->frequency,
            'cycles' => $this->cycles,
        ]);
    }
}
