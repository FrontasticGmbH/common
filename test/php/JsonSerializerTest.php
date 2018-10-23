<?php

namespace Frontastic;

use Doctrine\Common\Collections\ArrayCollection;
use Frontastic\Common\JsonSerializer;

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
            [(object) ['foo' => 'bar'], ['_type' => 'stdClass', 'foo' => 'bar']],
            [(object) ['password' => 'bar'], ['_type' => 'stdClass', 'password' => '_FILTERED_']],
            [[(object) ['password' => 'bar']], [['_type' => 'stdClass', 'password' => '_FILTERED_']]],
            [new ArrayCollection([(object) ['password' => 'bar']]), [['_type' => 'stdClass', 'password' => '_FILTERED_']]],
            [new \DateTime('15.04.1981 8:16 CEST'), '1981-04-15T08:16:00+02:00'],
            [(object) ['foo' => [(object) ['password' => 'bar']]], ['_type' => 'stdClass', 'foo' => [['_type' => 'stdClass', 'password' => '_FILTERED_']]]],
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
}
