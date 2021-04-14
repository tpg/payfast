<?php

declare(strict_types=1);

namespace TPG\PayFast\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use TPG\PayFast\Customer;
use TPG\PayFast\Merchant;
use TPG\PayFast\PaymentMethod;
use TPG\PayFast\Subscription;
use TPG\PayFast\Transaction;

class SubscriptionTest extends TestCase
{
    /**
     * @test
     **/
    public function it_can_create_a_new_subscription_transaction(): void
    {
        $merchant = new Merchant('ID', 'KEY', 'PASSPHRASE');

        $transaction = new Transaction($merchant, 10000, 'Subscription');
        $transaction->subscription();

        self::assertEquals(array_merge(
            $merchant->attributes(),
            [
                'amount' => '100.00',
                'item_name' => 'Subscription',
                'email_confirmation' => 0,
                'subscription_type' => 1,
                'billing_date' => (new \DateTime())->format('Y-m-d'),
                'recurring_amount' => '100.00',
                'frequency' => 3,
                'cycles' => 0,
            ]
        ), $transaction->attributes());
    }

    /**
     * @test
     **/
    public function it_can_fetch_a_subscription_object(): void
    {
        $token = '5de7ed4c-0326-d453-f812-f338a40846f1';
        $success = [
            'code' => 200,
            'status' => 'success',
            'data' => [
                'response' => [
                    'amount' => 10000,
                    'cycles' => 0,
                    'cycles_complete' => 1,
                    'frequency' => 3,
                    'run_date' => (new \DateTime())->format(DATE_ATOM),
                    'status' => 1,
                    'status_reason' => '',
                    'status_text' => 'ACTIVE',
                    'token' => $token,
                ],
            ],
        ];

        $merchant = new Merchant('ID', 'KEY', 'PASSPHRASE');

        $handler = new MockHandler([
            new Response(200, [], json_encode($success, JSON_THROW_ON_ERROR)),
        ]);

        $client = new Client([
            'handler' => $handler,
        ]);

        $subscription = new Subscription($merchant, $token, $client);
        $subscription->testing()->fetch();

        self::assertSame(3, $subscription->frequency());
        self::assertSame(1, $subscription->cyclesComplete());
    }

    /**
     * @test
     **/
    public function it_can_pause_a_subscription(): void
    {
        $token = '5de7ed4c-0326-d453-f812-f338a40846f1';
        $merchant = new Merchant('ID', 'KEY', 'PASSPHRASE');

        $success = [
            'code' => 200,
            'status' => 'success',
            'data' => [
                'response' => true,
            ]
        ];

        $handler = new MockHandler([
            new Response(200, [], json_encode($success, JSON_THROW_ON_ERROR)),
        ]);

        $client = new Client([
            'handler' => $handler,
        ]);

        $subscription = new Subscription($merchant, $token, $client);
        $subscription->pause();

        self::assertTrue($subscription->paused());
    }

    /**
     * @test
     **/
    public function it_can_unpause_a_subscription(): void
    {
        $token = '5de7ed4c-0326-d453-f812-f338a40846f1';
        $merchant = new Merchant('ID', 'KEY', 'PASSPHRASE');

        $success = [
            'code' => 200,
            'status' => 'success',
            'data' => [
                'response' => true,
            ]
        ];

        $handler = new MockHandler([
            new Response(200, [], json_encode($success, JSON_THROW_ON_ERROR)),
        ]);

        $client = new Client([
            'handler' => $handler,
        ]);

        $subscription = new Subscription($merchant, $token, $client);
        $subscription->unpause();

        self::assertFalse($subscription->paused());
    }

    /**
     * @test
     **/
    public function it_can_cancel_a_subscription(): void
    {
        $token = '5de7ed4c-0326-d453-f812-f338a40846f1';
        $merchant = new Merchant('ID', 'KEY', 'PASSPHRASE');

        $success = [
            'code' => 200,
            'status' => 'success',
            'data' => [
                'response' => true,
            ]
        ];

        $handler = new MockHandler([
            new Response(200, [], json_encode($success, JSON_THROW_ON_ERROR)),
        ]);

        $client = new Client([
            'handler' => $handler,
        ]);

        $subscription = new Subscription($merchant, $token, $client);
        $subscription->cancel();

        self::assertTrue($subscription->cancelled());
    }

    /**
     * @test
     **/
    public function it_can_update_a_subscription()
    {
        $token = '5de7ed4c-0326-d453-f812-f338a40846f1';
        $merchant = new Merchant('ID', 'KEY', 'PASSPHRASE');

        $success = [
            'code' => 200,
            'status' => 'success',
            'data' => [
                'response' => [
                    'amount' => 10000,
                    'cycles' => 10,
                    'cycles_complete' => 1,
                    'frequency' => 3,
                    'run_date' => (new \DateTime())->format(DATE_ATOM),
                    'status' => 1,
                    'token' => $token,
                ],
            ]
        ];

        $handler = new MockHandler([
            new Response(200, [], json_encode($success, JSON_THROW_ON_ERROR)),
        ]);

        $client = new Client([
            'handler' => $handler,
        ]);

        $subscription = new Subscription($merchant, $token, $client);
        $subscription->update([
            'cycles' => 10,
        ]);

        self::assertSame(10, $subscription->cycles());
    }
}
