#Shopware

This bundle contains Shopware 6 SalesChannel API integration

##Preparation

This bundle utilizes Shopware6 SalesChannel API. In order to get started head to you Shopware6 Admin SalesChannel section (in sidebar).
Choose an existing Headless SalesChannel API or create a new one. Once done configuring, grab API access key - this will have to be inserted 
in place of `API_KEY` when configuring

###Adjusting Shopware6 configuration

@TODO: clarify how to override allowed_limits

##Usage
Add following line to your catwalk `bundles.php`

```php 
#bundles.php

<?php

return [
    new \Frontastic\Common\ShopwareBundle\FrontasticCommonShopwareBundle()
];

```

In `project.yml` set `API_KEY` to API access key taken from Shopware6 Admin SalesChannel configuration, set engine to `showpare` and you're good to go
```yaml
configuration:
    showpare:
       apiKey: API_KEY
       endpoint: https://shopware6-url
    products:
       engine: shopware
```

