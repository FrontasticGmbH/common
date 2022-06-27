<?php

namespace Frontastic\Common\AlgoliaBundle\Domain;

use Frontastic\Common\AlgoliaBundle\Domain\ProductSearchApi\Mapper;

class AlgoliaMapperFactory
{
    public function factorForConfigs(
        object $typeSpecificConfig,
        ?object $algoliaConfig = null
    ): Mapper {
        $apiVersion = $typeSpecificConfig->apiVersion ?? $algoliaConfig->apiVersion ?? null;

        return new Mapper($apiVersion);
    }
}
