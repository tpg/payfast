<?php

declare(strict_types=1);

namespace TPG\PayFast;

use DateTime;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use TPG\PayFast\Exceptions\ValidationException;

class PayFastResponse
{
    protected array $data;

    protected Customer $customer;
    protected Transaction $transaction;
    protected array $customIntegers = [];
    protected array $customStrings = [];

    public function __construct(array $data)
    {
        $this->data = $data;

        $this->customIntegers = $this->getCustomValues('custom_int');
        $this->customStrings = $this->getCustomValues('custom_str');

        $this->customer = (new Customer())
            ->setName($data['name_first'] ?? null, $data['name_last'] ?? null)
            ->setEmail($data['email_address'] ?? null)
            ->setCellNumber($data['cell_number'] ?? null);

    }

    public function merchantPaymentId(): ?string
    {
        return Arr::get($this->data, 'm_payment_id');
    }

    public function payFastPaymentId(): string
    {
        return $this->data['pf_payment_id'];
    }

    public function name(): string
    {
        return $this->data['item_name'];
    }

    public function description(): ?string
    {
        return Arr::get($this->data, 'item_description');
    }

    public function amountGross(): int
    {
        return (int)str_replace('.', '', (string)$this->data['amount_gross']);
    }

    public function amountFee(): int
    {
        return (int)str_replace('.', '', (string)$this->data['amount_fee']);
    }

    public function amountNet(): int
    {
        return (int)str_replace('.', '', (string)$this->data['amount_net']);
    }

    public function customIntegers(): array
    {
        return $this->getCustomValues('custom_int');
    }

    public function customStrings(): array
    {
        return $this->getCustomValues('custom_str');
    }

    protected function getCustomValues(string $prefix): array
    {
        $integers = [];

        for ($i = 1; $i <= 5; $i++) {
            $integers[] = $data[$prefix.$i] ?? null;
        }

        return array_filter($integers, fn ($value) => ! empty($value));
    }

    public function customer(): Customer
    {
        return (new Customer())
            ->setName($this->data['name_first'] ?? null, $this->data['name_last'] ?? null)
            ->setEmail($this->data['email_address'] ?? null)
            ->setCellNumber($this->data['cell_number'] ?? null);
    }

    public function signature(): string
    {
        return $this->data['signature'];
    }

    public function token(): ?string
    {
        return Arr::get($this->data, 'token');
    }

    public function paymentStatus(): string
    {
        return $this->data['payment_status'];
    }

    public function billingDate(): ?DateTime
    {
        $date = Arr::get($this->data, 'billing_date');

        if (! $date) {
            return null;
        }

        return DateTime::createFromFormat('Y-m-d', $date);
    }
}
