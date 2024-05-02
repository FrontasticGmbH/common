# `abstract`  ViewStruct

**Fully Qualified**: [`\Frontastic\Common\Mvc\ViewStruct`](../../../src/php/Mvc/ViewStruct.php)

Target for properties and view logic passed to any templating mechanism or
serialization method. Returning a ViewStruct from a controller is catched by
the ViewListener and transformed into a Twig template for example:

     # View/Default/HelloView.php      class HelloView extends ViewStruct     
{          public $name;

         public function reverseName()          {              return
strrev($this->name);          }      }

     # Controller/DefaultController.php

     {          public function helloAction($name)          {             
return new HelloView(array('name' => $name));          }      }

     # Resources/views/Default/hello.html.twig      Hello {{ view.name }} or
{{ view.reverseName() }}!

## Methods

* [__construct()](#__construct)
* [__get()](#__get)

### __construct()

```php
public function __construct(
    array $data
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`array`||

Return Value: `mixed`

### __get()

```php
public function __get(
    string $name
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$name`|`string`||

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
