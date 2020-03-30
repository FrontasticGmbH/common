<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain\Locale;

abstract class SapLocaleCreator
{
    abstract public function createLocaleFromString(string $localeString): SapLocale;
}
