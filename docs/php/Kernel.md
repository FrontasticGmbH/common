# `abstract`  Kernel

**Fully Qualified**: [`\Frontastic\Common\Kernel`](../../src/php/Kernel.php)

**Extends**: `\Symfony\Component\HttpKernel\Kernel`

## Methods

* [getBaseDir()](#getbasedir)
* [registerContainerConfiguration()](#registercontainerconfiguration)
* [getRootDir()](#getrootdir)
* [getCacheDir()](#getcachedir)
* [getLogDir()](#getlogdir)
* [getConfiguration()](#getconfiguration)
* [getBaseConfiguration()](#getbaseconfiguration)
* [getEnvironmentFromConfiguration()](#getenvironmentfromconfiguration)
* [getAdditionalConfigFiles()](#getadditionalconfigfiles)
* [getDebug()](#getdebug)
* [isDebugEnvironment()](#isdebugenvironment)

### getBaseDir()

```php
static abstract public function getBaseDir(): string
```

Return Value: `string`

### registerContainerConfiguration()

```php
public function registerContainerConfiguration(
    \Symfony\Component\Config\Loader\LoaderInterface $loader
): mixed
```

*Register symfony configuration from base dir.*

Argument|Type|Default|Description
--------|----|-------|-----------
`$loader`|`\Symfony\Component\Config\Loader\LoaderInterface`||

Return Value: `mixed`

### getRootDir()

```php
public function getRootDir(): mixed
```

*Symfony determines this be Kernel file location otherwise, this does not
work for Catwalks.*

Return Value: `mixed`

### getCacheDir()

```php
public function getCacheDir(): string
```

Return Value: `string`

### getLogDir()

```php
public function getLogDir(): string
```

Return Value: `string`

### getConfiguration()

```php
static public function getConfiguration(): mixed
```

*Initialize configuration*

Return Value: `mixed`

### getBaseConfiguration()

```php
static public function getBaseConfiguration(): mixed
```

Return Value: `mixed`

### getEnvironmentFromConfiguration()

```php
static public function getEnvironmentFromConfiguration(): mixed
```

*Get environment*

Return Value: `mixed`

### getAdditionalConfigFiles()

```php
static public function getAdditionalConfigFiles(): mixed
```

*Get additional config files*

Return Value: `mixed`

### getDebug()

```php
static public function getDebug(): mixed
```

*Get debug*

Return Value: `mixed`

### isDebugEnvironment()

```php
static public function isDebugEnvironment(
    mixed $environment
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$environment`|`mixed`||

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
