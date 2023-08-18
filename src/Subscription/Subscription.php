<?php

declare(strict_types=1);

namespace TPG\PayFast\Subscription;

use TPG\PayFast\Attributes;
use TPG\PayFast\Enums\SubscriptionFrequency;
use TPG\PayFast\Enums\SubscriptionType;
use TPG\PayFast\Money;

readonly class Subscription
{
    public function __construct(
        public SubscriptionType $type = SubscriptionType::Subscription,
        public SubscriptionFrequency $frequency = SubscriptionFrequency::Monthly,
        public int $cycles = 0,
        public ?\DateTime $billingDate = null,
        public ?int $recurringAmount = null,
        public ?bool $notifyEmail = null,
        public ?bool $notifyWebhook = null,
        public ?bool $notifyBuyer = null,
    ) {
    }

    public function toArray(): array
    {
        return (new Attributes())->prep([
            'subscription_type' => $this->type,
            'billing_date' => $this->billingDate->format('Y-m-d'),
            'recurring_amount' => $this->recurringAmount
                ? (new Money($this->recurringAmount))->format()
                : null,
            'frequency' => $this->frequency,
            'cycles' => $this->cycles,
        ]);
    }
}
