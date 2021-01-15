<?php

declare(strict_types=1);

namespace TPG\PayFast\Tests;

use TPG\PayFast\FormBuilder;
use TPG\PayFast\Merchant;
use TPG\PayFast\Signature;
use TPG\PayFast\Transaction;

class FormBuilderTest extends TestCase
{
    /**
     * @test
     **/
    public function it_can_build_a_form(): void
    {
        $merchant = new Merchant('ID', 'KEY');
        $transaction = new Transaction($merchant, 10000, 'Transaction');

        $signature = (new Signature($transaction, 'passphrase'))->generate();

        $form = (new FormBuilder(
            $transaction,
            $signature,
            'http://host.test'
        ));

        self::assertStringContainsString(
            '<input type="hidden" name="signature" value="'.$signature.'" />',
            $form->build()
        );
    }
}
