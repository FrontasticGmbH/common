

## `1.1.3` (2020-08-05)

* Fixed release script

## `1.1.2` (2020-08-05)

* fix: Use readlink instead of realpath to work on mac
* Enable tideways for sparhandy and fix check in setup handle
* chore(frontasticli): only build a release on new tag
* Adapted final message
* Reactivated adyen in release script
* chore(frontasticli): restrict release action to master branch
* fix(boost-theme): using the default theme
* chore: adding lobenberg for now again as the contract is not ended
* fix: enables using correct emojis in MS teams as well
* fix(boost-theme): product teaser - clickable
* feat(boost-theme): theming - next phase
* chore(frontasticli): Add github action for releasing
* fix(boost-theme): custom hook and some photo scaling
* fix(boost-theme): custom hook and some photo scaling (solution for now, until we get more customized photos from commerce tools)
* chore: removing customers that have github actions by now
* chore: enabling teams hook for Apollo and disabling Slack
* chore: Remove orphan DI tag frontastic.common.api_integration.

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
