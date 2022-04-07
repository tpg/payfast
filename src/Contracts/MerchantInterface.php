<?php

declare(strict_types=1);

namespace TPG\PayFast\Contracts;

use GuzzleHttp\Client;

interface MerchantInterface
{
    public function ping(bool $testing = false, Client $client = null): bool;
    public function merchantId(): string;
    public function merchantKey(): string;
    public function passphrase(): string;
    public function setReturnUrl(string $returnUrl): self;
    public function setCancelUrl(string $cancelUrl): self;
    public function setNotifyUrl(string $notifyUrl): self;
    public function attributes(): array;
}
