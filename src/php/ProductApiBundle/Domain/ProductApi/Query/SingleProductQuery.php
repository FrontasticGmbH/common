<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\InvalidQueryException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

/**
 * This class represents a query for a single product. The product can be queried either by the product ID or by the
 * SKU.
 */
class SingleProductQuery extends Query
{
    /**
     * @var ?string
     */
    public $productId;

    /**
     * @var ?string
     */
    public $sku;

    /**
     * @param SingleProductQuery|ProductQuery $query
     * @deprecated This only exists for converting legacy uses of the `ProductApi::getProduct` to the new interface
     */
    public static function fromLegacyQuery($query): SingleProductQuery
    {
        if ($query instanceof SingleProductQuery) {
            return $query;
        }
        if ($query instanceof ProductQuery) {
            return static::fromValues([
                'productId' => $query->productId,
                'sku' => $query->sku,
                'locale' => $query->locale,
                'loadDangerousInnerData' => $query->loadDangerousInnerData,
            ]);
        }

        throw new InvalidQueryException(sprintf(
            'query needs to be of type %s or %s, got %s',
            SingleProductQuery::class,
            ProductQuery::class,
            get_class($query)
        ));
    }

    public static function byProductIdWithLocale(string $productId, string $locale): SingleProductQuery
    {
        return static::fromValues([
            'productId' => $productId,
            'locale' => $locale,
        ]);
    }

    public static function bySkuWithLocale(string $sku, string $locale): SingleProductQuery
    {
        return static::fromValues([
            'sku' => $sku,
            'locale' => $locale,
        ]);
    }

    public function validate(): void
    {
        $this->validateProperty('locale', 'string');
        $this->validateProperty('loadDangerousInnerData', 'boolean');
        $this->validateProperty('productId', 'string');
        $this->validateProperty('sku', 'string');

        if ($this->locale === null || $this->locale === '') {
            throw InvalidQueryException::emptyLocale();
        }

        if ($this->productId === null && $this->sku === null) {
            throw new InvalidQueryException('either product ID or SKU need to be specified');
        }
        if ($this->productId !== null && $this->sku !== null) {
            throw new InvalidQueryException('can not specify product ID and SKU');
        }
    }

    private static function fromValues(array $values): SingleProductQuery
    {
        $query = new static($values);
        $query->validate();
        return $query;
    }
}
