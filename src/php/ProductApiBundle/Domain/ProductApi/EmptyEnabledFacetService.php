<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

class EmptyEnabledFacetService implements EnabledFacetService
{
    public function getEnabledFacetDefinitions(): array
    {
        return [];
    }
}
