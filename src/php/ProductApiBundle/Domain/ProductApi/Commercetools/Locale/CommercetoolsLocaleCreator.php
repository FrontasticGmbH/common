<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale;

abstract class CommercetoolsLocaleCreator
{
    abstract public function createLocaleFromString(string $localeString): CommercetoolsLocale;
}
