<?php

namespace Frontastic\Common\SprykerBundle\Domain\Project;

use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\SprykerBundle\BaseApi\SprykerApiBase;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use Frontastic\Common\SprykerBundle\Domain\Project\Mapper\ProductSearchableAttributesMapper;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;

class SprykerProjectApi extends SprykerApiBase implements SprykerProjectApiInterface
{
    /**
     * @var string[]
     */
    private $projectLanguages;

    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        LocaleCreator $localeCreator,
        array $projectLanguages
    ) {
        parent::__construct($client, $mapperResolver, $localeCreator);

        $this->projectLanguages = $projectLanguages;
    }

    /**
     * @return Attribute[]
     */
    public function getSearchableAttributes(): array
    {
        // @TODO: implement multi languages

        $resources = [];
        try {
            $response = $this->client->get('/product-management-attributes');
            $resources  = $response->document()->primaryResources();
        } catch (\Exception $e) {
            // Endpoint not implemented
            if ($e->getCode() === 404) {
                // TODO: Log error
            }
        }

        $attributes = $this->mapperResolver
            ->getExtendedMapper(ProductSearchableAttributesMapper::MAPPER_NAME)
            ->mapResourceArray($resources);

        foreach ($attributes as &$attribute) {
            $attribute->label = $this->mapLocales($attribute->label);
            foreach ($attribute->values as &$value) {
                if (is_array($value)) {
                    $value = $this->mapLocales($value);
                }
            }
        }

        return $this->addCustomAttributes($attributes);
    }

    private function mapLocales(array $localizedStrings): array
    {
        $localizedResult = [];
        foreach ($this->projectLanguages as $language) {
            $locale = $this->localeCreator->createLocaleFromString($language);
            $localizedResult[$language] =
                $localizedStrings[$locale->language . '_' . $locale->country] ??
                (reset($localizedStrings) ?: '');
        }

        return $localizedResult;
    }

    /**
     * @param string[] $attributes
     *
     * @return string[]
     */
    private function addCustomAttributes(array $attributes): array
    {
        // Not included in attributes in Spryker
        $attributeId = 'price';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_MONEY,
            'label' => null, // Can we get the price label somehow?
        ]);

        $attributeId = 'listingPrices';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_MONEY,
        ]);

        $attributeId = 'categories.id';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_CATEGORY_ID,
        ]);

        return $attributes;
    }
}
