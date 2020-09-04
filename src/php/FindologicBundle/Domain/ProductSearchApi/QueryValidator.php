<?php

namespace Frontastic\Common\FindologicBundle\Domain\ProductSearchApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;

class QueryValidator
{
    public function isSupported(ProductQuery $query)
    {
        $hasMultipleSortFields = !empty($query->sortAttributes)
            && is_array($query->sortAttributes)
            && count($query->sortAttributes) > 1;

        if ($hasMultipleSortFields) {
            return ValidationResult::createUnsupported('Sorting by more than one field is not supported.');
        }

        if (!empty($query->sku) || !empty($query->skus)) {
            return ValidationResult::createUnsupported('Searching for SKUs is not supported.');
        }

        if (!empty($query->productId) || !empty($query->productIds)) {
            return ValidationResult::createUnsupported('Searching for productIds is not supported.');
        }

        return ValidationResult::createValid();
    }
}
