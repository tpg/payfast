<?php

declare(strict_types=1);

namespace TPG\PayFast;

class Merchant
{
    protected string $merchantId;
    protected string $merchantKey;
    protected ?string $returnUrl = null;
    protected ?string $cancelUrl = null;
    protected ?string $notifyUrl = null;

    public function __construct(string $merchantId, string $merchantKey)
    {
        $this->merchantId = $merchantId;
        $this->merchantKey = $merchantKey;
    }

    public function setReturnUrl(string $returnUrl): self
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    public function setCancelUrl(string $cancelUrl): self
    {
        $this->cancelUrl = $cancelUrl;
        return $this;
    }

    public function setNotifyUrl(string $notifyUrl): self
    {
        $this->notifyUrl = $notifyUrl;
        return $this;
    }

    public function attributes(): array
    {
        return array_filter([
            'merchant_id' => $this->merchantId,
            'merchant_key' => $this->merchantKey,
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
            'notify_url' => $this->notifyUrl,
        ], static fn ($value) => ! empty($value));
    }
}
