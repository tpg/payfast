<?php

declare(strict_types=1);

namespace TPG\PayFast;

class Money
{
    public int $value;

    public function __construct(mixed $amount)
    {
        if (str_contains((string) $amount, '.')) {
            $this->value = (int) str_replace('.', '', $amount);
        } else {
            $this->value = (int) $amount;
        }
    }

    public function format(): string
    {
        return number_format($this->value / 100, 2, '.', '');
    }

    public function rands(): int
    {
        return (int) substr((string) $this->value, 0, -2);
    }

    public function cents(): int
    {
        return (int) substr((string) $this->value, -2);
    }
}
