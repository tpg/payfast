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
    ->setPaymentId('PAYID123')
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

```php
$payfast = new \TPG\PayFast\PayFast($transaction, 'passphrase');

$submissionDelay = 10; // seconds
$form = $payfast->form($submissionDelay);

echo $form;
```

## Validating ITN

```php
namespace App\Http\Controllers;

class PayFastController
{
    public function webhook(Request $request)
    {
        $validator = new \TPG\PayFast\ITNValidator($request->get());
    }
}
```
