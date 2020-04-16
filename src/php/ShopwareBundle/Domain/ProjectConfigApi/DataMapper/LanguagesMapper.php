<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareLanguage;

class LanguagesMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'languages';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $languagesData = $this->extractData($resource);

        $result = [];
        foreach ($languagesData as $languageData) {
            $result[] = $this->mapDataToShopwareLanguage($languageData);
        }

        return $result;
    }

    private function mapDataToShopwareLanguage(array $languageData): ShopwareLanguage
    {
        $language = new ShopwareLanguage($languageData, true);
        $language->name = $this->resolveTranslatedValue($languageData, 'name');
        $language->localeCode = $languageData['locale']['code'];
        $language->localeName = $this->resolveTranslatedValue($languageData['locale'], 'name');
        $language->localeTerritory = $this->resolveTranslatedValue($languageData['locale'], 'territory');

        return $language;
    }
}
