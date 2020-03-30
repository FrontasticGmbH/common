<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain\Locale;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapProjectConfigApi;

class DefaultSapLocaleCreator extends SapLocaleCreator
{
    /** @var SapProjectConfigApi */
    private $projectConfigApi;

    public function __construct(SapProjectConfigApi $projectConfigApi)
    {
        $this->projectConfigApi = $projectConfigApi;
    }

    public function createLocaleFromString(string $localeString): SapLocale
    {
        $locale = Locale::createFromPosix($localeString);

        $language = $this->pickLanguage($locale);
        $currency = $this->pickCurrency($locale);
        return new SapLocale([
            'languageCode' => $language,
            'currencyCode' => $currency,
        ]);
    }

    private function pickLanguage(Locale $frontasticLocale): string
    {
        $availableLanguages = $this->projectConfigApi->getLanguageCodes();

        $languages = [
            $frontasticLocale->language . '-' . $frontasticLocale->territory,
            $frontasticLocale->language,
        ];
        foreach ($languages as $language) {
            $foundLanguage = $this->findInOptions($language, $availableLanguages);
            if ($foundLanguage !== null) {
                return $foundLanguage;
            }
        }

        $languagePrefixes = [
            $frontasticLocale->language . '-',
        ];
        foreach ($languagePrefixes as $languagePrefix) {
            $foundLanguage = $this->findOptionWithPrefix($languagePrefix, $availableLanguages);
            if ($foundLanguage !== null) {
                return $foundLanguage;
            }
        }

        return reset($availableLanguages);
    }

    private function pickCurrency(Locale $frontasticLocale): string
    {
        $availableCurrencies = $this->projectConfigApi->getCurrencyCodes();

        $currencies = [
            $frontasticLocale->currency,
            Locale::createFromPosix($frontasticLocale->language . '_' . $frontasticLocale->territory)->currency,
        ];
        foreach ($currencies as $currency) {
            $foundCurrency = $this->findInOptions($currency, $availableCurrencies);
            if ($foundCurrency !== null) {
                return $foundCurrency;
            }
        }

        return reset($availableCurrencies);
    }

    private function findInOptions(string $value, array $options): ?string
    {
        foreach ($options as $option) {
            if (strcasecmp($value, $option) === 0) {
                return $option;
            }
        }

        return null;
    }

    private function findOptionWithPrefix(string $prefix, array $options): ?string
    {
        $prefixLength = strlen($prefix);
        foreach ($options as $option) {
            if (strncasecmp($prefix, $option, $prefixLength) === 0) {
                return $option;
            }
        }

        return null;
    }
}
