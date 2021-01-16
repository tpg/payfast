<?php

declare(strict_types=1);

namespace TPG\PayFast;

class Customer
{
    protected ?string $firstName = null;
    protected ?string $lastName = null;
    protected ?string $email = null;
    protected ?string $cellNumber = null;

    public function setName(?string $first = null, string $last = null): self
    {
        $this->firstName = $first;
        $this->lastName = $last;

        return $this;
    }

    public function firstName(): ?string
    {
        return $this->firstName;
    }

    public function lastName(): ?string
    {
        return $this->lastName;
    }

    public function setEmail(?string $email = null): self
    {
        $this->email = $email;
        return $this;
    }

    public function emailAddress(): ?string
    {
        return $this->email;
    }

    public function setCellNumber(?string $cellNumber = null): self
    {
        $this->cellNumber = $cellNumber;
        return $this;
    }

    public function cellNumber(): ?string
    {
        return $this->cellNumber;
    }

    public function attributes(): array
    {
        return (new Attributes())->prep([
            'name_first' => $this->firstName,
            'name_last' => $this->lastName,
            'email_address' => $this->email,
            'cell_number' => $this->cellNumber,
        ]);
    }
}
