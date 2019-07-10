<?php

namespace Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
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
     * @var string[]
     */
    private $languages;

    public function __construct(ProductApi\Commercetools\Client $client, array $languages)
    {
        $this->client = $client;
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

                $attributes[$attributeId] = new Attribute([
                    'attributeId' => $attributeId,
                    'type' => $this->mapAttributeType($rawAttribute['type']['name']),
                    'label' => $this->mapLocales($rawAttribute['label']),
                    'values' => (isset($rawAttribute['type']['values'])
                        ? $this->mapValueLocales($rawAttribute['type']['values'])
                        : null
                    ),
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

        $attributeId = 'categories.id';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_CATEGORY_ID,
            'label' => null, // Can we get the label somehow?
        ]);

        return $attributes;
    }

    /**
     * @TODO: Is this a general way or do we need to resolve locales differently?
     */
    private function mapLocales(array $localizedStrings): array
    {
        $localizedResult = [];
        foreach ($this->languages as $language) {
            $locale = Locale::createFromPosix($language);
            $localizedResult[$language] = (isset($localizedStrings[$locale->language])
                ? $localizedStrings[$locale->language]
                : '');
        }
        return $localizedResult;
    }

    private function mapValueLocales(array $values): array
    {
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
