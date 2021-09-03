<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldConfiguration;
use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor;
use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor\NullFieldVisitor;
use Frontastic\Common\SpecificationBundle\Domain\Schema\GroupFieldConfiguration;
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

    public function testGetCompleteValuesCallsVisitor()
    {
        $visitor = \Phake::mock(NullFieldVisitor::class);
        \Phake::when($visitor)->processField->thenCallParent();

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

        $configurationSchema->getCompleteValues($visitor);

        \Phake::verify($visitor)->processField(
            $this->isInstanceOf(FieldConfiguration::class),
            'foobar',
            ['aString']
        );
        \Phake::verify($visitor)->processField(
            $this->isInstanceOf(GroupFieldConfiguration::class),
            [
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
            ],
            ['aGroup']
        );

        \Phake::verify($visitor)->processField(
            $this->isInstanceOf(FieldConfiguration::class),
            23,
            ['aGroup', 0, 'groupFirst']
        );
        \Phake::verify($visitor)->processField(
            $this->isInstanceOf(FieldConfiguration::class),
            23,
            ['aGroup', 1, 'groupFirst']
        );
        \Phake::verify($visitor)->processField(
            $this->isInstanceOf(FieldConfiguration::class),
            23,
            ['aGroup', 2, 'groupFirst']
        );

        \Phake::verify($visitor)->processField(
            $this->isInstanceOf(FieldConfiguration::class),
            'lalala',
            ['aGroup', 0, 'groupSecond']
        );
        \Phake::verify($visitor)->processField(
            $this->isInstanceOf(FieldConfiguration::class),
            '',
            ['aGroup', 1, 'groupSecond']
        );
        \Phake::verify($visitor)->processField(
            $this->isInstanceOf(FieldConfiguration::class),
            '',
            ['aGroup', 2, 'groupSecond']
        );
    }

}
