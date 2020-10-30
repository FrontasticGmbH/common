<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\FacetDefinition;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFilter;

class MapperTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var Mapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new Mapper();
    }

    /**
     * @dataProvider provideDataToAttributesExamples
     */
    public function testDataToAttributes($attributesFixture, $locale, $expectedResult)
    {
        $actualResult = $this->mapper->dataToAttributes(
            ['attributes' => [$attributesFixture]],
            new CommercetoolsLocale($locale)
        );

        unset($actualResult['baseId']);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function provideDataToAttributesExamples()
    {
        return [
            'simpleStringAttribute' => [
                [
                    "name" => "submodel",
                    "value" => "Something",
                ],
                [
                    'country' => 'GB',
                    'language' => 'en',
                    'currency' => 'GBP',
                ],
                [
                    "submodel" => "Something",
                ],
            ],
            'keyValueAttribute' => [
                [
                    "name" => "brand",
                    "value" => [
                        "label" => "Rolex",
                        "key" => "rolex",
                    ],
                ],
                [
                    'country' => 'GB',
                    'language' => 'en',
                    'currency' => 'GBP',
                ],
                [
                    "brand" => [
                        "key" => "rolex",
                        "label" => "Rolex",
                    ],
                ],
            ],
            'translatedAttribute' => [
                [
                    "name" => "variantDescription",
                    "value" => [
                        "de" => "foo",
                        "en" => "bar",
                    ],
                ],
                [
                    'country' => 'GB',
                    'language' => 'en',
                    'currency' => 'GBP',
                ],
                [
                    "variantDescription" => "bar",
                ],
            ],
            'translatedLabelAttribute' => [
                [
                    "name" => "gender",
                    "value" => [
                        "label" => [
                            "de" => "",
                            "en" => "Male",
                        ],
                        "key" => "male",
                    ],
                ],
                [
                    'country' => 'GB',
                    'language' => 'en',
                    'currency' => 'GBP',
                ],
                [
                    "gender" => [
                        "key" => "male",
                        "label" => "Male",
                    ],
                ],
            ],
            'setAttribute' => [
                [
                    "name" => "features",
                    "value" => [
                        [
                            "label" => [
                                "en" => "Date",
                                "de" => "",
                            ],
                            "key" => "date",
                        ],
                        [
                            "label" => [
                                "en" => "Luminescent Hands",
                                "de" => "",
                            ],
                            "key" => "luminescent - hands",
                        ],
                    ],
                ],
                [
                    'country' => 'GB',
                    'language' => 'en',
                    'currency' => 'GBP',
                ],
                [
                    "features" => [
                        [
                            "key" => "date",
                            "label" => "Date",
                        ],
                        [
                            "key" => "luminescent - hands",
                            "label" => "Luminescent Hands",
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideFacetRequestExamples
     */
    public function testFacetsToRequest($facetDefinition, $expectedFacetQuery)
    {
        $actualFacetQueries = $this->mapper->facetsToRequest(
            [$facetDefinition],
            new CommercetoolsLocale([
                'country' => 'GB',
                'language' => 'en',
                'currency' => 'GBP',
            ])
        );

        $this->assertCount(1, $actualFacetQueries, 'Number of generated queries');
        $this->assertSame($expectedFacetQuery, $actualFacetQueries[0]);
    }

    public static function provideFacetRequestExamples()
    {
        return [
            [
                new FacetDefinition(['attributeId' => 'variant.attribute.size', 'attributeType' => 'number']),
                'variant.attribute.size as variant.attribute.size',
            ],
            [
                new FacetDefinition(['attributeId' => 'variant.price', 'attributeType' => 'money']),
                'variant.price.centAmount:range (0 to *) as variant.price',
            ],
            [
                new FacetDefinition(['attributeId' => 'variant.attribute.size', 'attributeType' => 'enum']),
                'variant.attribute.size.label as variant.attribute.size',
            ],
            [
                new FacetDefinition(['attributeId' => 'variant.attribute.size', 'attributeType' => 'localizedEnum']),
                'variant.attribute.size.label.en as variant.attribute.size',
            ],
            [
                new FacetDefinition([
                    'attributeId' => 'variant.attribute.audience',
                    'attributeType' => 'localizedText',
                ]),
                'variant.attribute.audience.en as variant.attribute.audience',
            ],
            [
                new FacetDefinition(['attributeId' => 'variant.attribute.onSale', 'attributeType' => 'boolean']),
                'variant.attribute.onSale as variant.attribute.onSale',
            ],
            [
                new FacetDefinition(['attributeId' => 'variant.attribute.vendor', 'attributeType' => 'text']),
                'variant.attribute.vendor as variant.attribute.vendor',
            ],
            [
                new FacetDefinition(['attributeId' => 'variant.attribute.recommended', 'attributeType' => 'reference']),
                'variant.attribute.recommended as variant.attribute.recommended',
            ],
            [
                new FacetDefinition([
                    'attributeId' => 'variant.attribute.fancyStuff',
                    'attributeType' => 'customUnknown',
                ]),
                'variant.attribute.fancyStuff as variant.attribute.fancyStuff',
            ],
        ];
    }

    /**
     * @dataProvider provideFacetToFilterExamples
     */
    public function testFacetsToFilter($facetDefinition, $facet, $expectedFilters)
    {
        $actualFilters = $this->mapper->facetsToFilter(
            [$facet],
            [$facetDefinition],
            new CommercetoolsLocale([
                'country' => 'GB',
                'language' => 'en',
                'currency' => 'GBP',
            ])
        );

        $this->assertSame($expectedFilters, $actualFilters);
    }

    public static function provideFacetToFilterExamples()
    {
        return [
            [
                new FacetDefinition(['attributeId' => 'variant.attribute.size', 'attributeType' => 'number']),
                new RangeFacet([
                    'handle' => 'variant.attribute.size',
                    'min' => 23,
                    'max' => 42,
                ]),
                ['variant.attribute.size:range (23 to 42)'],
            ],
            [
                new FacetDefinition(['attributeId' => 'variant.price', 'attributeType' => 'money']),
                new RangeFacet([
                    'handle' => 'variant.price',
                    'min' => 23,
                    'max' => 42,
                ]),
                ['variant.price.centAmount:range (23 to 42)'],
            ],
            [
                new FacetDefinition(['attributeId' => 'variant.attribute.size', 'attributeType' => 'enum']),
                new TermFacet([
                    'handle' => 'variant.attribute.size',
                    'terms' => ['XS'],
                ]),
                ['variant.attribute.size.label:"XS"'],
            ],
            [
                new FacetDefinition(['attributeId' => 'variant.attribute.size', 'attributeType' => 'localizedEnum']),
                new TermFacet([
                    'handle' => 'variant.attribute.size',
                    'terms' => ['large'],
                ]),
                ['variant.attribute.size.label.en:"large"'],
            ],
            [
                new FacetDefinition([
                    'attributeId' => 'variant.attribute.audience',
                    'attributeType' => 'localizedText',
                ]),
                new TermFacet([
                    'handle' => 'variant.attribute.audience',
                    'terms' => ['Small Kids'],
                ]),
                ['variant.attribute.audience.en:"Small Kids"'],
            ],
            [
                new FacetDefinition(['attributeId' => 'variant.attribute.onSale', 'attributeType' => 'boolean']),
                new TermFacet([
                    'handle' => 'variant.attribute.onSale',
                    'terms' => ['yes'],
                ]),
                ['variant.attribute.onSale:"yes"'],
            ],
            [
                new FacetDefinition([
                    'attributeId' => 'variant.attribute.fancyStuff',
                    'attributeType' => 'customUnknownRange',
                ]),
                new RangeFacet([
                    'handle' => 'variant.attribute.fancyStuff',
                    'min' => 23,
                    'max' => 42,
                ]),
                ['variant.attribute.fancyStuff:range (23 to 42)'],
            ],
            [
                new FacetDefinition([
                    'attributeId' => 'variant.attribute.fancyStuff',
                    'attributeType' => 'customUnknownTerm',
                ]),
                new TermFacet([
                    'handle' => 'variant.attribute.fancyStuff',
                    'terms' => ['1100101'],
                ]),
                ['variant.attribute.fancyStuff:"1100101"'],
            ],
            // Multi term test
            [
                new FacetDefinition(['attributeId' => 'variant.attribute.size', 'attributeType' => 'localizedEnum']),
                new TermFacet([
                    'handle' => 'variant.attribute.size',
                    'terms' => ['large', 'small', 'medium'],
                ]),
                [
                    'variant.attribute.size.label.en:"large","small","medium"',
                ],
            ],
        ];
    }

    public function testDataToNumberRangeFacetValuesAvailable()
    {
        $fixture = $this->loadFixture('dataToNumberRangeFacet_values_available_input.json');

        $actualResult = $this->mapper->dataToNumberRangeFacet('range-with-values', $fixture);

        $expectedResult = $this->loadFixture('dataToNumberRangeFacet_values_available_output.json');

        $this->assertEquals(
            new \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\RangeFacet($expectedResult),
            $actualResult
        );
    }

    /**
     * @dataProvider providePrepareFilterQueryExamples
     */
    public function testPrepareQueryFilter(array $inputFilters, array $expectedFilterStrings)
    {
        $actualFilterStrings = $this->mapper->prepareQueryFilter(
            $inputFilters,
            new CommercetoolsLocale([
                'country' => 'GB',
                'language' => 'en',
                'currency' => 'GBP',
            ])
        );

        $this->assertEquals(
            $expectedFilterStrings,
            $actualFilterStrings
        );
    }

    public function providePrepareFilterQueryExamples()
    {
        return [
            'legacyFilterStrings' => [
                [
                    'variants.attributes.visibility: true',
                    'variants.attributes.searchability: "1"',
                ],
                [
                    'variants.attributes.visibility: true',
                    'variants.attributes.searchability: "1"',
                ],
            ],
            'money' => [
                [
                    new RangeFilter([
                        'min' => 100,
                        'max' => 200,
                        'handle' => 'variants.price',
                        'attributeType' => 'money',
                    ]),
                ],
                [
                    'variants.price.centAmount:range (100 to 200)',
                ],
            ],
            'enum' => [
                [
                    new TermFilter([
                        'terms' => ['grey', 'red'],
                        'handle' => 'variants.attributes.color',
                        'attributeType' => 'enum',
                    ]),
                ],
                [
                    'variants.attributes.color.key:"grey","red"',
                ],
            ],
            'number (misc range)' => [
                [
                    new RangeFilter([
                        'min' => 3,
                        'max' => 8,
                        'handle' => 'variants.attributes.size',
                        'attributeType' => 'number',
                    ]),
                ],
                [
                    'variants.attributes.size:range (3 to 8)',
                ],
            ],
            'text (misc term)' => [
                [
                    new TermFilter([
                        'terms' => ['levis', 'wrangler'],
                        'handle' => 'variants.attributes.brand',
                        'attributeType' => 'text',
                    ]),
                ],
                [
                    'variants.attributes.brand:"levis","wrangler"',
                ],
            ],
            'localized text' => [
                [
                    new TermFilter([
                        'terms' => ['levis', 'wrangler'],
                        'handle' => 'variants.attributes.brand',
                        'attributeType' => 'localizedText',
                    ]),
                ],
                [
                    'variants.attributes.brand.en:"levis","wrangler"',
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    private function loadFixture(string $fileName)
    {
        return json_decode(file_get_contents(__DIR__ . '/_fixtures/' . $fileName), true);
    }
}
