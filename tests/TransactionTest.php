<?php

declare(strict_types=1);

namespace TPG\PayFast\Tests;

use TPG\PayFast\Customer;
use TPG\PayFast\Merchant;
use TPG\PayFast\PayFast;
use TPG\PayFast\PaymentMethod;
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

    /**
     * @test
     **/
    public function ensure_attribute_order(): void
    {
        $merchant = new Merchant('ID', 'KEY');
        $merchant->setNotifyUrl('http://notify.url')
            ->setCancelUrl('http://cancel.url')
            ->setReturnUrl('http://return.url');

        $customer = (new Customer())
            ->setName('First', 'Last')
            ->setEmail('test@example.com')
            ->setCellNumber('0123456789');

        $transaction = new Transaction($merchant, 10000, 'Item Name');
        $transaction->setCustomer($customer)
            ->setPaymentId('PAYID1')
            ->setDescription('Item Description')
            ->setCustomStrings(['S1', 'S2', 'S3', 'S4', 'S5'])
            ->setCustomIntegers([1, 2, 3, 4, 5])
            ->setEmailConfirmation(true)
            ->setEmailConfirmationAddress('confirm@example.com')
            ->setPaymentMethod(PaymentMethod::CC);

        self::assertSame([
            'merchant_id',
            'merchant_key',
            'return_url',
            'cancel_url',
            'notify_url',
            'name_first',
            'name_last',
            'email_address',
            'cell_number',
            'm_payment_id',
            'amount',
            'item_name',
            'item_description',
            'custom_int1',
            'custom_int2',
            'custom_int3',
            'custom_int4',
            'custom_int5',
            'custom_str1',
            'custom_str2',
            'custom_str3',
            'custom_str4',
            'custom_str5',
            'email_confirmation',
            'confirmation_address',
            'payment_method',
        ], array_keys($transaction->attributes()));
    }
}
