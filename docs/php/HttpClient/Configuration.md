#  Configuration

**Fully Qualified**: [`\Frontastic\Common\HttpClient\Configuration`](../../../src/php/HttpClient/Configuration.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`options` | [`Options`](Options.md) | `null` | - | 
`defaultHeaders` | `string[]` | `[]` | - | List (not hashmap!) of headers
`signatureSecret` | `?string` | `null` | - | 
`collectStats` | `bool` | `true` | - | 
`collectProfiling` | `bool` | `true` | - | 

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
