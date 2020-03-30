<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\LifecycleEventDecorator;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\ProductType;

/**
 * Base implementation of the ProductApi LifecycleDecorator, which should be used when writing own LifecycleDecorators
 * as base class for future type-safety and convenience reasons, as it will provide the needed function naming as well
 * as parameter type-hinting.
 *
 * The before* Methods will be obviously called *before* the original method is executed and will get all the parameters
 * handed over, which the original method will get called with. Overwriting this method can be useful if you want to
 * manipulate the handed over parameters by simply manipulating it.
 * These methods doesn't return anything.
 *
 * The after* Methods will be oviously called *after* the orignal method is executed and will get the unwrapped result
 * from the original method handed over. So if the original methods returns a Promise, the resolved value will be
 * handed over to this function here.
 * Overwriting this method could be useful if you want to manipulate the result.
 * These methods need to return null if nothing should be manipulating, thus will lead to the original result being
 * returned or they need to return the same data-type as the original method returns, otherwise you will get Type-Errors
 * at some point.
 *
 * In order to make this class available to the Lifecycle-Decorator, you will need to tag your service based on this
 * class with "productApi.lifecycleEventListener": e.g. by adding the tag inside the `services.xml`
 * ```
 * <tag name="productApi.lifecycleEventListener" />
 * ```
 */
abstract class BaseImplementation
{
    /*** getCategories() **********************************************************************************************/
    public function beforeGetCategories(ProductApi $productApi, CategoryQuery $query): void
    {
    }

    /**
     * @param ProductApi $productApi
     * @param Category[] $categories
     * @return Category[]|null
     */
    public function afterGetCategories(ProductApi $productApi, array $categories): ?array
    {
        return null;
    }

    /*** getProductTypes() ********************************************************************************************/
    public function beforeGetProductTypes(ProductApi $productApi, ProductTypeQuery $query): void
    {
    }

    /**
     * @param ProductApi $productApi
     * @param ProductType[] $productTypes
     * @return ProductType[]|null
     */
    public function afterGetProductTypes(ProductApi $productApi, array $productTypes): ?array
    {
        return null;
    }

    /*** getProduct() *************************************************************************************************/
    public function beforeGetProduct(
        ProductApi $productApi,
        $query,
        string $mode = ProductApi::QUERY_SYNC
    ): void {
    }

    public function afterGetProduct(ProductApi $productApi, ?Product $product): ?Product
    {
        return null;
    }

    /*** query() ******************************************************************************************************/
    public function beforeQuery(
        ProductApi $productApi,
        ProductQuery $query,
        string $mode = ProductApi::QUERY_SYNC
    ): void {
    }

    public function afterQuery(ProductApi $productApi, ?Result $result): ?Result
    {
        return null;
    }
}
