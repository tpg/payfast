<?php

declare(strict_types=1);

namespace TPG\PayFast;

use DateTime;
use Illuminate\Support\Arr;
use TPG\PayFast\Enums\PaymentStatus;

readonly class PayFastResponse
{
    public function __construct(
        public string $payfastPaymentId,
        public PaymentStatus $paymentStatus,
        public string $itemName,
        public int $merchantId,
        public string $token,
        public ?string $merchantPaymentId = null,
        public ?string $itemDescription = null,
        public ?int $amountGross = null,
        public ?int $amountFee = null,
        public ?int $amountNet = null,
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
            itemName: Arr::get($data, 'item_name'),
            merchantId: (int) Arr::get($data, 'merchant_id'),
            token: Arr::get($data, 'token'),
            merchantPaymentId: Arr::get($data, 'm_payment_id'),
            itemDescription: Arr::get($data, 'item_description'),
            amountGross: (new Money(Arr::get($data, 'amount_gross')))->value,
            amountFee: (new Money(Arr::get($data, 'amount_fee')))->value,
            amountNet: (new Money(Arr::get($data, 'amount_net')))->value,
            customIntegers: self::customValues('custom_int'),
            customStrings: self::customValues('custom_str'),
            customer: self::customer($data),
        );
    }

    protected function money(string $key): int
    {
        return (int) str_replace('.', '', (string) $this->data[$key]);
    }

    protected function date(string $key): ?Carbon
    {
        $data = Arr::get($this->data, $key);
        if (! $data) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d', $data);
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
