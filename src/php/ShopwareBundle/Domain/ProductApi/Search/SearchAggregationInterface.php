<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search;

use JsonSerializable;

interface SearchAggregationInterface extends JsonSerializable
{
    public function setResultData(array $resultData): void;

    public function getResultData(): array;
}
