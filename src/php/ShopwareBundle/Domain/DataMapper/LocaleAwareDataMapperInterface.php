<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\Locale\ShopwareLocale;

interface LocaleAwareDataMapperInterface
{
    public function setLocale(ShopwareLocale $locale);

    public function getLocale(): ShopwareLocale;
}
