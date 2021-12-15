<?php

namespace Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor;

use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldConfiguration;
use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor;

class ExtractDataSourcesVisitor implements FieldVisitor
{
    /**
     * @var string
     */
    private array $foundDataSourceTypes;

    public function __construct()
    {
        $this->foundDataSourceTypes = [];
    }

    public function processField(FieldConfiguration $configuration, $value, array $fieldPath)
    {
        if($configuration->getType()=== 'stream') {
            //$this->foundDataSourceTypes[] = $configuration->getDataSourceTypes()
        }
        return $value;
    }

    public function getFoundDataSourceTypes()
    {
        return array_unique($this->foundDataSourceTypes);
    }

}
