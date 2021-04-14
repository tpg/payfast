<?php

declare(strict_types=1);

namespace TPG\PayFast;

class Merchant
{
    protected $attributes = [
        'merchant_id' => null,
        'merchant_key' => null,
        'return_url' => null,
        'cancel_url' => null,
        'notify_url' => null,
    ];
    protected string $passphrase;

    public function __construct(string $merchantId, string $merchantKey, string $passphrase)
    {
        $this->attributes['merchant_id'] = $merchantId;
        $this->attributes['merchant_key'] = $merchantKey;
        $this->passphrase = $passphrase;
    }

    public function merchantId(): string
    {
        return $this->attributes['merchant_id'];
    }

    public function merchantKey(): string
    {
        return $this->attributes['merchant_key'];
    }

    public function passphrase(): string
    {
        return $this->passphrase;
    }

    public function setReturnUrl(string $returnUrl): self
    {
        $this->attributes['return_url'] = $returnUrl;
        return $this;
    }

    public function setCancelUrl(string $cancelUrl): self
    {
        $this->attributes['cancel_url'] = $cancelUrl;
        return $this;
    }

    public function setNotifyUrl(string $notifyUrl): self
    {
        $this->attributes['notify_url'] = $notifyUrl;
        return $this;
    }

    public function attributes(): array
    {
        return (new Attributes())->prep($this->attributes);
    }
}
