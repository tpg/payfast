<?php

declare(strict_types=1);

use TPG\PayFast\Customer;

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
