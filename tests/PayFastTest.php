<?php

declare(strict_types=1);

namespace TPG\PayFast\Tests;

use TPG\PayFast\Merchant;
use TPG\PayFast\PayFast;
use TPG\PayFast\Transaction;

class PayFastTest extends TestCase
{
    /**
     * @test
     **/
    public function it_can_return_a_submit_form(): void
    {
        $merchant = new Merchant('ID', 'KEY', 'passphrase');
        $transaction = new Transaction($merchant, 10000, 'Transaction');

        $payfast = new PayFast($transaction->subscription());

        self::assertStringContainsString(
            'document.querySelector(\'#payfast_form\').submit()',
            $payfast->form(5));
    }
}
