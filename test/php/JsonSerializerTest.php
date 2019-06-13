<?php

namespace Frontastic\Common;

use Doctrine\Common\Collections\ArrayCollection;
use Frontastic\Common\JsonSerializer\ObjectEnhancer;

class JsonSerializerTest extends \PHPUnit\Framework\TestCase
{
    public function getSerilizationData()
    {
        return array(
            [42, 42],
            [42.23, 42.23],
            [true, true],
            [false, false],
            [null, null],
            ['string', 'string'],
            [[23, '42', null], [23, '42', null]],
            [['foo' => 'bar'], ['foo' => 'bar']],
            [(object) ['foo' => 'bar'], ['foo' => 'bar']],
            [(object) ['password' => 'bar'], ['password' => '_FILTERED_']],
            [[(object) ['password' => 'bar']], [['password' => '_FILTERED_']]],
            [new ArrayCollection([(object) ['password' => 'bar']]), [['password' => '_FILTERED_']]],
            [new \DateTime('15.04.1981 8:16 CEST'), '1981-04-15T08:16:00+02:00'],
            [(object) ['foo' => [(object) ['password' => 'bar']]], ['foo' => [['password' => '_FILTERED_']]]],
            [new JsonSerializerDataObjectFixture(), ['_type' => 'Frontastic\\Common\\JsonSerializerDataObjectFixture', 'test' => 'foo']]
        );
    }

    /**
     * @dataProvider getSerilizationData
     */
    public function testSerialize($input, $expected)
    {
        $serializer = new JsonSerializer(['password']);

        $result = $serializer->serialize($input);

        $this->assertEquals($expected, $result);
    }

    public function testSerializeWithEnhancer()
    {
        $serializer = new JsonSerializer();
        $enhancer = new class implements ObjectEnhancer {
            public function enhance($object): array
            {
                return ['someKey' => 23];
            }
        };
        $serializer->addEnhancer($enhancer);

        $object = new \stdClass();
        $object->property = 42;

        $actualData = $serializer->serialize($object);

        $this->assertEquals([
            'property' => 42,
            'someKey' => 23,
        ], $actualData);

    }
}
