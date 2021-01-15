<?php

declare(strict_types=1);

namespace TPG\PayFast\Tests;

use TPG\PayFast\Customer;

class CustomerTest extends TestCase
{
    /**
     * @test
     **/
    public function it_can_create_a_new_customer(): void
    {
        $customer = (new Customer())
            ->setName('First', 'Last')
            ->setEmail('email@test.com')
            ->setCellNumber('1234567890');

        self::assertEquals([
            'name_first' => 'First',
            'name_last' => 'Last',
            'email_address' => 'email@test.com',
            'cell_number' => '1234567890',
        ], $customer->attributes());
    }
}
