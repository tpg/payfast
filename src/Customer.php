<?php

declare(strict_types=1);

namespace TPG\PayFast;

class Customer
{
    protected ?string $firstName = null;
    protected ?string $lastName = null;
    protected ?string $email = null;
    protected ?string $cellNumber = null;

    public function setName(string $first, string $last = null): self
    {
        $this->firstName = $first;
        $this->lastName = $last;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setCellNumber(string $cellNumber): self
    {
        $this->cellNumber = $cellNumber;
        return $this;
    }

    public function attributes(): array
    {
        return array_filter([
            'name_first' => $this->firstName,
            'name_last' => $this->lastName,
            'email_address' => $this->email,
            'cell_number' => $this->cellNumber,
        ], static fn ($value) => ! empty($value));
    }
}
