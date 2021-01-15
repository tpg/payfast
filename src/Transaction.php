<?php

declare(strict_types=1);

namespace TPG\PayFast;

class Transaction
{
    protected int $amount;
    protected string $name;
    protected ?Customer $customer = null;
    protected Merchant $merchant;
    protected ?string $paymentId = null;
    protected ?string $description = null;
    protected array $customIntegers = [];
    protected array $customStrings = [];
    protected bool $emailConfirmation = false;
    protected ?string $emailConfirmationAddress = null;
    protected ?string $paymentMethod = null;

    public function __construct(Merchant $merchant, int $amount, string $name)
    {
        $this->merchant = $merchant;
        $this->amount = $amount;
        $this->name = $name;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function setPaymentId(string $paymentId): self
    {
        $this->paymentId = $paymentId;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setCustomIntegers(array $customIntegers = []): self
    {
        $this->customIntegers = $customIntegers;
        return $this;
    }

    public function setCustomStrings(array $customStrings = []): self
    {
        $this->customStrings = $customStrings;
        return $this;
    }

    public function setEmailConfirmation(bool $emailConfirmation = true): self
    {
        $this->emailConfirmation = $emailConfirmation;
        return $this;
    }

    public function setEmailConfirmationAddress(string $emailAddress): self
    {
        $this->emailConfirmationAddress = $emailAddress;
        return $this;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function attributes(): array
    {
        $attributes = array_merge(
            $this->merchant->attributes(),
            $this->customer ? $this->customer->attributes() : [],
            [
                'm_payment_id' => $this->paymentId,
                'amount' => $this->getDecimalAmount(),
                'item_name' => $this->name,
                'item_description' => $this->description,
            ],
            $this->getCustomAttributes($this->customIntegers, 'custom_int'),
            $this->getCustomAttributes($this->customStrings, 'custom_str'),
            [
                'email_confirmation' => $this->emailConfirmation ? '1' : '0',
                'confirmation_address' => $this->emailConfirmationAddress,
                'payment_method' => $this->paymentMethod,
            ]
        );

        return array_filter($attributes, static fn ($value) => ! empty($value));
    }

    protected function getDecimalAmount(): string
    {
        return number_format($this->amount / 100, 2, '.', '');
    }

    protected function getCustomAttributes(array $custom, string $keyPrefix): array
    {
        $values = [];

        for ($i = 1; $i <= 5; $i++) {
            $values[$keyPrefix.$i] = $custom[$i - 1] ?? null;
        }

        return array_filter($values, static fn ($value) => ! empty($value));
    }
}
