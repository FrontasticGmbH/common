<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor\NullFieldVisitor;
use PHPUnit\Framework\TestCase;

class ConfigurationSchemaTest extends TestCase
{
    private const SCHEMA_FIXTURE = [
        [
            'name' => 'First Section',
            'fields' => [
                [
                    'field' => 'aString',
                    'type' => 'string',
                    'default' => 'foobar',
                ],
            ]
        ],
        [
            'name' => 'Second Section',
            'fields' => [
                [
                    'field' => 'aGroup',
                    'type' => 'group',
                    'min' => 3,
                    'fields' => [
                        [
                            'field' => 'groupFirst',
                            'type' => 'number',
                            'default' => 23,
                        ],
                        [
                            'field' => 'groupSecond',
                            'type' => 'string',
                        ],
                    ]
                ]
            ]
        ]
    ];

    public function testGetCompleteValuesWithoutVisitor()
    {
        $configurationSchema = ConfigurationSchema::fromSchemaAndConfiguration(
            self::SCHEMA_FIXTURE,
            [
                'aGroup' => [
                    [
                        'groupSecond' => 'lalala',
                    ]
                ]
            ]
        );

        $this->assertEquals(
            [
                'aString' => 'foobar',
                'aGroup' => [
                    [
                        'groupSecond' => 'lalala',
                        'groupFirst' => 23,
                    ],
                    [
                        'groupFirst' => 23,
                        'groupSecond' => '',
                    ],
                    [
                    'groupFirst' => 23,
                    'groupSecond' => '',
                ]
            ]
            ],
            $configurationSchema->getCompleteValues()
        );
    }

}
