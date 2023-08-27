<?php

declare(strict_types=1);

namespace TPG\PHPayfast\Customer;

use TPG\PHPayfast\Attributes;
use TPG\PHPayfast\Validator;
use TPG\Yerp\Rules;

readonly class Customer
{
    public function __construct(
        #[Rules\Required(failure: 'The first name is required.')]
        public ?string $firstName = null,
        public ?string $lastName = null,
        #[Rules\Email(failure: 'The email address is invalid.')]
        public ?string $email = null,
        public ?string $cell = null
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        Validator::validate($this);
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
