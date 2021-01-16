<?php

declare(strict_types=1);

namespace TPG\PayFast;

class Signature
{
    protected Transaction $transaction;
    protected ?string $passphrase;

    public function __construct(Transaction $transaction, ?string $passphrase = null)
    {
        $this->transaction = $transaction;
        $this->passphrase = $passphrase;
    }

    public function generate(): string
    {
        return md5($this->attributes());
    }

    protected function attributes(): string
    {
        $attributes = $this->transaction->attributes();

        array_walk($attributes, static function (&$value, $key) {
            $value = $key.'='.urlencode(trim($value));
        });

        return implode(
            '&',
            array_values($attributes)
        ).'&passphrase='.urlencode(trim($this->passphrase));
    }
}
