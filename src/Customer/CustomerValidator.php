<?php

declare(strict_types=1);

namespace TPG\PayFast\Customer;

use Valitron\Validator;

class CustomerValidator
{
    protected Validator $validator;

    public function __construct(protected Customer $customer)
    {
        $this->validator = new Validator($this->customer->toArray());
        $this->validator->rules([
            'required' => [
                'name_first',
                'name_last',
            ],
            'email' => [
                'email_address',
            ],
            'numeric' => [
                'cell_number',
            ],
        ]);
    }

    public function validate(): array|bool
    {
        $this->validator->validate();
        return $this->validator->errors();
    }
}
