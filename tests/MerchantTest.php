<?php

declare(strict_types=1);

use TPG\PayFast\Merchant;

it('can create a new merchant object', function () {

    $merchant = new Merchant(
        'MERCHANT_ID',
        'MERCHANT_SECRET',
        'MERCHANT_PASSPHRASE');

    expect($merchant)->toBeInstanceOf(Merchant::class);

});

it('can create a testing merchant', function () {

    $merchant = new Merchant('id', 'secret', 'passphrase', true);

    expect($merchant->testing)->toBeTrue();

});
