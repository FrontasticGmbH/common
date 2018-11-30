<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;


use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;

class MapperTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider provideFacetRequestExamples
     */
    public function testFacetsToRequest($facetDefinition, $expectedFacetQuery)
    {
        $mapper = new Mapper();

        $actualFacetQueries = $mapper->facetsToRequest(
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
                'variant.attribute.size:range (* to *) as variant.attribute.size',
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
}
