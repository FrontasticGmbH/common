# common Changelog

## `1.1.3` (2020-08-05)

* Fixed release script

## `1.1.1` (2020-07-30)

* fix: Logger is still unavailable in container.
* chore: Removed FactoryServiceLocator from libraries/common.
* feat(FT34): Log ignored errors in CommerceTools AccountApi.
* fix(FT-33): Remove cart_id from session on checkout.
* fix: Resilience for empty cart ID & private -> protected.
* refactor: rename properties
* fix: removes preprocessing from SVG files
* refactor: remove command class
* refactor: extract method parameters from command
* refactor: extract method
* chore: remove unused import
* fix: extract details from commercetools response
* feat(FT-23): add CartApi::updatePayment
* feat(FT-23): add details to Payment entity
* fix: hands over locale also to second attempt and only stripes the cart
* chore: add documentation for domain object
* fix: use compatible ramsey/uuid in common
* fix: install ramsey/uuid in libraries/common
* feat: Mapped phone and state on Address response
* fix: Included phpstan notation to ignore static call
* feat: update default addresses at Commercetools
* fix: Adjusted BaseImplementation of Cart Decorator to recent BC break.
* fix: CommerceTools AccountApi expect birthdate to be always set.
* feat!: custom fields
* considering path as well
* Throw duplicate account exception in new SAP versions
* Refactored name BaseObject by ApiDataObject
* Changed updateWithProjectSpecificData to non static method
* Created method to update BaseObject
* Removed type filter on Address creation
* Ignored additional attributes on BaseObject
* Extracted address mapping to new AccountMapper
* Used newWithProjectSpecificData for create DO and pass it to POST action
* Created BaseObject and extend it in required DOs
* Returned client on SapAccountApi::getDangerousInnerClient
* AccountApi should not depend on context
* BC: Rename confirmation token route parameter
* Expose additional category filter properties
* Added field `state` and mapping for CommerceTools in `Address`.

## `1.1.0` (2020-07-30)

## `1.0.0` (2020-05-27)


* Initial stable release
