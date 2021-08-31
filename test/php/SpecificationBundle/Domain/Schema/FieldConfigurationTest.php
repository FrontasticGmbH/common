<?php

namespace Frontastic\Common\SpecificationBundle\Domain\Schema;

use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldConfiguration;
use PHPUnit\Framework\TestCase;

class FieldConfigurationTest extends TestCase
{
    /**
     * @dataProvider provideDefaultTranslatableExamples
     */
    public function testDefaultTranslatable(string $fieldTypeFixture, bool $expectedIsTranslatable)
    {
        $fieldConfiguration = FieldConfiguration::fromSchema([
            'type' => $fieldTypeFixture,
            'field' => 'testField',
        ]);

        $this->assertSame($expectedIsTranslatable, $fieldConfiguration->isTranslatable());
    }

    public static function provideDefaultTranslatableExamples(): array
    {
        return [
            ['string', true],
            ['text', true],
            ['markdown', true],
            ['json', true],

            ['boolean', false],
            ['custom', false],
            ['decimal', false],
            ['enum', false],
            ['integer', false],
            ['media', false],
            ['node', false],
            ['number', false],
            ['reference', false],
            ['stream', false],
            ['tree', false],
        ];
    }
}
