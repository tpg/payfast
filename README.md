# PayFast Library

![Run Tests](https://github.com/tpg/payfast/workflows/Run%20Tests/badge.svg)

A simple PayFast library.

---
## Installation
Install the PayFast library through composer by adding it to your `composer.json` file:

```json
{
    "require": {
        "thepublicgood/payfast": "1.x-dev"
    }
}
```

Or install using the command line:

```
composer require thepublicgood/payfast
```

# Usage

## Merchant

```php
$merchant = new \TPG\PayFast\Merchant('MERCHANT_ID', 'MERCHANT_KEY');

$merchant
    ->setReturnUrl($returnUrl)
    ->setCancelUrl($cancelUrl)
    ->setNotifyUrl($notifyUrl);
```

## Customer

```php
$customer = new \TPG\PayFast\Customer();

$customer
    ->setName('First', 'Last')
    ->setEmail('email@test.com')
    ->setCellNumber('1234567890');
```

## Transaction

```php
$transaction = new \TPG\PayFast\Transaction($merchant, 10000, 'Item Name');

$transaction
    ->setCustomer($customer)
    ->setMerchantPaymentId('PAYID123')
    ->setDescription('Item Description')
    ->setCustomIntegers([
        1,
        2,
        3,
        4,
        5,
    ])
    ->setCustomStrings([
        'S1',
        'S2',
        'S3',
        'S4',
        'S5'
    ])
    ->setEmailConfirmation(true)
    ->setEmailConfirmationAddress('email@test.com')
    ->setPaymentMethod(\TPG\PayFast\PaymentMethod::ALL);
```

## Creating a form
PayFast does not require that a passphrase be set. However, to enforce secure behaviour, this library REQUIRES a
passphrase. You'll need to set one on your PayFast account before you can process transactions.

Create a new `PayFast` instance and pass in the transaction and your passphrase string. We can now generate a simple
HTML form which can be placed in your view. The form ID is always `#payfast_form` so you can refer to it using a bit
of JavaScript, or you can pass an integer value to the `form` method to automatically submit the form after that number
of seconds have elapsed.

```php
$payfast = new \TPG\PayFast\PayFast($transaction, 'passphrase');

$submissionDelay = 10; // seconds to wait before automatically submitting the form.
$form = $payfast->form($submissionDelay);

echo $form;
```

## Validating ITN
Once a transaction has ben submitted to PayFast and you've set a notify URL, you can validate the ITN that comes back
from PayFast using the `ItnValidator` class.

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
        $pfid = $response->payfastPaymentId();   // PayFast's payment ID
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

## Testing
Payfast provides a simple sandbox against which transactions can be tested. The sandbox can be found here:
https://sandbox.payfast.co.za. In order to use the sandbox, you'll need to tell the library that you're testing. You
can do so using the `testing()` method when on the `Payfast` instance when creating a form:

```php
$payfast = new \TPG\PayFast\PayFast($transaction, 'passphrase');

$form = $payfast->testing()->form();
```

This will ensure that requests are sent to the sandbox and not the actual PayFast endpoint. The same is true for the
`ItnValidator`:

```php
$validator = new \TPG\PayFast\ItnValidator($request->input());
$valid = $validator->testing()->validate(10000, $passphrase, $request->ip());
```
