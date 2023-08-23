<?php

declare(strict_types=1);

use TPG\PHPayfast\Exceptions\ValidationException;
use TPG\PHPayfast\Payfast;
use TPG\PHPayfast\Transaction\Transaction;

$merchant = new \TPG\PHPayfast\Merchant('id', 'secret', 'passphrase');

it('can create a new transaction object', function () use ($merchant) {

    $transaction = Payfast::merchant($merchant)
        ->createTransaction('item name', 10000);

    expect($transaction)->toBeInstanceOf(Transaction::class);

    $form = $transaction->form();

    expect($form)->toContain('<form method="post" action="https://www.payfast.co.za/eng/process" id="payfast">');
});

it('can have customer data attached', function () use ($merchant) {
    $transaction = Payfast::merchant($merchant)
        ->createTransaction('name', 10000)
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
        ->createTransaction('name', 10000)
        ->withStrings(['s1', 's2', 's3'])
        ->withIntegers([1, 2, 3]);

    expect($transaction->customStrings)->toBe(['s1', 's2', 's3'])
        ->and($transaction->customIntegers)->toBe([1, 2, 3]);
});

it('can have return, cancel and notify urls', function () use ($merchant) {

    $transaction = Payfast::merchant($merchant)
        ->createTransaction('name', 10000)
        ->withUrls('https://return.test', 'https://cancel.test', 'https://notify.test');

    expect($transaction->returnUrl)->toBe('https://return.test')
        ->and($transaction->cancelUrl)->toBe('https://cancel.test')
        ->and($transaction->notifyUrl)->toBe('https://notify.test');

});

it('will validate URLs', function () use ($merchant) {

    $transaction = Payfast::merchant($merchant)
        ->createTransaction('name', 10000)
        ->withUrls('not_a_url');



    $transaction->validate();

})->throws(ValidationException::class, 'return_url must be a URL');
