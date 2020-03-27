# Common Libraries

**Copyright (C) Frontastic GmbH - All Rights Reserved**

These is a library which contains common domain concepts and functionality for
Frontastic.

Documentation about Frontastic can be found at https://docs.frontastic.cloud/
while the [API Documentation](docs/) can be found inside this repository.

# API Design Concepts

## TL;DR

* Plain data objects without logic
* Logic replaceable encapsulated in services from DIC or factory
* Expose the common base of all back-ends
* `dangerousInner*` for raw data
* `before*` / `after*` Decorators for customer adjustments

## Domain Model

One of the core concepts we believe in in API Design is [Injectables vs
Newables as described in the Qafoo blog](https://qafoo.com/blog/111_injectables_newables.html).
This is why we have objects modelling the data which usually have no methods at
all and objects which implement the service interactions. We call them Data
Objects (Newables) and Services (Injectables) in this document.

### Data Objects

The Domain Objects model the Data Structures. The only methods which are
allowed on Data Objects are methods which nobody would ever implement in a
different way. Since the APIs are the base for many different customer projects
those methods basically will not exist. A popular example are mail validations
in Domain Objects, while the mail validation regular expressions already differ
you could even think of using external services to validate the validity of a
mail address, which then obviously should not happen in Data Objects.

Our Data Objects are modeled as classes with public properties extending [a
base DataObject](https://github.com/kore/DataObject) ensuring PHP throws
exceptions when accessing non existent properties. We do this to simplify
creation and usage – we do not consider getters and setters a must even you
could implement additional type checks there. In the near future PHP might even
add support for typed properties which would even solve this problem.

While we generally consider immutability of Data Objects a plus, and we should
usually not modify them ourselves, our Data Objects are mutable by intention.
This enables modification by customers: For examples we allow customers to map
out additional properties and allow them to define, for example, additional
attributes inside a variant (see Decorators).

For those customers who are allowed to modify PHP code we allow them to extend
the existing Data Objects to create objects with additional properties. So no
class should be `final`.

All parameters and return values of methods (except for constructors) *should*
be Data Objects, while we seldomly use scalar values, while this is
discouraged. Services **must not** be parameters (except for the constructor or
setter injection) nor return values.

### Services

Services implement the actual functionality, like implementing our API
interfaces for a certain endpoint. Services are **always** created by our
factories (by configuration) or by the Symfony Dependency Injection Container.
Services can also be overwritten by customers, but this should be the last
resort to implement a certain functionality. We want to provide sensible
extension points (like the decorators) to implement all functionailty.

Beside the general implementation for the API (like `ProductApi\Commercetools`)
there may be any number of helper services (like the mapper, the client, …).

## Code Structure

There are mainly two types of bundles:

* API **Abstractions** `*ApiBundle` (for example the `ProductApiBundle`)

  These bundles are supposed to only contain the interfaces and the domain
  objects.  This principle is currently violated by the Commercetools
  implementation which is part of the API abstractions. This is supposed to be
  refactored accordingly.

* API **Implementations** (for example the `ShopwareBundle`)

  These bundles implement the API for a certain backend and can be enabled if
  the customers uses this backend. These API Implementation bundles usually
  implement the interfaces from the API Abstraction bundles and may contain
  additional helper services. They should usually not define any Domain
  Objects.

Besides that there are some additional bundles and infrastructure code like the
generic Http-Client-Implementation which allows us to implement, monitor and
configure HTTP requests in a generic way.

## Decorators

The decorator structure is implemented in
[Catwalk](https://github.com/FrontasticGmbH/catwalk) but still is relevant to
these APIs: We allow people to hook into every API call using `before*` and
`after*` decorators for all API methods. This allows people to modify queries
to the API and also the return values of the API. This is the main extension
point for all APIs. On top of this there might be API specific configuration
handled by the API factory.

## Dangerous-Inner-*

Many Domain Objects have a `dangerousInner*` property (for example
`dangerousInnerProduct`) which is a direct reference to the unmodified return
value from the respective API. This allows users of our APIs to access
additional data which our own mappers do not map out to our own Domain Objects.

We understand that we do not cover all potential use cases of the mapped APIs
and this allows people to access the original data inside their decorators. The
`dengerousInner*` properties are usually stripped out by the Backend For
Frontend, though, to not leak confidential data and to reduce data size. Also
many API SDKs which might be used by certain implementations use "Domain
Objects" which are not possible to serialize sensibly.
