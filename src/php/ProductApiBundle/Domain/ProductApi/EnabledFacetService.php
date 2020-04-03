<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

interface EnabledFacetService
{
    /**
     * @return FacetDefinition[]
     */
    public function getEnabledFacetDefinitions(): array;
}
