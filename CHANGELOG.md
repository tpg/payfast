# Changelog

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
