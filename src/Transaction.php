<?php

declare(strict_types=1);

namespace TPG\PayFast;

use TPG\PayFast\Enums\PaymentMethod;

class Transaction
{
    public readonly Customer $customer;
    public readonly ?string $returnUrl;
    public readonly ?string $cancelUrl;
    public readonly ?string $notifyUrl;
    public readonly ?array $customIntegers;
    public readonly ?array $customStrings;
    public readonly ?PaymentMethod $paymentMethod;
    public readonly ?Subscription $subscription;
    public readonly ?string $confirmationEmail;
    public readonly ?Split $split;

    public function __construct(
        readonly public string $name,
        readonly public int $amount,
        readonly public ?string $description = null,
        readonly public ?string $merchantPaymentId = null,
    ) {
    }

    public function for(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function withUrls(string $return = null, string $cancel = null, string $notify = null): self
    {
        $this->returnUrl = $return;
        $this->cancelUrl = $cancel;
        $this->notifyUrl = $notify;

        return $this;
    }

    public function withIntegers(array $integers): self
    {
        $this->customIntegers = $integers;

        return $this;
    }

    public function withStrings(array $strings): self
    {
        $this->customStrings = $strings;

        return $this;
    }

    public function allowPaymentMethod(PaymentMethod $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function subscription(Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function confirm(string $email): self
    {
        $this->confirmationEmail = $email;

        return $this;
    }

    public function splitWith(Split $split): self
    {
        $this->split = $split;

        return $this;
    }

    public function toArray(): array
    {
        return (new Attributes())->prep([
            ...$this->merchant->toArray(),
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
            'notify_url' => $this->notifyUrl,
            ...$this->customer?->toArray() ?? [],
            'm_payment_id' => $this->merchantPaymentId,
            'amount' => (new Money($this->amount))->format(),
            'item_name' => $this->name,
            'item_description' => $this->description,
            ...$this->customAttributes($this->customIntegers, 'custom_int'),
            ...$this->customAttributes($this->customStrings, 'custom_str'),
            'email_confirmation' => $this->confirmationEmail ? 1 : null,
            'confirmation_address' => $this->confirmationEmail,
            'payment_method' => $this->paymentMethod,
            ...$this->subscription?->toArray(),
            ...$this->setup(),
        ]);
    }

    protected function customAttributes(array $custom, string $keyPrefix): array
    {
        $values = [];

        for ($i = 1; $i <= 5; $i++) {
            $values[$keyPrefix.$i] = $custom[$i - 1] ?? null;
        }

        return array_filter($values, static fn ($value) => ! empty($value));
    }

    protected function setup(): array
    {
        return (new Attributes())->prep([
            'setup' => $this->split?->toArray(),
        ]);
    }
}
