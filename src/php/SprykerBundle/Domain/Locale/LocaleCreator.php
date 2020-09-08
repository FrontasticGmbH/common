<?php

namespace Frontastic\Common\SprykerBundle\Domain\Locale;

abstract class LocaleCreator
{
    abstract public function createLocaleFromString(string $localeString): SprykerLocale;
}
