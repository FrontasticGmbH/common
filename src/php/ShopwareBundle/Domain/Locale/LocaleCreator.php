<?php declare(strict_types=1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;

class LocaleCreator
{
    public function createLocaleFromString(string $localeString): ShopwareLocale
    {
        // @TODO: fetch project config

        $locale = Locale::createFromPosix($localeString);

        // @TODO: implement pickers from project configuration
        $language = $locale->language;
        $country = $locale->country;
        $currency = $locale->currency;

        return new ShopwareLocale([
            'language' => $language,
            'country' => $country,
            'currency' => $currency,
        ]);
    }
}
