<?php

declare(strict_types=1);

namespace TPG\PHPayfast\Customer;

use TPG\PHPayfast\Exceptions\ValidationException;
use TPG\PHPayfast\Validation\Validator;

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
