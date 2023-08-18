<?php

declare(strict_types=1);

namespace TPG\PayFast\Transaction;

readonly class Split
{
    public function __construct(
        public string $merchantId,
        public ?int $percentage = null,
        public ?int $amount = null,
        public ?int $min = null,
        public ?int $max = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'merchant_id' => $this->merchantId,
            'percentage' => $this->percentage,
            'amount' => $this->amount,
            'min' => $this->min,
            'max' => $this->max,
        ];
    }
}
