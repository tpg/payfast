<?php

declare(strict_types=1);

namespace TPG\PHPayfast;

use TPG\PHPayfast\Transaction\Transaction;
use TPG\PHPayfast\Transaction\Itn;

readonly class Payfast
{
    protected Transaction $transaction;

    public function __construct(protected Merchant $merchant)
    {
    }

    public static function merchant(
        string|Merchant $id,
        string $key = null,
        string $passphrase = null,
        bool $testing = false
    ): self {
        $merchant = $id instanceof Merchant ? $id : new Merchant($id, $key, $passphrase, $testing);

        return new self($merchant);
    }

    public function createTransaction(string $name, int $amount, string $description = null, string $merchantPaymentId = null): Transaction
    {
        return new Transaction($this->merchant, $name, $amount, $description, $merchantPaymentId);
    }

    public function validate(array $data, int $amount, string $referer): Itn
    {
        $validator = new Itn($data, $this->merchant->testing);
        $validator->validate($amount, $this->merchant->passphrase, $referer);

        return $validator;
    }
}
