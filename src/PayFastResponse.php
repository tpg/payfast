<?php

declare(strict_types=1);

namespace TPG\PayFast;

use DateTime;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use TPG\PayFast\Enums\PaymentStatus;
use TPG\PayFast\Exceptions\ValidationException;

readonly class PayFastResponse
{
    protected Customer $customer;
    protected ?array $customIntegers;
    protected ?array $customStrings;
    public ?string $merchantPaymentId;
    public int $payfastPaymentId;
    public ?PaymentStatus $paymentStatus;
    public string $itemName;
    public ?string $itemDescription;
    public int $amountGross;
    public int $amountFee;
    public int $amountNet;
    public int $merchantId;
    public string $token;
    public ?Carbon $billingDate;
    public ?string $signature;

    public function __construct(public array $data)
    {
        $this->customer = (new Customer(
            firstName: $data['name_first'] ?? null,
            lastName: $data['name_last'] ?? null,
            email: $data['email_address'] ?? null,
            cell: $data['cell_number'] ?? null,
        ));

        $this->merchantPaymentId = Arr::get($data, 'm_payment_id');
        $this->payfastPaymentId = Arr::get($data, 'pf_payment_id');
        $this->paymentStatus = PaymentStatus::tryFrom(Arr::get($data, 'payment_status'));
        $this->itemName = Arr::get($data, 'item_name');
        $this->itemDescription = Arr::get($data, 'item_description');
        $this->amountGross = $this->money('amount_gross');
        $this->amountFee = $this->money('amount_fee');
        $this->amountNet = $this->money('amount_net');
        $this->customIntegers = $this->customValues('custom_int');
        $this->customStrings = $this->customValues('custom_str');
        $this->merchantId = Arr::get($data, 'merchant_id');
        $this->token = Arr::get($data, 'token');
        $this->billingDate = $this->date('billing_date');
        $this->signature = Arr::get($data, 'signature');
    }

    protected function money(string $key): int
    {
        return (int)str_replace('.', '', (string)$this->data[$key]);
    }

    protected function date(string $key): ?Carbon
    {
        $data = Arr::get($this->data, $key);
        if (! $data) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d', $data);
    }

    protected function customValues(string $prefix): array
    {
        $integers = [];

        for ($i = 1; $i <= 5; $i++) {
            $integers[] = $data[$prefix.$i] ?? null;
        }

        return array_filter($integers, static fn ($value) => ! empty($value));
    }
}
