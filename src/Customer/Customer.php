<?php

declare(strict_types=1);

namespace TPG\PayFast\Customer;

use TPG\PayFast\Attributes;
use TPG\PayFast\Exceptions\ValidationException;

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
        if ($messages = (new CustomerValidator($this))->validate()) {
            $message = $messages[array_key_first($messages)][0];
            throw new ValidationException($message);
        }
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
