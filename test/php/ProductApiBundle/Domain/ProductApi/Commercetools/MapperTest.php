<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;


use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;

class MapperTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var Mapper
     */
    private $mapper;

    public function setUp()
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
            Locale::createFromPosix($locale)
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
                'en_GB',
                [
                    "submodel" => "Something"
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
                'en_GB',
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
                'en_GB',
                [
                    "variantDescription" => "bar",
                ]
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
                'en_GB',
                [
                    "gender" => [
                        "key" => "male",
                        "label" => "Male",
                    ]
                ]
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
                'en_GB',
                [
                    "features" => [
                        [
                            "key" => "date",
                            "label" => "Date",
                        ],
                        [
                            "key" => "luminescent - hands",
                            "label" => "Luminescent Hands",
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider provideFacetRequestExamples
     */
    public function testFacetsToRequest($facetDefinition, $expectedFacetQuery)
    {
        $actualFacetQueries = $this->mapper->facetsToRequest(
            [$facetDefinition],
            new Locale(['language' => 'en'])
        );

        $this->assertCount(1, $actualFacetQueries, 'Number of generated queries');
        $this->assertSame($expectedFacetQuery, $actualFacetQueries[0]);
    }

    public static function provideFacetRequestExamples()
    {
        return [
            [
                ['attributeId' => 'variant.attribute.size', 'attributeType' => 'number'],
                'variant.attribute.size as variant.attribute.size',
            ],
            [
                ['attributeId' => 'variant.price', 'attributeType' => 'money'],
                'variant.price.centAmount:range (0 to *) as variant.price',
            ],
            [
                ['attributeId' => 'variant.attribute.size', 'attributeType' => 'enum'],
                'variant.attribute.size.label as variant.attribute.size',
            ],
            [
                ['attributeId' => 'variant.attribute.size', 'attributeType' => 'localizedEnum'],
                'variant.attribute.size.label.en as variant.attribute.size',
            ],
            [
                ['attributeId' => 'variant.attribute.audience', 'attributeType' => 'localizedText'],
                'variant.attribute.audience.en as variant.attribute.audience',
            ],
            [
                ['attributeId' => 'variant.attribute.onSale', 'attributeType' => 'boolean'],
                'variant.attribute.onSale as variant.attribute.onSale',
            ],
            [
                ['attributeId' => 'variant.attribute.vendor', 'attributeType' => 'text'],
                'variant.attribute.vendor as variant.attribute.vendor',
            ],
            [
                ['attributeId' => 'variant.attribute.recommended', 'attributeType' => 'reference'],
                'variant.attribute.recommended as variant.attribute.recommended',
            ],
            [
                ['attributeId' => 'variant.attribute.fancyStuff', 'attributeType' => 'customUnknown'],
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
            new Locale(['language' => 'en'])
        );

        $this->assertSame($expectedFilters, $actualFilters);
    }

    public static function provideFacetToFilterExamples()
    {
        return [
            [
                ['attributeId' => 'variant.attribute.size', 'attributeType' => 'number'],
                new RangeFacet([
                    'handle' => 'variant.attribute.size',
                    'min' => 23,
                    'max' => 42,
                ]),
                ['variant.attribute.size:range (23 to 42)']
            ],
            [
                ['attributeId' => 'variant.price', 'attributeType' => 'money'],
                new RangeFacet([
                    'handle' => 'variant.price',
                    'min' => 23,
                    'max' => 42,
                ]),
                ['variant.price.centAmount:range (23 to 42)']
            ],
            [
                ['attributeId' => 'variant.attribute.size', 'attributeType' => 'enum'],
                new TermFacet([
                    'handle' => 'variant.attribute.size',
                    'terms' => ['XS']
                ]),
                ['variant.attribute.size.label:"XS"']
            ],
            [
                ['attributeId' => 'variant.attribute.size', 'attributeType' => 'localizedEnum'],
                new TermFacet([
                    'handle' => 'variant.attribute.size',
                    'terms' => ['large']
                ]),
                ['variant.attribute.size.label.en:"large"']
            ],
            [
                ['attributeId' => 'variant.attribute.audience', 'attributeType' => 'localizedText'],
                new TermFacet([
                    'handle' => 'variant.attribute.audience',
                    'terms' => ['Small Kids']
                ]),
                ['variant.attribute.audience.en:"Small Kids"']
            ],
            [
                ['attributeId' => 'variant.attribute.onSale', 'attributeType' => 'boolean'],
                new TermFacet([
                    'handle' => 'variant.attribute.onSale',
                    'terms' => ['yes']
                ]),
                ['variant.attribute.onSale:"yes"']
            ],
            [
                ['attributeId' => 'variant.attribute.fancyStuff', 'attributeType' => 'customUnknownRange'],
                new RangeFacet([
                    'handle' => 'variant.attribute.fancyStuff',
                    'min' => 23,
                    'max' => 42
                ]),
                ['variant.attribute.fancyStuff:range (23 to 42)']
            ],
            [
                ['attributeId' => 'variant.attribute.fancyStuff', 'attributeType' => 'customUnknownTerm'],
                new TermFacet([
                    'handle' => 'variant.attribute.fancyStuff',
                    'terms' => ['1100101']
                ]),
                ['variant.attribute.fancyStuff:"1100101"']
            ],
            // Multi term test
            [
                ['attributeId' => 'variant.attribute.size', 'attributeType' => 'localizedEnum'],
                new TermFacet([
                    'handle' => 'variant.attribute.size',
                    'terms' => ['large', 'small', 'medium']
                ]),
                [
                    'variant.attribute.size.label.en:"large","small","medium"',
                ]
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
     * @return mixed
     */
    private function loadFixture(string $fileName)
    {
        return json_decode(file_get_contents(__DIR__ . '/_fixtures/' . $fileName), true);
    }
}
