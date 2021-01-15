<?php

declare(strict_types=1);

namespace TPG\PayFast;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

class PayFast
{
    protected Transaction $transaction;
    protected bool $testing = false;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function setTesting(bool $testing = true): self
    {
        $this->testing = $testing;
        return $this;
    }

    public function form(?int $submitTimeout = null): string
    {
        $signature = (new Signature($this->transaction, 'secret'))->generate();

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
            $testing ? 'sandbox.' : null,
            'payfast.co.za/eng/process',
        ]);
    }
}
