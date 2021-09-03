<?php

namespace Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor;

use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldConfiguration;
use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor;
use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor\SequentialFieldVisitor;
use PHPUnit\Framework\TestCase;

class SequentialFieldVisitorTest extends TestCase
{
    public function testSequentialVisit()
    {
        $firstVisitor = new class implements FieldVisitor {
            public function processField(FieldConfiguration $configuration, $value, array $fieldPath)
            {
                return $value . "-first";
            }
        };
        $secondVisitor = new class implements FieldVisitor {
            public function processField(FieldConfiguration $configuration, $value, array $fieldPath)
            {
                return $value . ".second";
            }
        };

        $sequentialVisitor = new SequentialFieldVisitor([
            $firstVisitor,
            $secondVisitor
        ]);

        $this->assertEquals(
            'input-first.second',
            $sequentialVisitor->processField(
                \Phake::mock(FieldConfiguration::class),
                'input',
                []
            )
        );

    }

}
