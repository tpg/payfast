<?php

declare(strict_types=1);

namespace TPG\PayFast\Transaction;

use TPG\PayFast\Validation\Validator;

class TransactionValidator extends Validator
{
    public function rules(): array
    {
        return [
            'merchant_id' => [
                'string',
                'required',
            ],
            'merchant_key' => [
                'string',
                'required',
            ],
            'name_first' => [
                'nullable',
                'string',
            ],
            'name_last' => [
                'nullable',
                'string',
            ],
            'return_url' => [
                'url',
            ],
            'cancel_url' => [
                'url',
            ],
            'notify_url' => [
                'url',
            ],
        ];
    }
}
