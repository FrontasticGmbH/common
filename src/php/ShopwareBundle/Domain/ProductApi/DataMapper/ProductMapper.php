<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use Frontastic\Common\ProductApiBundle\Domain\Product;

class ProductMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'product';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $productData = $this->extractData($resource);


        return new Product();
    }


}
