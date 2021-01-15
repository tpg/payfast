<?php

declare(strict_types=1);

namespace TPG\PayFast\Tests;

use TPG\PayFast\Customer;
use TPG\PayFast\Merchant;
use TPG\PayFast\Transaction;

class TransactionTest extends TestCase
{
    /**
     * @test
     **/
    public function it_can_create_a_new_transaction_instance(): void
    {
        $merchant = new Merchant('ID', 'KEY');

        $transaction = new Transaction($merchant, 10000, 'Transaction');

        self::assertEquals(array_merge(
            $merchant->attributes(),
            [
                'amount' => '100.00',
                'item_name' => 'Transaction',
            ]
        ), $transaction->attributes());;
    }

    /**
     * @test
     **/
    public function it_can_have_a_customer(): void
    {
        $merchant = new Merchant('ID', 'KEY');
        $customer = (new Customer())
            ->setName('First', 'Last');

        $transaction = new Transaction($merchant, 10000, 'Transaction');
        $transaction->setCustomer($customer);

        self::assertEquals('First', $transaction->attributes()['name_first']);
        self::assertEquals('Last', $transaction->attributes()['name_last']);
        self::assertArrayNotHasKey('email_address', $transaction->attributes());
    }

    /**
     * @test
     **/
    public function it_can_have_a_payment_id(): void
    {
        $merchant = new Merchant('ID', 'KEY');
        $transaction = new Transaction($merchant, 10000, 'Transaction');

        $transaction->setPaymentId('PID1');

        self::assertEquals('PID1', $transaction->attributes()['m_payment_id']);
    }

    /**
     * @test
     **/
    public function it_can_have_custom_attributes(): void
    {
        $merchant = new Merchant('ID', 'KEY');
        $transaction = new Transaction($merchant, 10000, 'Transaction');

        $transaction->setCustomIntegers([
            10,
            20,
            30,
            40,
            50,
        ])->setCustomStrings([
            'S1',
            'S2',
            'S3',
            'S4',
            'S5'
        ]);

        self::assertArrayHasKey('custom_int5', $transaction->attributes());
        self::assertArrayHasKey('custom_str5', $transaction->attributes());
    }
}
