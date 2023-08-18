<?php

declare(strict_types=1);

use TPG\PayFast\Exceptions\ValidationException;
use TPG\PayFast\Payfast;
use TPG\PayFast\Transaction\Transaction;

$merchant = new \TPG\PayFast\Merchant('id', 'secret', 'passphrase');

it('can create a new transaction object', function () use ($merchant) {

    $transaction = Payfast::merchant($merchant)
        ->createTransaction('item name', 10000, 'item description', 'merchant payment id');

    expect($transaction)->toBeInstanceOf(Transaction::class);

    $form = $transaction->form();

    expect($form)->toContain('<form method="post" action="https://www.payfast.co.za/eng/process" id="payfast">');
});

it('can have customer data attached', function () use ($merchant) {
    $transaction = Payfast::merchant($merchant)
        ->createTransaction('name', 10000, 'description', 'id')
        ->for('first name', 'last name', 'customer@email.test', '0821112222');

    expect($transaction->customer->firstName)->toBe('first name');

    $form = $transaction->form();

    expect($form)->toContain(
        '<input type="hidden" name="name_first" value="first name" />',
        '<input type="hidden" name="name_last" value="last name" />',
        '<input type="hidden" name="email_address" value="customer@email.test" />',
        '<input type="hidden" name="cell_number" value="0821112222" />'
    );
});

it('can can have custom data attached', function () use ($merchant) {

    $transaction = Payfast::merchant($merchant)
        ->createTransaction('name', 10000, 'description', 'id')
        ->withStrings(['s1', 's2', 's3'])
        ->withIntegers([1, 2, 3]);

    expect($transaction->customStrings)->toBe(['s1', 's2', 's3'])
        ->and($transaction->customIntegers)->toBe([1, 2, 3]);
});
