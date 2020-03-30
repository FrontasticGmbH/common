<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreator;
use function GuzzleHttp\Promise\unwrap;

class SapProjectApi implements ProjectApi
{
    private const ATTRIBUTE_TYPES = [
        'price' => Attribute::TYPE_MONEY,
    ];

    /** @var SapClient */
    private $client;

    /** @var SapLocaleCreator */
    private $localeCreator;

    /** @var string[] */
    private $projectLanguages;

    public function __construct(
        SapClient $client,
        SapLocaleCreator $localeCreator,
        array $projectLanguages
    ) {
        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->projectLanguages = $projectLanguages;
    }

    public function getSearchableAttributes(): array
    {
        $languagesToFetch = [];
        foreach ($this->projectLanguages as $language) {
            $locale = $this->localeCreator->createLocaleFromString($language);
            if (!array_key_exists($locale->languageCode, $languagesToFetch)) {
                $languagesToFetch[$locale->languageCode] = [];
            }
            $languagesToFetch[$locale->languageCode][] = $language;
        }

        $results = [];
        foreach ($languagesToFetch as $languageCode => $languages) {
            $results[] = $this->client
                ->get(
                    '/rest/v2/{siteId}/products/search',
                    [
                        'fields' => 'facets',
                        'lang' => $languageCode,
                    ]
                )
                ->then(function (array $data) use ($languages): array {
                    $attributes = [];

                    foreach ($data['facets'] as $facet) {
                        $attributeId = explode(':', $facet['values'][0]['query']['query']['value'])[2];

                        $attributeData = [
                            'label' => array_fill_keys($languages, $facet['name']),
                        ];

                        if ($facet['category'] ?? false === true) {
                            $attributeData['type'] = Attribute::TYPE_CATEGORY_ID;
                        }

                        $attributes[$attributeId] = $attributeData;
                    }

                    return $attributes;
                });
        }
        $results = unwrap($results);

        $attributes = [];
        foreach ($results as $result) {
            foreach ($result as $attributeId => $attributeData) {
                if (!array_key_exists($attributeId, $attributes)) {
                    $attributeType =
                        $attributeData['type'] ??
                        static::ATTRIBUTE_TYPES[$attributeId] ??
                        Attribute::TYPE_TEXT;

                    $attributes[$attributeId] = new Attribute([
                        'attributeId' => $attributeId,
                        'type' => $attributeType,
                        'label' => [],
                    ]);
                }

                $attributes[$attributeId]->label = array_merge(
                    $attributes[$attributeId]->label,
                    $attributeData['label']
                );
            }
        }

        return $attributes;
    }
}
