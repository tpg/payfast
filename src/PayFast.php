<?php

declare(strict_types=1);

namespace TPG\PayFast;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

class PayFast
{
    protected bool $testing = false;
    protected string $passphrase;

    public function __construct(protected Transaction $transaction)
    {
        $this->passphrase = $transaction->merchant()->passphrase;
    }

    public function testing(bool $testing = true): self
    {
        $this->testing = $testing;
        return $this;
    }

    public function form(?int $submitTimeout = null): string
    {
        $signature = (new Signature($this->transaction->attributes(), $this->passphrase))->generate();

        return (new FormBuilder(
            $this->transaction,
            $signature,
            $this->getHost($this->testing),
            $submitTimeout
        ))->build();
    }

    protected function getHost(bool $testing): string
    {
        return implode('', [
            'https://',
            $testing ? 'sandbox.' : 'www.',
            'payfast.co.za/eng/process',
        ]);
    }
}
