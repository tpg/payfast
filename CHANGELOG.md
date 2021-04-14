# Changelog

## [0.1.0] 14-04-2021
### Changed
* Documentation updated.
* Changed the `Merchant` constructor now takes 3 parameters. The third being the required passphrase.
* Changed the `PayFast` constructor will no longer accept the passphrase as the second parameter.
* Changed attributes will be included if they're specifically NULL and not falsy.
* Changed the `Signature` constructor will now accept an array of attributes as the first parameter instead of a `Transaction` instance.
* Changed the `generate` method on the `Signature` class now accepts a boolean parameter to sort the attributes (required by PayFast API).
* Changed some dependency updates.
  
### Added
* Added a `Subscription` class to represent a PayFast subscription object.
* Added a `subscription` method to the `Transaction` class to set a transaction as recurring.
* Added a `recurringAmount` method to `Transaction`.
* Added a `merchant` method to `Transaction` to get the set merchant instance.
* Added a `merchantId()` method to `Merchant` class to get the set ID.
* Added a `merchantKey()` method to `Merchant` class to get the set key.
* Added a `passphrase()` method to `Merchant` class to get the set passphase.
* Added a `token()` method to the `ItnValidator` and the `PayfastResponse` classes for when tokens are returned from PayFast.
* Added a new `PayfastException` for errors returned by PayFast.

### Removed
* Remove the paused check when unpausing a subscription.

## [0.0.1] 16-01-2021
### Added
* Added method to be able to fetch customer data from a `Customer` instance. There are new `firstName()`, `lastName()`, `emailAddress()` and `cellNumber()` methods.
* Added the `ItnValidator` class. Call the `validate()` method to validate the response from PayFast.
* Added a `PayfastResponse` class to represent the response from the processor.
* Added a `PaymentStatus` class which just has some useful class constants.
* Added a `Transaction::amount()` method to return the transaction total amount.


### Changed
* The first parameter of the `Customer->setName()`  method is now optional.
* Changes to the README file to reflect usage changes.
* Renamed the `Transaction::paymentId` member to `merchantPaymentId`.

## [0.0.0] 15-01-2021
### Added
* Project setup 
