<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareLanguage;

class LanguagesMapper implements DataMapperInterface
{
    public const MAPPER_NAME = 'languages';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $result = [];
        foreach ($resource as $languageData) {
            $result[] = $this->mapDataToShopwareLanguage($languageData);
        }

        return $result;
    }

    private function mapDataToShopwareLanguage(array $languageData): ShopwareLanguage
    {
        $language = new ShopwareLanguage($languageData, true);
        $language->name = $languageData['translated']['name'] ?? $languageData['name'];
        $language->localeCode = $languageData['locale']['code'];
        $language->localeName = $languageData['locale']['translated']['name'] ?? $languageData['locale']['name'];
        $language->localeTerritory = $languageData['locale']['translated']['territory'] ?? $languageData['locale']['territory'];

        return $language;
    }
}
