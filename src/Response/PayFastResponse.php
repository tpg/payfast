<?php

declare(strict_types=1);

namespace TPG\PayFast\Response;

use DateTime;
use Illuminate\Support\Arr;
use TPG\PayFast\Customer\Customer;
use TPG\PayFast\Enums\PaymentStatus;
use TPG\PayFast\Money;

readonly class PayFastResponse
{
    public function __construct(
        public string $payfastPaymentId,
        public PaymentStatus $paymentStatus,
        public string $name,
        public int $merchantId,
        public string $token,
        public ?string $merchantPaymentId = null,
        public ?string $description = null,
        public ?int $gross = null,
        public ?int $fee = null,
        public ?int $net = null,
        public array $customIntegers = [],
        public array $customStrings = [],
        public ?Customer $customer = null,
        public ?DateTime $billingDate = null,
        public ?string $signature = null,
    ) {
    }

    public static function createFromResponse(array $data): self
    {
        return new self(
            payfastPaymentId: Arr::get($data, 'pf_payment_id'),
            paymentStatus: PaymentStatus::tryFrom(Arr::get($data, 'payment_status')),
            name: Arr::get($data, 'item_name'),
            merchantId: (int) Arr::get($data, 'merchant_id'),
            token: Arr::get($data, 'token'),
            merchantPaymentId: Arr::get($data, 'm_payment_id'),
            description: Arr::get($data, 'item_description'),
            gross: (new Money(Arr::get($data, 'amount_gross')))->value,
            fee: (new Money(Arr::get($data, 'amount_fee')))->value,
            net: (new Money(Arr::get($data, 'amount_net')))->value,
            customIntegers: self::customValues('custom_int'),
            customStrings: self::customValues('custom_str'),
            customer: self::customer($data),
            billingDate: Arr::get($data, 'billing_date')
                ? DateTime::createFromFormat('Y-m-d', Arr::get($data, 'billing_date'))
                : null,
            signature: Arr::get($data, 'signature'),
        );
    }

    protected static function customValues(string $prefix): array
    {
        $integers = [];

        for ($i = 1; $i <= 5; $i++) {
            $integers[] = $data[$prefix.$i] ?? null;
        }

        return array_filter($integers, static fn ($value) => ! empty($value));
    }

    protected static function customer(array $data): ?Customer
    {
        $firstName = Arr::get($data, 'name_first');
        $lastName = Arr::get($data, 'name_last');
        $email = Arr::get($data, 'email_address');

        if (! $firstName && ! $lastName && ! $email) {
            return null;
        }

        return new Customer($firstName, $lastName, $email);
    }
}
