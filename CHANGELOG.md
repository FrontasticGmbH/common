# common Changelog

## `1.1.11` (2020-09-17)

* feat!: Use the product search API
* feat: Add ShopifyProductSearchApi
* feat: Call product event listeners
* chore: Extracted common webspocket code

## `1.1.10` (2020-09-15)

* Regenerated API documentation
* Generated TypeScript types for catwalk & common domain models

## `1.1.9` (2020-09-11)

* Implement localization and include Product filter by language
* add Spryker product search API
* add Shopware product search API
* add SAP product search API
* Add query validator and throw on unsupported features
* Add result facet mapping and refactor transformations fully into mapper
* Deprecate Tastics not wrapped into tastify()
* add feature flag for consistency between product and product search api
* Implement Findologic pagination
* Removed lodash dependency
* Build cloudinary URLs ourselves
* Add FindologicBundle

## `1.1.8` (2020-08-24)

* chore(shopify integration): Included pagination documentation
* fix(shopify integration): Updated Api test to use cursor based pagination
* feat(shopify integration): Implemented cursor-based pagination with previousCursor and nextCursor
* fix(shopware): throw not found exception on invalid ID
* feat: Included custom query fields on ProductApi::getProduct()
* fix(shopify integration): Returned concrete Result class on ProductApi::queryCatergories()
* fix(shopify integration): ensured sku content in each variant
* feat(shopify integration): handled non existing product response
* feat(shopify integration): Included test flag on Spryker API test config
* feat(shopify integration): Implement nextCursor and previousCursor for pagination
* feat(shopify integration): Included filter on ProductApi:Query to allow pagination
* feat(shopify integration): Included field to allow backward pagination
* feat: Implement queryCategories in SprykerProductApi
* fix(spyrker integration): Commented out SprykerProjectApi endpoint request
* fix(spryker integration): Removed multi languages comments from SprykerProjectApi
* fix(spryker integration): Defined SessionService and set right usage
* fix(spryker integration): Removed usage of Account sesion in favour of session_id
* fix(spryker integration): Used session_id to build the AnonymousHeader
* fix(spryker integration): Fix url to use cartId
* fix: fetch total product count from Shopware
* fix(spryker integration): Promoted AccountHelper as public service
* fix(spryker integration): Removed FactoryServiceLocator on WishlistApiFactory
* fix(spryker integration): Replaced missing factoryServices by containers
* fix(spryker integration): Removed missing orphan DI tags
* fix(spryker integration): Replace factoryLocator by container for DI
* feat(shopify integration): Added Category API tests
* fix(shopify integration): Added missing import
* fix(shopify integration): Rollback offset deprecation
* feat(shopify integration): Implemented new endpoint ProductApi::queryCategories
* feat(shopify integration): Implemented ProductApi::getCategories
* feat(shopify integration): Implemented ProductApi:getProductTypes endpoint
* feat(shopify integration): Implement ProductApi::getProduct endpoint
* fix(shopify integration): Replaced serviceLocator by container
* feat(spryker integration): Inlcuded filter by category
* fix(shopify intetration): Filter by multiple SKUs
* feat(shopify integration): Added query filters for ProductsAPI
* feat(shopify integatrion): Included API for Product filters
* feat(shopify integration): Set frontastic credentials
* fix(shopify integration): Used newest API version
* feat(shopify integration): Mapped Products and Variants
* feat(shopify integration): Added variables for cursor pagination
* feat(shopify integration): Created basic bundle structure and client
* fix(spryker integration): Removed default language from Spryker Api
* fix(spryker integration): Removed unused decorator
* fix(spryker integration): Removed phpstan ignore tag
* fix(spryker integration): Updated ignore class to cover API test missing class
* fix(spryker integration): Fixed missed style issues
* fix(Spryker integration): Fixed style issues
* feat(spryker integration): Used default Spryker endpoint on API test
* fix(spryker integration): Fixed unused classes
* fix(spryker integration): Fixed typehint error
* fix(spryker integration): Fixed phpstan issues
* fix(spryker integration): Fixed add to cart by customer or guest
* fix(spryker integration): Especified right API client
* feat(spryker integration): Included extension to map concrete products
* feat(spryker integration): Extracted cart config to variable
* feat(spryker integration): Migrated Wishlist API
* feat(spryker integration): Removed salutations from Project API
* feat(spryker integration): Initial migration of Cart API with broken test
* feat(spryker-integration): Fixed minimum Account API endpoint to pass API test
* feat(spryker_integration): Initial migration of Account API
* feat(spryker integration): Migrated Project API
* feat(spryker integration): Impleted WoohooLabs ResourceObject to handle Api response content
* feat(spryker integration): Included raw api output as part of Http/Response

## `1.1.5` (2020-08-05)

* fix: Restore (again) missing CHANGELOG.md in common

## `1.1.4` (2020-08-05)

* fix: Restore missing CHANGELOG.md in common

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
