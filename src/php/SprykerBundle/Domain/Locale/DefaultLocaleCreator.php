<?php

namespace Frontastic\Common\SprykerBundle\Domain\Locale;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\SprykerBundle\Domain\ProjectConfig\SprykerProjectConfigApi;

class DefaultLocaleCreator extends LocaleCreator
{
    /**
     * @var SprykerProjectConfigApi
     */
    private $projectConfigApi;

    public function __construct(SprykerProjectConfigApi $projectConfigApi)
    {
        $this->projectConfigApi = $projectConfigApi;
    }

    public function createLocaleFromString(string $localeString): SprykerLocale
    {
        $projectConfig = $this->fetchProjectConfig();

        $locale = Locale::createFromPosix($localeString);

        $language = $this->pickLanguageFromProjectConfig(
            $this->extractProjectConfigResource(
                $projectConfig,
                SprykerProjectConfigApi::RESOURCE_LOCALES
            ),
            $locale
        );
        $country = $this->pickCountryFromProjectConfig(
            $this->extractProjectConfigResource(
                $projectConfig,
                SprykerProjectConfigApi::RESOURCE_COUNTRIES
            ),
            $locale
        );
        $currency= $this->pickCurrencyFromProjectConfig(
            $this->extractProjectConfigResource(
                $projectConfig,
                SprykerProjectConfigApi::RESOURCE_CURRENCIES
            ),
            $locale
        );

        return new SprykerLocale([
            'language' => $language,
            'country' => $country,
            'currency' => $currency,
        ]);
    }

    private function extractProjectConfigResource(array $projectConfig, string $resourceName): array
    {
        return $projectConfig[$resourceName] ?? [];
    }

    private function fetchProjectConfig(): array
    {
        return $this->projectConfigApi->getProjectConfig();
    }

    private function pickLanguageFromProjectConfig(array $projectConfigLanguages, Locale $frontasticLocale): string
    {
        $locale = sprintf('%s_%s', $frontasticLocale->language, $frontasticLocale->territory);

        foreach ($projectConfigLanguages as $projectConfigLanguage) {
            if ($projectConfigLanguage['name'] === $locale) {
                return $projectConfigLanguage['code'];
            }
        }

        foreach ($projectConfigLanguages as $projectConfigLanguage) {
            if ($projectConfigLanguage['code'] === $frontasticLocale->language) {
                return $projectConfigLanguage['code'];
            }
        }

        return $projectConfigLanguages[0]['code'];
    }

    private function pickCountryFromProjectConfig(array $projectConfigCountries, Locale $frontasticLocale): string
    {
        foreach ($projectConfigCountries as $projectConfigCountry) {
            if ($projectConfigCountry['iso2Code'] === $frontasticLocale->territory) {
                return $projectConfigCountry['name'];
            }
        }

        return $projectConfigCountries[0]['name'];
    }

    private function pickCurrencyFromProjectConfig(array $projectConfigCurrencies, Locale $frontasticLocale): string
    {
        foreach ($projectConfigCurrencies as $projectConfigCurrency) {
            if ($projectConfigCurrency['code'] === $frontasticLocale->currency) {
                return $projectConfigCurrency['code'];
            }
        }

        return $projectConfigCurrencies[0]['code'];
    }
}
