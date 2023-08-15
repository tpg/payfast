<?php

declare(strict_types=1);

namespace TPG\PayFast;

readonly class Customer
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public ?string $email,
        public ?string $cell
    )
    {
    }


    public function attributes(): array
    {
        return (new Attributes())->prep([
            'name_first' => $this->firstName,
            'name_last' => $this->lastName,
            'email_address' => $this->email,
            'cell_number' => $this->cell,
        ]);
    }
}
