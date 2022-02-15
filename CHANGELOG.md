# common Changelog

## `2.25.2` (2022-02-15)

* fix(FP-775): Minor refactor, import of SprykerAddress
* fix(FP-755): Fix Spryker address

## `2.25.1` (2022-01-27)

* fix: if there's no filter chosen - the error reappears

## `2.25.0` (2022-01-24)

* fix(FP-838): improved error handling on parse locator for Shopware
* feat(FP-838): prevent update email, shipping address, or billing address on Shopware CartApi
* feat(FP-838): implemented set shipping and billing address on Shopware CartApi
* feat(FP-838): map discounts and shipping info on Shopware CartApi
* feat(FP-838): map shipping info from order on Shopware CartApi
* feat(FP-838): upgrade order, anonymous cart, and email on Shopware CartApi
* feat(FP-838): allow customer guest create and update emal on Shopware AccountApi
* feat(FP-838): upgraded discount actions on Shopware CartApi
* feat(FP-838): upgrade shipping methods actions on Shopware CartApi
* feat(FP-838): upgrade add, update, and remove item to cart on Shopware CartApi
* feat(FP-838): upgrade get anonymous cart and get cart by id on Shopware

## `2.24.0` (2022-01-05)

* feat(FP-324): Include User-Agent Frontastic as part of API requests

## `2.23.0` (2021-12-16)

* feat: Created method for CustomDataSourceValidation in the CustomDataSourceService
* feat: Store the stream type for stream fields
* feat: Introduced a special field configuration for stream fields

## `2.22.0` (2021-12-02)

* feat(FP-1193): included rawApiImput on getProduct
* feat(FP-581): refresh account after update address on Shopify
* feat(FP-581): handle existing address error on Shopify
* feat(FP-1193): included rawApiInput fields for queries on Product and ProductSearch API
* fix(FP-1193): added url schema simbols
* feat!(FP-1193): upgrade and fix shopify version to 2021-10

## `2.21.1` (2021-11-30)

* fix: make product price nullable in Algolia integration

## `2.21.0` (2021-10-26)

* !feat(FP-646): implemented traceability strategy adding a correlation-id to all requests and responses
* fix: Nested groups were not visited correctly in ConfigurationSchema.

## `2.20.1` (2021-10-21)

* fix: return a 400 json response on type error of dynamic pages

## `2.20.0` (2021-10-11)


* Catwalk version update

## `2.19.3` (2021-10-07)

* fix(api-hub): disable deprecation notice in prod
* fix(api-hub): only output deprecation notice in non-prod-env
* fix: Keep old previewUrl config working properly
* feat: Allow for different view and endpoint URLs for preview

## `2.19.2` (2021-09-23)

* Always use plain json_decode for decoding CT responses

## `2.19.1` (2021-09-09)

* fix(FP-980): validated result to catch no active carts in Shopify

## `2.19.0` (2021-09-08)

* fix: Keep unknown field values in completed configuration fields.

## `2.18.2` (2021-09-04)

* fix: Handle documentary fields properly.

## `2.18.1` (2021-09-03)

* chore: bumped version required of frontastic/catwalk to 1.17 

## `2.18.0` (2021-09-03)

* feat(FP-935): Submit current field path to visitors.
* feat(fp-935): Fixed potential construction issue through `new static()`
* feat(fp-935): Visitor that executes multiple visitors.
* feat(fp-935): Test visitors are called correctly.
* feat(fp-935): Retrieval of complete values + infra to visit these values.
* feat(fp-935): Translatable support in backend schema implementation.
* chore(fp-935): Removed orphan method.
* feat(fp-935): Renamed PHP test suite.
* feat(fp-935): Ensure compatibility of schema handling with groups.
* feat(fp-935): Implemented dedicated handling of "group" schema fields.
* feat(fp-935): Specialization classes for schema value types.
* feat(fp-935): Adjust behavior of non-existing field value request to JS impl.
* feat(fp-935): Regression test for PHP implementation of ConfigurationSchema.
* feat(fp-935): Extracted regression test into dedicated file.
* feat(fp-935): Migrated more tests to code lang independent format.
* feat(fp-935): Extracted first config tests into code independant format.

## `2.17.2` (2021-08-26)

* fix(FP-932): modified array key validation
* feat(FP-932): mapped missing variant properties in Algolia

## `2.17.1` (2021-08-26)

* chore: bumped version required of frontastic/catwalk to 1.16

## `2.17.0` (2021-08-26)

* feat(FP-932): get default language from project config in Algolia
* feat(FP-932): set algolia library as suggested on composer
* feat(FP-932): loop over attributes to get searchable attributes in Algolia
* fix(FP-932): prevent filtering if there are no terms
* feat(FP-932): handled multilanguage query in Algolia
* feat(FP-932): allowed multilanguages on Algolia client
* feat(FP-932): extracted mappers to dedicated class in Algolia
* feat(FP-932): used offset pagination in Algolia
* feat(FP-932): implemented selected facets in Algolia
* feat(FP-932): included and handled text attributes as searchables in Algolia
* feat(FP-932): missing file for the implemented facet filters in Algolia
* feat(FP-932): implemented facet filters in Algolia
* feat(FP-932): implemented query price filter for Algolia
* feat(FP-932): return all facets and price as searchable attributes on Algolia
* feat(FP-932): ignore productId and sku from product facets and searchable attributes
* feat(FP-932): implemented filters for query fields for Algolia
* feat(FP-932): implemented query filter for Algolia
* feat(FP-932): use key label structure for Attributes values
* feat(FP-932): implemented getSearchAttributes for Algolia

## `2.16.2` (2021-08-19)

* Revert "fix: adds frontasticBasicAuthPassword to exclude list"

## `2.16.1` (2021-08-17)

* fix: adds frontasticBasicAuthPassword to exclude list

## `2.16.0` (2021-08-04)

* feat(FP-839): used integer for price filters
* feat(FP-839): included price filter and facets on Shopware
* fix(FP-839): validated terms only in filters that might content them
* chore(FP-839): renamed handle parser used for Facets and Filters
* feat(FP-839): pased handler on filter creation
* feat(custom-data-source): CustomDataSource schema (copy from CustomStream).
* feat(FP-839): get group from common groupId field
* fix(FP-839): reused client request for both queries

## `2.15.0` (2021-07-29)

* feat: enhancing tideways logging for GraphCMS adding Cache status header as well as RequestId

## `2.14.0` (2021-07-20)

* feat(FP-837): set API test credential for Shopware 6.4
* feat(FP-837): used php comparation for API version on Shopware
* fix(FP-837): added address on refresh account on Shopware
* fix: Move NextJsBundle to correct component.
* fix(FP-837): used address mapper
* chore(FP-837): added versioning docs
* feat(FP-837): handled different api version on Shopware client
* feat: Bundle for NextJs
* feat(FP-837): migrated AccountApi to store-api on Shopware
* feat(FP-837): handle elements and data fiels on Account and Project mappers on Shopware
* feat(FP-837): upgraded project config endpoints to store-api on Shopware

## `2.13.4` (2021-07-13)

* fix: strip API version on Shopware store-api
* chore: replaced authToken by apiToken on Shopify
* chore: replaced authToken by apiToken on Spryker

## `2.13.3` (2021-07-08)

* fix(FP-830): replaced authToken by apiToken on Shopware
* feat: deprecated authToken in favor of apiToken.
* fix: specify api version on Shopware

## `2.13.2` (2021-07-06)

* fix(FP-830): keep authToken on refresh account for Shopware

## `2.13.1` (2021-07-01)

* refactor: set Spryker cart test credentials as generic
* fix: Allow empty local to be parsed on Spryker

## `2.13.0` (2021-06-23)

* chore(FT-731): removed unnecessary array_merge
* feat(FP-731): login customer after reset account password on Shopware
* feat(FP-731): implemented reset account password on Shopware
* feat(FP-731): implemented generic auth provider on Shopware
* feat(FP-731): validated default language and currency headers on Shopware
* feat(FP-731): refresed account base on email on Shopware
* feat(FP-731): implemented account creation and confirmation on Shopware

## `2.12.0` (2021-06-15)

* feat: Added Ganesha based curcuit breaker for our HTTP client stack

## `2.11.10` (2021-06-14)

* chore: increased symfony minor version
* fix: implemented AccountApi::getSalutations on Shopware

## `2.11.9` (2021-05-28)

* fix(FP-728): keeped original and masterData as dangerousInner on Commercetools

## `2.11.8` (2021-05-28)

* fix: validated relations before map them in Spryker
* fix: remove scope from client call
* fix: implemented CartApi::setShippingMethod on Shopware integration

## `2.11.7` (2021-05-18)

* fix(FP-709): moved cart validation to Shopware integration
* fix(FP-709): included addresses and email into cart on Spryker
* fix(FP-709): moved cart validation to Commercetools integration

## `2.11.6` (2021-05-13)

* fix(FP-691): returned empty response if content by id is query on GraphCMS

## `2.11.5` (2021-05-11)

* fix(FP-695): validated empty nodes before access data in Shopify
* feat(backstage): latest terminology typo fix
* feat(backstage): latest terminology updates
* fix(backstage): test update
* feat(backstage): latest copy updates
* feat: tastics term in studio

## `2.11.4` (2021-04-29)

* fix(FP-624): moved variant SKU method to Shopify integration
* fix(FP-624): validated product returned when filtered by SKUs in Shopify

## `2.11.3` (2021-04-29)

* misc: Tests for CustomerService.

## `2.11.2` (2021-04-27)

* fix(FP-645): included host and status code to Contentful logs
* fix(FP-645): decorated Contentful client to enhance logger
* fix: allow config environment for Contentful client
* fix(FP-645): set default timeout and logger on Contentful client
* fix: Find version manually

## `2.11.1` (2021-04-22)

* fix: Fix the /api/version SystemController to use version parameter bag
* fix: Fix the /api/version SystemController parameter bag

## `2.11.0` (2021-04-20)

* feat(FP-617): exposed authorization url on project config for commercetools

## `2.10.13` (2021-04-13)

* fix: fixed unit test for Content
* fix(606): included Content slug on GraphCMS
* fix(606): validate content attribute key before fetch
* misc: included API test to place order for logged in user

## `2.10.12` (2021-04-08)

* fix(FP-567): handle client exceptions and skip Union types
* fix(FP-363): Shopify API, use existing account with token and remove returned address on create
* fix(FP-363): API to add address on customer create and unit tests for create update adjustment

## `2.10.11` (2021-03-30)

* fix: Remove phpcpd from all projects

## `2.10.10` (2021-03-23)

* fix(FP-96): migrated and updated implementation for shipping methods on Shopware
* fix(FP-84): used alias on category facet and set missing definition
* fix: Use proper AccessDeniedException

## `2.10.9` (2021-03-10)

* fix(FP-458): used config from engine and include query as part of Result
* chore(FP-458): removed unused max offset
* fix(FP-458): exposed max offset in project.yml and validated before query
* fix(FP-458): created maximum offset and validate Product queries againts it on Commercetools

## `2.10.8` (2021-03-09)

* chore(FP-447): improved documentation and log messages
* fix(FP-447): throw and handle Cart not active exception
* fix(FP-447): get existing cart for logged in customer in Shopify

## `2.10.7` (2021-03-04)

* fix(FP-447): cart regenerated if is already completed on Shopify
* fix: validate version and category index before map

## `2.10.6` (2021-03-02)

* fix(FP-249): added address on account creation for Commercetools

## `2.10.5` (2021-03-01)

* fix(FP-395): implemented build query for next page and calculate last (#623)
* fix: fix E_NOTICE on product not found

## `2.10.4` (2021-02-25)

* fix: stan errors

## `2.10.3` (2021-02-23)

* fix(FP-389): included discounts on LineItems and mapped data for Commercetools
* fix: removed decoded exceptions in favor of fallback on php native decoder

## `2.10.2` (2021-02-19)

* fix: removed cartApi extra argument from BaseImplementationAdapterV2

## `2.10.1` (2021-02-19)

* fix(FP-364): fixed unit test inlcuding dangerousInnerShippingMethod to expected data
* fix(FP-364): extended shipping method query and map localization zone

## `2.10.0` (2021-02-18)

* feat(FP-81): included documentation of rawApiInput fields from Commercetools on Wishlist
* fix(FP-335): extended Wishlist from ApiDataObject
* feat(FP-81): included documentation of rawApiInput fields from Commercetools
* Feat: JavaScript based Decorators

## `1.9.5` (2021-02-18)

## `2.9.0` (2021-02-16)

* fix(FP-323): mapped discount for product discounts
* fix: added attributes response on after event for getSearchableAttributes
* feat: implemented BaseImplementation v2 and adapter for AccountApi (#605)
* fix: used ContentApi instead of WishlistApi as aggreageted on LifecycleEventDecorator
* feat: included BaseImplementation for ProductSearchApi and deprecated duplicated methods in ProductApi

## `2.8.0` (2021-02-11)

* feat: Update ShopifyCartApi.php
* feat: add compareAtPriceV2 on Shopify product variants

## `2.7.2` (2021-02-11)

* fix: Return empty array to unblock the customer

## `2.7.1` (2021-02-02)

* chore: included the conflict version with catwalk on common/composer.json

## `2.7.0` (2021-02-02)

* !feat(fp-90) catwalk controllers (#580)
* feat: included shipping info id for CommerceTools

## `2.6.1` (2021-01-27)

* fix: removed shipping or billing fields dependency to set shippingMethodName
* fix(FP-150): returned ShippingMethod in getAvailableShippingMethods for Shopify

## `2.6.0` (2021-01-20)

* feat(FT-545): implemented shipping methods for a given cart on Spryker

## `2.5.1` (2021-01-20)

* fix: Missed adaption to OrderIdGeneratorV2.
* feat(FT-590): upgrade Shopify version on API test and include SEO fields on product

## `2.5.0` (2021-01-18)

* fix(563): revert change to tests and add typecasting into Spryker CatalogSearchQuery to ensure that categoryId will be an int
* feat: included metafields on Shopify product variants
* feat(FT-581): mapped rates and locations on ShippingRates for Commercetools
* fix: Consistency for toString() methods on ProductApi\Locale.
* feat: Allow advanced CommerceTools sort orders for ProductSearch.
* feat: Map Contentful content type information.
* feat: Allow access to Mapper & LocaleCreator on CT CartApi implementation.
* fix: Type hint Cart->discounts to be instances of Discount.
* feat: Make remainder getAggregate() methods on Event Decorators public.
* feat: OrderIdGeneratorV2.
* fix: Properly parse BC `option` from lineItems.
* fix: Avoid sending empty shipping method to CommerceTools.
* feat(cart-defaults): Options for CommerceTools cart defaults.
* feat(CartFetcher): CartFetcher as unified entrance to loading a cart.

## `2.4.0` (2021-01-14)

* feat(cart-defaults): Options for CommerceTools cart defaults.
* feat(FT-506): implemented setEmail for Spryker
* fix(FT-457): reset index key on unique images
* fix(FT-563): add if-else condition depending on current customer to change behavior for Spryker
* feat(FT-457): included Shopify product images to each variant

## `2.3.0` (2021-01-06)

* feat(FT-544): implemented cart available shipping methods
* fix(shopify): mapped variant stock from quantityAvailable and included availableForSale
* feat(FT-470): Mapped shipping discounted price when present

## `2.2.1` (2020-12-18)

* fix: removed extra comma at the end of parameter list

## `2.2.0` (2020-12-18)

* fix: added missing default language
* feat(FT-532): extracted Wishlist Api interface
* feat(FT-532): extracted Cart Api interface
* feat(FT-532): extracted Account Api interface
* fix(FT-540): make defaultLanguage in Spryker Product Search API bundle non-nullable, but make localeString in parseLocaleString nullable
* fix(FT-540): Fix SprykerProductSearchApi, add usage of forLanguage to have locale included into products search

## `2.1.0` (2020-12-14)

* Fix missing namespace
* Update json calls around wrapper
* feat(FT-519): returned first zone rate price if not matching
* fix(FT-519): mapped shipping method price
* fix: Default for machineLimit is actually set during parsing.
* fix(FT-519): removed available shipping controller and use method as part of cart info
* fix: Wrap all commands modifying the git index into <retry>
* feat(FT-519): implemented shipping methods controllers
* fix(spryker): removed name mapping from decription
* feat(spryker): implemented redem and remove discount code
* chore: Ignore simdjson_decode not found build failure
* feat: Allow min. 3 Machines per customer.
* [FT-357] Install SIMDjson
* fix: BC mechanism for extraction of ProductSearchApi.
* fix: Pass on data section into projects
* fix: Remaining dependencies to work with common 2.0
* fix: Set composer platform to PHP 7.4

## `2.0.0` (2020-11-27)

* feat(tax): included tax class and implementation fro Commercetools
* feat!(ShippingMethod): API enhancement for fetching shipping methods. (#420)
* Built assets for release 2020.11.26.17.12
* fix(findologic): copy&paste error
* fix(findologic): use differnet shop for tests
* fix(findologic): include properties in query
* fix(findologic): use slugProperty in API tests
* fix(findologic): add missing property
* fix(findologic): only use slug property if present
* fix(findologic): handle missing route collection
* fix: Copy improvements
* fix: Ignore TrackingService from catwalk
* feat(shopify): increased the default number of variants to fetch
* fix: Correct bundle references
* refactor: removed duplicated tests
* [FT-442] Migrate DataObjects to
* feat(Findologic Product Search): Implement Slug Parsing from configured Findologic property field
* fix: set right test name on AnonymousCart
* fix(shopify): set dangerousInner* on missing objects
* feat(shopify): included descriptionHtml and images to Product query
* feat(ApiBase): implemented CartApiBase on Spryker

## `1.1.20` (2020-11-26)

* fix: Correct bundle references
* refactor: removed duplicated tests
* [FT-442] Migrate DataObjects to ApiDataObjects
* fix: set right test name on AnonymousCart

## `1.1.19` (2020-11-13)

* chore: Built release 2020.11.13.11.34
* fix(shopify): Used lowecase for variant attributes
* fix(shopify): increased number of variants retrieved
* fix(spryker): Set default language in CartApi
* fix: set Variant.id and enable SAP test
* fix(shopify): Validated shippingLine data before map shipping methods
* fix: correctly recreate carts shipping info
* fix: don't serialize empty object as array
* fix: assert cart locale on getById when specified
* fix: Replaced deprecated method to valite object key
* fix(common): flag missing required value for translatable string filed
* fix(shopware): Commented invalid attributes for Shopware v2 and v3
* feat(shopify): Implemented facet filters for tags and product types

## `1.1.18` (2020-11-04)

* fix(spryker): Included abstract decription
* fix(spryker): Counted items returned
* feat(spryker): Included query filter by SKUs and productIds
* fix(spryker): Replaced attribute value by key and label
* refactor(spryker): Removed unused methods and extra comments
* fix(spryker): Removed mapImages overwrited implementation
* fix(spryker): Set valid variant sku

## `1.1.17` (2020-10-30)

* fix(spryker): Returned orignal account if not possible to be refreshed with no authToken
* fix: don't enforce tax category in Commercetools
* fix: use Shopware calculated price if available
* fix(spryker): Removed verifyEmail as non part of AccountAPI abstraction
* fix(spryker): Returned email as part of reset account

## `1.1.16` (2020-10-16)

* fix(productSearchApi): Remove URL decoding for Findologic provided URLs
* fix(productSearchApi): Remove redundant code from FindologicClientFactory
* feat(productSearchApi): Add config options for category and slug source to Findologic Mapper
* refactor(shopify): Improved filter limit calculation
* feat(shopify): Implemented filter by category id
* fix(shopify): Set default limit in product queries
* fix(shopify): Removed product if from searchable attributes
* fix(shopify): Especified correct operator between filters
* fix(shopify): Handle Enum attribute as boolean
* feat(shopify): Mapped query filters
* fix(shopify): Set vendor attribute as text
* fix(shopify): Removed un-used variable
* fix: Only set fetch_format when needed
* chore: Extracted common webspocket code

## `1.1.15` (2020-10-13)

* fix: Do not cast null values in media library to numeric
* fix: Set whislist as not implemented for Shopify

## `1.1.14` (2020-10-07)

* fix(shopify): Used only SKU values on variant.sku mapping
* fix: Import deprecate helper in a way it also works in tests
* fix(shopify): Used variant Id instead of SKU to add product to the cart
* fix(productSearchApi): Skip SearchableAttributes integration tests for Findologic
* fix(ProductSearchApi): Set nextCursor to null if it exceeds total results
* fix: Removed cartId validation on testAPI
* chore: Refactored withCurrency method as appendCurrencyToUrl
* fix: Passed LocalCreator into Guest and Customer Cart
* fix: Casting categoryId as sting
* fix: Set right RequestProvider path on Findologic tests
* fix: Set whislist as not implemented for Shopify
* feat(shopify rate limits): Moved RequestProvider to CoreBundle and used in ShopifyClient
* feat(shopify CartApi): Throwed exception on error responses
* feat(shopify CartApi): Implemented getForUser
* feat(shopify CartApi): Implemented setEmail, getOrder and getOrders methods
* feat(shopify CartApi): Implemented ShippingAddress method
* feat(shopify CartApi): Returned Client as DangerourInnerClient
* chore(shopify CartApi): Validated empty address before mapping
* feat(shopify CartApi): Implemented setShippingAddress
* refactor(shopify CartApi): Extrated Account mapper
* feat(shopify CartApi): Implemented update and remove LineItem
* refactor(shopify CartApi): Used product variant mapper
* refactor(shopify CartApi): Extracted product mapper
* feat(shopify CartApi): Implemented addToCart
* feat(shopify CartApi): Implemented initial integration for getById
* feat(shopify CartApi): Implemented CartApi skeleton along with basic getAnonymous implementation
* chore(shopify AccountApi): Added missing method notation
* chore(shopify AccountApi): Refactored query fields building
* feat(shopify AccountApi): Implemented update and get address
* feat(shopify AccountApi): Implement add address
* feat(shopify AccountApi): Implement update account and password
* feat(shopify AccountApi): Implement create, login and refresh account
* feat: call product search API directly
* feat(productSearchApi): Include label facet type in result mapping and remove image as it is not available
* refactor(productSearchApi): Move parameter building to only include client parameters in search
* feat(productSearchApi): Add test for findologic client request parameters
* feat(productSearchApi): Add client request parameters to findologic requests
* feat(productSearchApi): Strip HTML entities and tags from findologic names and summaries
* fix(productSearchApi): Apply Findologic data source config from engine section
* feat(productSearchApi): Make Findologic result attributes configurable
* feat!: use the product search API
* feat: add ShopifyProductSearchApi
* fix: unwrap promise in test
* feat(productSearch): call product event listeners
* chore: Import notifier from compiled build to make jest happy
* refactor(productSearchApi): Up the async by not unwrapping promises within getSearchableAttributes
* fix(productSearchApi): Fix condition check in Findologic implementation
* feat(productSearchApi): Throw on unknown language requested
* chore: Extracted common webspocket code
* misc: Tagged release 1.1.10 for common
* chore: Regenerated API documentation
* Merge pull request #317 from FrontasticGmbH/kore/typescript-types
* feat(shopware): use parent ID as product ID
* feat(shopware): don't exclude variants from product search
* chore: coding style
* fix(api tests): add some assertions
* feat(shopware): don't test product search returns all variants
* feat(shopware): support legacy filter options
* feat(shopware): use proper total calculation
* fix(spryker searchableAttributes): Refactor test to use SprykerProductSearchApi
* chore: Added @required to data objects in common, too
* feat(spryker searcheableAttributes): Copied implementation in ProductSearchApi
* feat(spryker searchableAttributes): Improved Localization to store required values
* fix(spryker searchableAttributes): Removed unused mapper
* feat(spryker searchableAttributes): Implemented searchable attributes logic
* chore: Updated type definitions
* chore: Added note about autogenerated files
* chore: Also reverted newly added docs
* chore: Revert docs changes to reduce diff size
* fix: Import under "full name" to reduce naming conflicts
* feat: Correctly import referenced types
* feat: Created TypeScript types for common domain objects
* chore: Started generating non namespaced types in file tree
* chore: Generated additional docs for new bundles
* chore: Updated API doc overview
* chore: Removed TypeScript types using namespaces
* chore: Regenerated docs
* fix(productSearchApi): Fix Findologic fallback test after refactoring
* feat(productSearchApi): Support SKU search via fallback for Findologic
* fix(productSearchApi): Fix Findologic getSearchableAttributes request
* fix: Change namespace for two misplaced tests
* feat(productSearchApi): Add support for multiple Findologic backends by locale
* misc: Remove unused Findologic Client Factory method
* feat(productSearchApi): Add simple SearchableAttributes implementation for Findologic
* refactor(productSearchApi): Make getSearchableAttributes async
* Ensure type renaming works again
* Generated TypeScript types for catwalk & common domain models
* Fixed type hint
* Flagged types

## `1.1.13` (2020-10-02)

* fix: Removed cartId validation on testAPI
* chore: Refactored withCurrency method as appendCurrencyToUrl
* fix: Passed LocalCreator into Guest and Customer Cart
* fix: Casting categoryId as sting
* fix: Set right RequestProvider path on Findologic tests
* fix: Set whislist as not implemented for Shopify
* feat(shopify rate limits): Moved RequestProvider to CoreBundle and used in ShopifyClient
* feat(shopify CartApi): Throwed exception on error responses
* feat(shopify CartApi): Implemented getForUser
* feat(shopify CartApi): Implemented setEmail, getOrder and getOrders methods
* feat(shopify CartApi): Implemented ShippingAddress method
* feat(shopify CartApi): Returned Client as DangerourInnerClient
* chore(shopify CartApi): Validated empty address before mapping
* feat(shopify CartApi): Implemented setShippingAddress
* refactor(shopify CartApi): Extrated Account mapper
* feat(shopify CartApi): Implemented update and remove LineItem
* refactor(shopify CartApi): Used product variant mapper
* refactor(shopify CartApi): Extracted product mapper
* feat(shopify CartApi): Implemented addToCart
* feat(shopify CartApi): Implemented initial integration for getById
* feat(shopify CartApi): Implemented CartApi skeleton along with basic getAnonymous implementation
* chore(shopify AccountApi): Added missing method notation
* chore(shopify AccountApi): Refactored query fields building
* feat(shopify AccountApi): Implemented update and get address
* feat(shopify AccountApi): Implement add address
* feat(shopify AccountApi): Implement update account and password
* feat(shopify AccountApi): Implement create, login and refresh account
* feat: call product search API directly
* feat(productSearchApi): Include label facet type in result mapping and remove image as it is not available
* refactor(productSearchApi): Move parameter building to only include client parameters in search
* feat(productSearchApi): Add test for findologic client request parameters
* feat(productSearchApi): Add client request parameters to findologic requests
* feat(productSearchApi): Strip HTML entities and tags from findologic names and summaries
* fix(productSearchApi): Apply Findologic data source config from engine section
* feat(productSearchApi): Make Findologic result attributes configurable
* feat!: use the product search API
* feat: add ShopifyProductSearchApi
* fix: unwrap promise in test
* feat(productSearch): call product event listeners
* chore: Import notifier from compiled build to make jest happy
* refactor(productSearchApi): Up the async by not unwrapping promises within getSearchableAttributes
* fix(productSearchApi): Fix condition check in Findologic implementation
* feat(productSearchApi): Throw on unknown language requested
* chore: Extracted common webspocket code
* chore: Regenerated API documentation
* feat(shopware): use parent ID as product ID
* feat(shopware): don't exclude variants from product search
* fix(api tests): add some assertions
* feat(shopware): don't test product search returns all variants
* feat(shopware): support legacy filter options
* feat(shopware): use proper total calculation
* fix(spryker searchableAttributes): Refactor test to use SprykerProductSearchApi
* feat(spryker searcheableAttributes): Copied implementation in ProductSearchApi
* feat(spryker searchableAttributes): Improved Localization to store required values
* fix(spryker searchableAttributes): Removed unused mapper
* feat(spryker searchableAttributes): Implemented searchable attributes logic
* chore: Updated type definitions
* chore: Added note about autogenerated files
* chore: Also reverted newly added docs
* chore: Revert docs changes to reduce diff size
* fix: Import under "full name" to reduce naming conflicts
* feat: Correctly import referenced types
* feat: Created TypeScript types for common domain objects
* chore: Started generating non namespaced types in file tree
* chore: Generated additional docs for new bundles
* chore: Updated API doc overview
* chore: Removed TypeScript types using namespaces
* chore: Regenerated docs
* fix(productSearchApi): Fix Findologic fallback test after refactoring
* feat(productSearchApi): Support SKU search via fallback for Findologic
* fix(productSearchApi): Fix Findologic getSearchableAttributes request
* fix: Change namespace for two misplaced tests
* feat(productSearchApi): Add support for multiple Findologic backends by locale
* misc: Remove unused Findologic Client Factory method
* feat(productSearchApi): Add simple SearchableAttributes implementation for Findologic
* refactor(productSearchApi): Make getSearchableAttributes async
* misc: Clean up test
* Ensure type renaming works again
* Generated TypeScript types for catwalk & common domain models
* Fixed type hint
* Flagged types

## `1.1.12` (2020-10-01)

* feat: Shopify CartApi & AccountApi

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
