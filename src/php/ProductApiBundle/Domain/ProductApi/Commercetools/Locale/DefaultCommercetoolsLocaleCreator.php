<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ProjectConfigApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;

class DefaultCommercetoolsLocaleCreator extends CommercetoolsLocaleCreator
{
    /**
     * @var ProjectConfigApi
     */
    private $projectConfigApi;

    /**
     * @var bool
     */
    private $projectConfigFetched = false;

    /**
     * @var string[]
     */
    private $languages = null;

    /**
     * @var string[]
     */
    private $countries = null;

    /**
     * @var string[]
     */
    private $currencies = null;

    public function __construct(ProjectConfigApi $projectConfigApi)
    {
        $this->projectConfigApi = $projectConfigApi;
    }

    public function createLocaleFromString(string $localeString): CommercetoolsLocale
    {
        $this->fetchProjectConfig();

        $locale = Locale::createFromPosix($localeString);

        $language = $this->pickLanguage($locale);
        $country = $this->pickCountry($locale, $language);
        $currency = $this->pickCurrency($locale);
        return new CommercetoolsLocale([
            'language' => $language,
            'country' => $country,
            'currency' => $currency,
        ]);
    }

    private function pickLanguage(Locale $frontasticLocale): string
    {
        $languages = [
            $frontasticLocale->language . '-' . $frontasticLocale->territory,
            $frontasticLocale->language,
        ];
        foreach ($languages as $language) {
            $foundLanguage = $this->findInOptions($language, $this->languages);
            if ($foundLanguage !== null) {
                return $foundLanguage;
            }
        }

        $languagePrefixes = [
            $frontasticLocale->language . '-',
        ];
        foreach ($languagePrefixes as $languagePrefix) {
            $foundLanguage = $this->findOptionWithPrefix($languagePrefix, $this->languages);
            if ($foundLanguage !== null) {
                return $foundLanguage;
            }
        }

        return $this->languages[0];
    }

    private function pickCountry(Locale $frontasticLocale, string $language): string
    {
        $countries = [
            $frontasticLocale->territory,
            $frontasticLocale->language,
            $language,
        ];
        foreach ($countries as $country) {
            $foundCountry = $this->findInOptions($country, $this->countries);
            if ($foundCountry !== null) {
                return $foundCountry;
            }
        }

        return $this->countries[0];
    }

    private function pickCurrency(Locale $frontasticLocale): string
    {
        $currencies = [
            $frontasticLocale->currency,
            Locale::createFromPosix($frontasticLocale->language . '_' . $frontasticLocale->territory)->currency,
        ];
        foreach ($currencies as $currency) {
            $foundCurrency = $this->findInOptions($currency, $this->currencies);
            if ($foundCurrency !== null) {
                return $foundCurrency;
            }
        }

        return $this->currencies[0];
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

    private function fetchProjectConfig(): void
    {
        if ($this->projectConfigFetched) {
            return;
        }

        $result = $this->projectConfigApi->getProjectConfig();

        foreach (['languages', 'countries', 'currencies'] as $property) {
            if (!array_key_exists($property, $result)) {
                throw new \RuntimeException('Commercetools has no ' . $property . ' configured');
            }
            $values = $result[$property];
            if (!is_array($values)) {
                throw new \RuntimeException('Invalid JSON');
            }
            if (count($values) === 0) {
                throw new \RuntimeException('Commercetools has no ' . $property . ' configured');
            }
            foreach ($values as $value) {
                if (!is_string($value)) {
                    throw new \RuntimeException('Invalid JSON: ' . $property . ' has to contain strings');
                }
            }

            $this->$property = $values;
        }

        $this->projectConfigFetched = true;
    }
}
