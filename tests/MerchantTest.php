<?php

declare(strict_types=1);

namespace TPG\PayFast\Tests;

use TPG\PayFast\Merchant;
use TPG\PayFast\PayFast;
use TPG\PayFast\Transaction;

class MerchantTest extends TestCase
{
    /**
     * @test
     **/
    public function it_can_create_a_new_merchant(): void
    {
        $merchant = new Merchant('MERCHANT_ID', 'MERCHANT_KEY');
        $merchant->setReturnUrl('http://return.url')
            ->setCancelUrl('http://cancel.url')
            ->setNotifyUrl('http://notify.url');

        self::assertEquals([
            'merchant_id' => 'MERCHANT_ID',
            'merchant_key' => 'MERCHANT_KEY',
            'return_url' => 'http://return.url',
            'cancel_url' => 'http://cancel.url',
            'notify_url' => 'http://notify.url',
        ], $merchant->attributes());
    }
}
