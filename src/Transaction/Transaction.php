<?php

declare(strict_types=1);

namespace TPG\PayFast\Transaction;

use TPG\PayFast\Attributes;
use TPG\PayFast\Customer\Customer;
use TPG\PayFast\Enums\PaymentMethod;
use TPG\PayFast\Transaction\FormBuilder;
use TPG\PayFast\Merchant;
use TPG\PayFast\Money;
use TPG\PayFast\Transaction\Split;
use TPG\PayFast\Subscription\Subscription;
use TPG\PayFast\Validation\Signature;

class Transaction
{
    public ?Customer $customer = null;

    public ?string $returnUrl = null;

    public ?string $cancelUrl = null;

    public ?string $notifyUrl = null;

    /**
     * @var array<int>
     */
    public array $customIntegers = [];

    /**
     * @var array<string>
     */
    public array $customStrings = [];

    public ?PaymentMethod $paymentMethod = null;

    public ?Subscription $subscription = null;

    public ?string $confirmationEmail = null;

    public ?Split $split = null;

    public function __construct(
        readonly protected Merchant $merchant,
        public string $name,
        public int $amount,
        public ?string $description = null,
        public ?string $merchantPaymentId = null,
    ) {
    }

    public function for(
        Customer|string $customer,
        string $lastName = null,
        string $email = null,
        string $cell = null
    ): self {
        $this->customer = $customer instanceof Customer
            ? $customer
            : new Customer($customer, $lastName, $email, $cell);

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
            ...$this->subscription?->toArray() ?? [],
            ...$this->setup(),
        ]);
    }

    public function form(string $id = 'payfast', int $submitTimeout = null): string
    {
        $signature = (new Signature(
            $this->toArray(),
            $this->merchant->passphrase)
        )->generate();

        return (new FormBuilder(
            $id,
            $this,
            $signature,
            $this->getHost(),
            $submitTimeout
        ))->build();
    }

    protected function getHost(): string
    {
        return implode('', [
            'https://',
            $this->merchant->testing ? 'sandbox.' : 'www.',
            'payfast.co.za/eng/process',
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
