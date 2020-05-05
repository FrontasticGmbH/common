<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

abstract class LocaleCreator
{
    abstract public function createLocaleFromString(string $localeString): ShopwareLocale;
}
