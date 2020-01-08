<?php

namespace Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;

class Commercetools implements ProjectApi
{
    private const TYPE_MAP = [
        'lenum' => Attribute::TYPE_LOCALIZED_ENUM,
        'ltext' => Attribute::TYPE_LOCALIZED_TEXT,
    ];

    /**
     * @var ProductApi\Commercetools\Client
     */
    private $client;

    /**
     * @var ProductApi\Commercetools\Locale\CommercetoolsLocaleCreator
     */
    private $localeCreator;

    /**
     * @var string[]
     */
    private $languages;

    public function __construct(
        ProductApi\Commercetools\Client $client,
        ProductApi\Commercetools\Locale\CommercetoolsLocaleCreator $localeCreator,
        array $languages
    ) {
        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->languages = $languages;
    }

    public function getSearchableAttributes(): array
    {
        $productTypes = $this->client->fetchAsync('/product-types')->wait();

        $attributes = [];
        foreach ($productTypes->results as $productType) {
            foreach ($productType['attributes'] as $rawAttribute) {
                if (!$rawAttribute['isSearchable']) {
                    continue;
                }

                $attributeId = 'variants.attributes.' . $rawAttribute['name'];

                $rawType = $rawAttribute['type']['name'];
                $rawValues = $rawAttribute['type']['values'] ?? null;
                if ($rawType === 'set') {
                    $rawType = $rawAttribute['type']['elementType']['name'];
                    $rawValues = $rawAttribute['type']['elementType']['values'] ?? null;
                }

                $attributes[$attributeId] = new Attribute([
                    'attributeId' => $attributeId,
                    'type' => $this->mapAttributeType($rawType),
                    'label' => $this->mapLocales($rawAttribute['label']),
                    'values' => $this->mapValueLocales($rawValues),
                ]);
            }
        }

        // Not included in attributes in CT
        $attributeId = 'variants.price';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_MONEY,
            'label' => null, // Can we get the price label somehow?
        ]);

        $attributeId = 'variants.scopedPrice.value';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_MONEY,
            'label' => null, // Can we get the price label somehow?
        ]);

        $attributeId = 'categories.id';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_CATEGORY_ID,
            'label' => null, // Can we get the label somehow?
        ]);

        return $attributes;
    }

    private function mapLocales(array $localizedStrings): array
    {
        $localizedResult = [];
        foreach ($this->languages as $language) {
            $locale = $this->localeCreator->createLocaleFromString($language);
            $localizedResult[$language] =
                $localizedStrings[$locale->language] ??
                (reset($localizedStrings) ?: '');
        }
        return $localizedResult;
    }

    private function mapValueLocales(array $values = null): ?array
    {
        if ($values === null) {
            return null;
        }

        foreach ($values as $key => $value) {
            if (is_array($value['label'])) {
                $values[$key]['label'] = $this->mapLocales($value['label']);
            }
        }
        return $values;
    }

    private function mapAttributeType(string $commerceToolsType): string
    {
        if (isset(self::TYPE_MAP[$commerceToolsType])) {
            return self::TYPE_MAP[$commerceToolsType];
        }
        return $commerceToolsType;
    }
}
