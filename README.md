[![Run Tests](https://github.com/tpg/payfast/actions/workflows/php.yml/badge.svg)](https://github.com/tpg/payfast/actions/workflows/php.yml)

# Payfast

A simple PayFast library.

# Installation

Install the PayFast library through composer by adding it to your `composer.json` file:

```json
{
    "require": {
        "thepublicgood/payfast": "1.x-dev"
    }
}
```

Or install using the command line:

```bash
composer require thepublicgood/payfast
```

# Usage

PayFast doesn't currently have an on-site payment solution in production. There is a beta service available, but this library does not support that. When the service is in production and support has been added to the sandbox environment, then I'll update this library. Until then, this library only supports the PayFast custom integration option.

## Merchant

All transactions require a merchant object. PayFast will provide you with your merchant ID and merchant Key. You will also need to log into your PayFast account and set a passphrase. Although not required by PayFast, this library requires a passphrase to be set.

Create a new merchant object from the `Merchant` class and pass your authentication data in. You can set the return URL, cancel URL and notify URL on the Merchant instance. You'll want to set all of these to endpoints at your website.

```php
$merchant = new \TPG\PayFast\Merchant('MERCHANT_ID', 'MERCHANT_KEY', 'PASSPHRASE');

$merchant
    ->setReturnUrl($returnUrl)
    ->setCancelUrl($cancelUrl)
    ->setNotifyUrl($notifyUrl);
```

Since PayFast will need to have access to these URLs, during testing it can be useful to have access to your test environment. Take a look at [Expose](https://beyondco.de/docs/expose/introduction) if you need this.

## Customer

A customer is not required for any transaction. However, if you'd like to set this data, you can do so by creating a new `Customer` instance and setting the name, email and cell number. This can help improve the customer experience if the user has registered an account with PayFast.

```php
$customer = new \TPG\PayFast\Customer();

$customer
    ->setName('First', 'Last')
    ->setEmail('email@test.com')
    ->setCellNumber('1234567890');
```

## Transactions

Transactions are where all the magic happens. The `Transaction` class constructor accepts three parameters: the `Merchant` instance, the value of the transaction (in South African cents) and the name of the item. The name could be some reference to the transaction so users can see what they're paying for on the PayFast website.

```php
$transaction = new \TPG\PayFast\Transaction($merchant, 10000, 'Item Name');
```

Once you have a transaction object, you can make a number of changes:

```php
$transaction
    ->setCustomer($customer)                  // Set a customer
    ->setMerchantPaymentId('PAYID123').       // A payment reference
    ->setDescription('Item Description')      // A payment description
    ->setCustomIntegers([                     // Up to 5 custom integers
        1,
        2,
        3,
        4,
        5,
    ])
    ->setCustomStrings([                     // Up to 5 custom strings
        'S1',
        'S2',
        'S3',
        'S4',
        'S5'
    ])
    ->setEmailConfirmation(true)            // Where to send email confirmations
    ->setEmailConfirmationAddress('email@test.com')  // The confirmation email
    ->setPaymentMethod(\TPG\PayFast\PaymentMethod::ALL); // Payment method
```

The payment method is just a way to limit what payment methods you accept. In most cases you'll probably want `PaymentMethod::ALL`, but there are a few others:

```php
PaymentMethod::ALL;  // All payment methods allowed
PaymentMethod::CC;   // Credit Cards
PaymentMethod::DC;   // Debit cards
PaymentMethod::EFT;  // EFT
PaymentMethod::MP;   // MasterPass
PaymentMethod::MC;   // Mobicred
PaymentMethod::SC;   // SCode
```

There is no way to allow a combination of these. It's either all or one.

## Creating a form

Create a new `PayFast` instance and pass in the transaction. We can now generate a simple HTML form which can be placed in your view. The form ID is always `#payfast_form` so you can refer to it using a bit of JavaScript, or you can pass an integer value to the `form()` method to automatically submit the form after that number of seconds have elapsed.

```php
$payfast = new \TPG\PayFast\PayFast($transaction);

$submissionDelay = 10; // seconds to wait before automatically submitting the form.
$form = $payfast->form($submissionDelay);

echo $form;
```

If you don't supply a delay, you will need to submit the form yourself. Remember that you should not display this form to the end user and all the form fields are of type "hidden".

## Validating the ITN

Once a transaction has ben submitted to PayFast and you've set a notify URL, you can validate the ITN that comes back from PayFast using the `ItnValidator` class. PayFast recommend setting a header right away and then continuing with the validation process.

```php
namespace App\Http\Controllers;

class PayFastController
{
    public function webhook(Request $request)
    {
        // From the PayFast docs... Send a 200 response right away...
        header('HTTP/1.0 200 OK');
        flush();
    
        // Create a new validator
        $validator = new \TPG\PayFast\ItnValidator($request->input());
        
        // You have access to all the response data through the `PayfastResponse` class.
        $response = $validator->response();
        
        $mpid = $response->merchantPaymentId();  // Original payment ID set on the transaction
        $pfid = $response->payFastPaymentId();   // PayFast's payment ID
        $name = $response->name();           // Item name or order number
        $description = $response->description();    // Item or order description
        $gross = $response->amountGross();        // Total charge
        $fee = $response->amountFee();          // Payfast fee amount
        $net = $response->amountNet();          // Net amount
        $integer = $response->customIntegers();    // Array of custom integers
        $string = $response->customStrings();     // Array of custom strings
        
        $firstName = $response->customer()->firstName();      // Customers first name
        $lastName = $response->customer()->lastName();       // Customers last name
        $emailAddress = $response->customer()->emailAddress();   // Customers email address
        $cellNumber = $response->customer()->cellNumber();     // Customers cell number
        
        $signature = $response->signature();                  // Signature for validation
        
        //--------------------
        
        // To validate the transaction, first ensure the transaction is COMPLETE:
        if ($response->paymentStatus() !== \TPG\PayFast\PaymentStatus::COMPLETE) {
            // incomplete...
        }
        
        // Then `validate()` will return true or throw an exception
        $valid = $validator->validate(10000, $passphrase, $request->ip());
        
        if (!$valid) {
            echo $validator->error();
        }
        
        // validated!
    }
}
```

## Subscriptions

Subscriptions are start in the same way as standard transactions. Simply add a call to `subscription()` on the `Transaction` instance:

```php
$transaction = new Transaction($merchant, 10000);
$transaction->subscription();
```

This will ensure the transaction is passed to PayFast as a recurring transaction. The `subscription` method also takes a few options to customise the subscription. You can specify the frequency, the number of cycles and the billing date:

```php
$transaction->subscription(
    Transaction::SUBSCRIPTION_FREQUENCY_QUARTERLY,  // frequency
    10,                                             // number of cycles
    new DateTime('tomorrow'),                       // Billing start date
);
```

PayFast supports four frequency options:

```php
$monthly = Transaction::SUBSCRIPTION_FREQUENCY_MONTHLY;  // default
$quarterly = Transaction::SUBSCRIPTION_FREQUENCY_QUARTERLY;
$biannually = Transaction::SUBSCRIPTION_FREQUENCY_BIANNUALLY;
$annually = Transaction::SUBSCRIPTION_FREQUENCY_ANNUALLY;
```

The `cycles` parameter defaults to 0 meaning indefinite. The subscription will continue until cancelled.

Once you've submitted the transaction, you can use the `token()` method on the `ItnValidator` instance to get a token reference for the transaction which can then be used to manage that subscription:

```php
$validator = new ItnValidator($request->input());

if ($validator->validate(10000, 'passphrase', $request->ip()) {
    $token = $validator->token();
}
```

### Fetching a subscription from PayFast

You can fetch details for any subscription using the `Subscription` class. Pass a `Merchant` instance as the first parameter and the subscription token as the second to the constructor and call the `fetch` method:

```php
$subscription = new Subscription($merchant, $token);
$subscription->fetch();

$data = $subscription->toArray();
```

There are number of features on the `Subscription` class which you can use to manage any subscription:

### Pausing/Unpausing a subscription

You can pause a subscription for any number of cycles, but by default a subscription is paused for just 1. The next billing cycle will then be skipped. You get the next billing date using the `runDate` method on the `Subscription` object.

```php
$subscription->pause();

$subscription->fetch()->runDate();  // Will skip the next billing date

//---------------------------------------

$subscription->pause(2);

$subscription->fetch()->runDate(); // Will skip the next two billing dates
```

Note that PayFast does not allow you to alter the number of cycles paused here. You will need to `unpause` and then `pause` again with the new cycles.

To unpause a subscription, simply call the `unpause()` method:

```php
$subscription->unpause();
```

To check if a subscription is paused, the `paused()` method will return true.

```php
$subscription->pause();
$subscription->paused();  // true
```

### Cancelling a subscription

To cancel a subscription, simply call the `cancel()` method:

```php
$subscription->cancel();
$subscription->cancelled();  // true
```

PayFast retains the information about cancelled transaction, so even if you fetched data from a transaction that had been previously cancelled, you'll still get that transaction data, but `cancelled()` will return `true`.

## Sandbox

PayFast provides a simple sandbox against which transactions can be tested. The sandbox can be found at [https://sandbox.payfast.co.za](https://sandbox.payfast.co.za). In order to use the sandbox, you'll need to tell the library that you're testing. You can do so using the `testing()` method when on the `Payfast` instance when creating a form:

```php
$payfast = new PayFast($transaction);

$form = $payfast->testing()->form();
```

This will ensure that requests are sent to the sandbox and not the actual PayFast endpoint. The same is true for the `ItnValidator`:

```php
$validator = new ItnValidator($request->input());
$valid = $validator->testing()->validate(10000, $passphrase, $request->ip());
```

And when managing subscriptions:

```php
$subscription = new Subscription($merchant, 'TOKEN');
$subscription->testing()->pause();
```
