<?php

declare(strict_types=1);

use TPG\PayFast\Enums\PaymentMethod;
use TPG\PayFast\Enums\SubscriptionFrequency;
use TPG\PayFast\Enums\SubscriptionType;

it('can create a new transaction object', function () {

    $transaction = \TPG\PayFast\Payfast::merchant('ID', 'SECRET', 'PASSPHRASE')
        ->createTransaction('New Shoes', 19995, 'White shoes', 'PAY101');

    expect($transaction->paymentMethod)->toBe(PaymentMethod::All);
});
