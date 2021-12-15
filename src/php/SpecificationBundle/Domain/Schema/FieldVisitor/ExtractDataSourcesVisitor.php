<?php

namespace Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor;

use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldConfiguration;
use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor;
use Frontastic\Common\SpecificationBundle\Domain\Schema\StreamFieldConfiguration;

class ExtractDataSourcesVisitor implements FieldVisitor
{
    /**
     * @var string
     */
    private array $foundStreamTypes;

    public function __construct()
    {
        $this->foundStreamTypes = [];
    }

    public function processField(FieldConfiguration $configuration, $value, array $fieldPath)
    {
        if ($configuration instanceof StreamFieldConfiguration) {
            $this->foundStreamTypes[] = $configuration->getStreamType();
        }
        return $value;
    }

    public function getFoundStreamTypes()
    {
        return array_unique($this->foundStreamTypes);
    }

}
