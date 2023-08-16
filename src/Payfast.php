<?php

declare(strict_types=1);

namespace TPG\PayFast;

class Payfast
{
    protected readonly Transaction $transaction;

    public function __construct(protected readonly Merchant $merchant, protected readonly bool $testing = false)
    {
    }

    public static function merchant(string $id, string $key, string $passphrase, bool $testing = false): self
    {
        return new self(new Merchant($id, $key, $passphrase), $testing);
    }

    public function createTransaction(string $name, int $amount, string $description = null, string $merchantPaymentId = null): Transaction
    {
        return $this->transaction = new Transaction($name, $amount, $description, $merchantPaymentId);
    }

    public function form(string $id = 'payfast', int $submitTimeout = null): string
    {
        $signature = (new Signature(
            $this->transaction->toArray(),
            $this->transaction->merchant->passphrase)
        )->generate();

        return (new FormBuilder(
            $id,
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
