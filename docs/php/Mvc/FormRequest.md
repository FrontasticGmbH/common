# `interface`  FormRequest

**Fully Qualified**: [`\Frontastic\Common\Mvc\FormRequest`](../../../src/php/Mvc/FormRequest.php)

## Methods

* [handle()](#handle)
* [getValidData()](#getvaliddata)
* [isValid()](#isvalid)
* [isBound()](#isbound)
* [getForm()](#getform)
* [createFormView()](#createformview)

### handle()

```php
public function handle(
    string $formType,
    mixed $bindData = null,
    array $options = []
): bool
```

*Attempt to handle a form and return true when handled and data is valid.*

Argument|Type|Default|Description
--------|----|-------|-----------
`$formType`|`string`||
`$bindData`|`mixed`|`null`|
`$options`|`array`|`[]`|

Return Value: `bool`

### getValidData()

```php
public function getValidData(): mixed
```

*Use this to retrieve the validated data from the form even when you attached `$bindData`.*

Only by using this method you can mock the form handling by providing a replacement valid value in tests.

Return Value: `mixed`

### isValid()

```php
public function isValid(): bool
```

*Is the bound form valid?*

Return Value: `bool`

### isBound()

```php
public function isBound(): bool
```

*Is the request bound to a form?*

Return Value: `bool`

### getForm()

```php
public function getForm(): \Symfony\Component\Form\FormInterface
```

Return Value: `\Symfony\Component\Form\FormInterface`

### createFormView()

```php
public function createFormView(): ?\Symfony\Component\Form\FormView
```

*Create the form view for the handled form.*

Throws exception when no form was handled yet.

Return Value: `?\Symfony\Component\Form\FormView`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
