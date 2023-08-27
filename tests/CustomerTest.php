<?php

declare(strict_types=1);

use TPG\PHPayfast\Customer\Customer;
use TPG\PHPayfast\Exceptions\ValidationException;

it('can create a new customer', function () {
    $customer = new Customer(
        firstName: 'Test',
        lastName: 'User',
        email: 'test@example.com',
        cell: '0821112222',
    );

    expect($customer)->toBeInstanceOf(Customer::class)
        ->and($customer->firstName)->toBe('Test');
});

it('will validate customer data', function () {

    $customer = new Customer('first', 'last', 'bad-email');

})->throws(ValidationException::class);
