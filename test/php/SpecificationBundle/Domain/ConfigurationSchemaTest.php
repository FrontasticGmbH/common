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

    public function testDoesNotAttendToDocumentaryFields()
    {
        $fixture = self::SCHEMA_FIXTURE;

        $fixture[0]['fields'][] = [
            'type' => 'description',
            'text' => 'foo',
        ];
        $fixture[1]['fields'][0]['fields'][] = [
            'type' => 'image',
            'text' => 'bar',
        ];

        $visitor = \Phake::mock(NullFieldVisitor::class);
        \Phake::when($visitor)->processField->thenCallParent();

        $configurationSchema = ConfigurationSchema::fromSchemaAndConfiguration(
            $fixture,
            []
        );

        $values = null;
        try {
            $values = $configurationSchema->getCompleteValues($visitor);
        } catch (\Throwable $e) {
            $this->fail('Completion failed: ' . $e->getMessage());
        }
        $this->assertNotNull($values);
    }

    public function testDoesNotRemoveUnknownFieldValuesOnCompletion()
    {
        $configurationSchema = ConfigurationSchema::fromSchemaAndConfiguration(
            self::SCHEMA_FIXTURE,
            [
                'unknownTop' => 'Do you know me?',
                'aGroup' => [
                    [
                        'groupSecond' => 'I am known',
                        'groupThird' => 'I am unknown',
                    ]
                ]
            ]
        );

        $values = $configurationSchema->getCompleteValues();

        $this->assertEquals('Do you know me?',$values['unknownTop']);
        $this->assertEquals('I am unknown', $values['aGroup'][0]['groupThird']);

        // Ensure valid field is also there
        $this->assertEquals('I am known', $values['aGroup'][0]['groupSecond']);
    }
}
