<?php

namespace Frontastic\Common\SprykerBundle\Domain\Project;

use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\SprykerBundle\BaseApi\SprykerApiBase;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use Frontastic\Common\SprykerBundle\Domain\Project\Mapper\ProductSearchableAttributesMapper;
use Frontastic\Common\SprykerBundle\Domain\Project\Mapper\SprykerSalutationMapper;
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
//        $languagesToFetch = [];
//        foreach ($this->projectLanguages as $language) {
//            $locale = $this->localeCreator->createLocaleFromString($language);
//            if (!array_key_exists($locale->language, $languagesToFetch)) {
//                $languagesToFetch[$locale->language] = [];
//            }
//            $languagesToFetch[$locale->language][] = $language;
//        }

        $response = $this->client->get('/product-searchable-attributes');

        $attributes = $this->mapperResolver
            ->getExtendedMapper(ProductSearchableAttributesMapper::MAPPER_NAME)
            ->mapResourceArray($response->document()->primaryResources());

        // check if there are no attributes due to error or something, just return an empty result and don't add the
        // price attribute, as this will lead to disabling all other facets in backstage.
        if (empty($attributes)) {
            return $attributes;
        }

        return $this->addCustomAttributes($attributes);
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

        return $attributes;
    }

//    /**
//     * @return \Frontastic\Common\SprykerBundle\Domain\SprykerSalutation[]
//     */
//    public function getSalutations(): array
//    {
//        $response = $this->client->get('/salutations');
//
//        return $this->mapResponseResource($response, SprykerSalutationMapper::MAPPER_NAME);
//    }
}
