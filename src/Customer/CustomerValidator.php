<?php

declare(strict_types=1);

namespace TPG\PayFast\Customer;

use TPG\PayFast\Exceptions\ValidationException;
use TPG\PayFast\Validation\Validator;

class CustomerValidator extends Validator
{
    public function rules(): array
    {
        return [
            'name_first' => [
                'string',
                'required',
            ],
            'name_last' => [
                'string',
                'required',
            ],
            'email_address' => [
                'string',
                'email',
            ],
            'cell_number' => [
                'string',
                'numeric',
            ],
        ];
    }
}
