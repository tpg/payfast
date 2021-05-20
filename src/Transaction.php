<?php

declare(strict_types=1);

namespace TPG\PayFast;

class Transaction
{
    public const SUBSCRIPTION_FREQUENCY_MONTHLY = 3;
    public const SUBSCRIPTION_FREQUENCY_QUARTERLY = 4;
    public const SUBSCRIPTION_FREQUENCY_BIANNUALLY = 5;
    public const SUBSCRIPTION_FREQUENCY_ANNUALLY = 6;

    protected bool $subscription = false;
    protected ?\DateTime $billingDate = null;
    protected int $amount;
    protected int $recurringAmount;
    protected int $frequency = self::SUBSCRIPTION_FREQUENCY_MONTHLY;
    protected int $cycles = 0;
    protected string $name;
    protected ?Customer $customer = null;
    protected Merchant $merchant;
    protected ?string $merchantPaymentId = null;
    protected ?string $description = null;
    protected array $customIntegers = [];
    protected array $customStrings = [];
    protected bool $emailConfirmation = false;
    protected ?string $emailConfirmationAddress = null;
    protected ?string $paymentMethod = null;

    public function __construct(Merchant $merchant, int $amount, string $name)
    {
        $this->merchant = $merchant;
        $this->recurringAmount = $this->amount = $amount;
        $this->name = $name;
    }

    public function subscription(int $frequency = 3, int $cycles = 0, ?\DateTime $billingDate = null): self
    {
        $this->frequency = $frequency;
        $this->cycles = $cycles;
        $this->subscription = true;
        $this->billingDate = $billingDate ?? new \DateTime();

        return $this;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function setRecurringAmount(int $amount): self
    {
        $this->recurringAmount = $amount;
        return $this;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function setMerchantPaymentId(string $merchantPaymentId): self
    {
        $this->merchantPaymentId = $merchantPaymentId;
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

    public function merchant(): Merchant
    {
        return $this->merchant;
    }

    public function attributes(): array
    {
        return (new Attributes())->prep(array_merge(
            $this->merchant->attributes(),
            $this->customer ? $this->customer->attributes() : [],
            [
                'm_payment_id' => $this->merchantPaymentId,
                'amount' => $this->getDecimalAmount($this->amount),
                'item_name' => $this->name,
                'item_description' => $this->description,
            ],
            $this->getCustomAttributes($this->customIntegers, 'custom_int'),
            $this->getCustomAttributes($this->customStrings, 'custom_str'),
            [
                'email_confirmation' => $this->emailConfirmation ? 1 : 0,
                'confirmation_address' => $this->emailConfirmationAddress,
                'payment_method' => $this->paymentMethod,
            ],
            $this->subscription ? [
                'subscription_type' => 1,
                'billing_date' => $this->billingDate->format('Y-m-d'),
                'recurring_amount' => $this->getDecimalAmount($this->recurringAmount),
                'frequency' => $this->frequency,
                'cycles' => $this->cycles,
            ] : [],
        ));
    }

    protected function getDecimalAmount(int $amount): string
    {
        return number_format($amount / 100, 2, '.', '');
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
