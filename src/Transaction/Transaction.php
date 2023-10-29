<?php

declare(strict_types=1);

namespace TPG\PHPayfast\Transaction;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use TPG\PHPayfast\Attributes;
use TPG\PHPayfast\Customer\Customer;
use TPG\PHPayfast\Enums\PayfastEndpoint;
use TPG\PHPayfast\Enums\PaymentMethod;
use TPG\PHPayfast\Enums\SubscriptionFrequency;
use TPG\PHPayfast\Enums\SubscriptionType;
use TPG\PHPayfast\Merchant;
use TPG\PHPayfast\Money;
use TPG\PHPayfast\Subscription\Subscription;

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

    public function subscription(
        SubscriptionFrequency $frequency = SubscriptionFrequency::Monthly,
        int $cycles = 0,
        int $amount = null,
        \DateTime $billingDate = null,
        bool $notifyBuyer = null
    ): self {
        $this->subscription = new Subscription(
            type: SubscriptionType::Subscription,
            frequency: $frequency,
            cycles: $cycles,
            billingDate: $billingDate,
            amount: $amount,
            notifyBuyer: $notifyBuyer
        );

        return $this;
    }

    public function tokenize(): self
    {
        $this->subscription = new Subscription(
            type: SubscriptionType::Tokenization,
        );

        return $this;
    }

    public function confirm(string $email): self
    {
        $this->confirmationEmail = $email;

        return $this;
    }

    public function splitWith(Split|string $splitWith, float $amount, int $min = null, int $max = null): self
    {
        $this->split = $splitWith instanceof Split
            ? $splitWith
            : new Split(
                merchantId: $splitWith,
                percentage: $amount < 0 ? $amount : null,
                amount: $amount > 0 ? $amount : null,
                min: $min,
                max: $max
            );

        return $this;
    }

    public function validate(): void
    {
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

    public function onsite(): string
    {
        $signature = (new Signature(
            $this->toArray(),
            $this->merchant->passphrase)
        )->generate();

        $response = Http::asJson()->acceptJson()->post($this->getHost(onsite: true), [
            'signature' => $signature,
            ...$this->toArray(),
        ]);

        if (! $response->successful()) {
            throw new \Exception($response->body(), $response->status());
        }

        return $response->json('uuid');
    }

    public function charge(string $token)
    {
        $headers = [
            'merchant-id' => $this->merchant->id,
            'version' => 'v1',
            'timestamp' => now()->toIso8601String(),
        ];

        $body = Arr::only($this->toArray(), [
            'amount',
            'item_name',
            'item_description',
            'm_payment_id',
            'setup',
        ]);

        $signature = (new Signature([
            ...$headers,
            ...$body,
        ], $this->merchant->passphrase))->generate();

        $url = 'https://api.payfast.co.za/subscriptions/'.$token.'/adhoc';

        if ($this->merchant->testing) {
            $url .= '?testing=true';
        }

        $response = Http::asJson()->acceptJson()->withHeaders([
            'merchant-id' => $this->merchant->id,
            'version' => 'v1',
            'timestamp' => now()->toIso8601String(),
            'signature' => $signature,
        ])->post($url, [
            Arr::only($this->toArray(), [
                'amount',
                'item_name',
                'item_description',
                'm_payment_id',
                'setup',
            ]),
        ]);

        dd($response->json());
    }

    protected function getHost(bool $onsite = false): string
    {
        return ($onsite ? PayfastEndpoint::Onsite : PayfastEndpoint::Process)->url($this->merchant->testing);
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
