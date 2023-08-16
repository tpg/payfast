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
