# Changelog

## v0.5.0 - 2024-07-15

Really just a few dependency updates. Nothing else.

## v0.4.0 - 2023-04-12

PayFast frustrates me somewhat. Anyway...

- Dropped support for PHP before v8.1.
- PayFast appear to be throwing errors when including an empty JSON string. The `Request->make()` method has been updated to set the body to `null` if no array is provided. This does away with the error from PayFast

## v0.3.4 - 2023-03-09

- Laravel 10 support.
- Dropped support for PHP 7.4.

## v0.3.3 - 2023-03-06

- [Bug] Fixed a bug in the request that was not including the body parameters in the signature.
- [Change] Changed the request body from `application/x-www-form-urlencoded` to `application/json`.

## [0.3.2] 29-06-2022

- Updated the PayFast validation hostname to include the "www." prefix.

## [0.3.1] 25-05-2022

### Fixed

- Error when making subscription API requests. The endpoint is incorrect.

# [0.3.0] 07-04-2022

### Changed

- Updated to support Laravel 9.

## [0.2.5] 26-01-2022

### Fixed

- Exception thrown when using `ItnValidator->validate()` without calling `testing()`. The validator will now assume testing is `false`.

## [0.2.3] 27-08-2021

### Fixed

- When posting to PayFast in production, the url should be `www.payfast.co.za` (with the "www" bit).

### Changed

- If no email confirmation is required, don't include the `email_confirmation` attribute with a false value.

## [0.2.2] 20-05-2021

### Changed

- Fixed a bug where the `setRecurringAmount` method on the `Transaction` class had a missing return value.
- Updated the `Subscription->request()` method to include the thrown exception when not able to communicate with PayFast.

## [0.2.1] 20-05-2021

### Changed

- The `ItnValidator->getParamString()` method's visibility has been changed from `protected` to `public`.

## [0.2.0] 20-05-2021

### Changed

- The `PayfastResponse` class has been renamed to `PayFastResponse` (uppercase "F").
- The `PayFastResponse->payfastPaymentId()` method has been renamed to `payFastPaymentId()`.

### Added

- Added a `billingDate` method to `PayFastResponse`.

## [0.1.0] 14-04-2021

### Changed

- Documentation updated.
- Changed the `Merchant` constructor now takes 3 parameters. The third being the required passphrase.
- Changed the `PayFast` constructor will no longer accept the passphrase as the second parameter.
- Changed attributes will be included if they're specifically NULL and not falsy.
- Changed the `Signature` constructor will now accept an array of attributes as the first parameter instead of a `Transaction` instance.
- Changed the `generate` method on the `Signature` class now accepts a boolean parameter to sort the attributes (required by PayFast API).
- Changed some dependency updates.

### Added

- Added a `Subscription` class to represent a PayFast subscription object.
- Added a `subscription` method to the `Transaction` class to set a transaction as recurring.
- Added a `recurringAmount` method to `Transaction`.
- Added a `merchant` method to `Transaction` to get the set merchant instance.
- Added a `merchantId()` method to `Merchant` class to get the set ID.
- Added a `merchantKey()` method to `Merchant` class to get the set key.
- Added a `passphrase()` method to `Merchant` class to get the set passphase.
- Added a `token()` method to the `ItnValidator` and the `PayfastResponse` classes for when tokens are returned from PayFast.
- Added a new `PayfastException` for errors returned by PayFast.

### Removed

- Remove the paused check when unpausing a subscription.

## [0.0.1] 16-01-2021

### Added

- Added method to be able to fetch customer data from a `Customer` instance. There are new `firstName()`, `lastName()`, `emailAddress()` and `cellNumber()` methods.
- Added the `ItnValidator` class. Call the `validate()` method to validate the response from PayFast.
- Added a `PayfastResponse` class to represent the response from the processor.
- Added a `PaymentStatus` class which just has some useful class constants.
- Added a `Transaction::amount()` method to return the transaction total amount.

### Changed

- The first parameter of the `Customer->setName()`  method is now optional.
- Changes to the README file to reflect usage changes.
- Renamed the `Transaction::paymentId` member to `merchantPaymentId`.

## [0.0.0] 15-01-2021

### Added

- Project setup
