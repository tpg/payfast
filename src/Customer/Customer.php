<?php

declare(strict_types=1);

namespace TPG\PHPayfast\Customer;

use TPG\PHPayfast\Attributes;
use TPG\PHPayfast\Exceptions\ValidationException;

readonly class Customer
{
    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $cell = null
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        (new CustomerValidator)->validate($this->toArray());
    }

    public function toArray(): array
    {
        return (new Attributes())->prep([
            'name_first' => $this->firstName,
            'name_last' => $this->lastName,
            'email_address' => $this->email,
            'cell_number' => $this->cell,
        ]);
    }
}
