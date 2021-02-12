<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi\LifecycleEventDecorator;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;

/**
 * Base implementation of the ProductSearchApi LifecycleDecorator, which should be used when writing own
 * LifecycleDecorators as base class for future type-safety and convenience reasons, as it will provide the needed
 * function naming as well as parameter type-hinting.
 *
 * The before* Methods will be obviously called *before* the original method is executed and will get all the parameters
 * handed over, which the original method will get called with. Overwriting this method can be useful if you want to
 * manipulate the handed over parameters by simply manipulating it.
 * These methods doesn't return anything.
 *
 * The after* Methods will be obviously called *after* the original method is executed and will get the unwrapped result
 * from the original method handed over. So if the original methods returns a Promise, the resolved value will be
 * handed over to this function here.
 * Overwriting this method could be useful if you want to manipulate the result.
 * These methods need to return null if nothing should be manipulating, thus will lead to the original result being
 * returned or they need to return the same data-type as the original method returns, otherwise you will get Type-Errors
 * at some point.
 *
 * In order to make this class available to the Lifecycle-Decorator, you will need to tag your service based on this
 * class with "productSearchApi.lifecycleEventListener": e.g. by adding the tag inside the `services.xml`
 * ```
 * <tag name="productSearchApi.lifecycleEventListener" />
 * ```
 */
abstract class BaseImplementation
{
    /*** getSearchableAttributes() ************************************************************************************/
    public function beforeGetSearchableAttributes(ProductSearchApi $productSearchApi): ?array
    {
        return null;
    }

    /**
     * @param ProductSearchApi $productSearchApi
     * @return ?Attribute[]
     */
    public function afterGetSearchableAttributes(ProductSearchApi $productSearchApi): ?array
    {
        return null;
    }

    /*** query() ******************************************************************************************************/
    public function beforeQuery(ProductSearchApi $productSearchApi, ProductQuery $query): ?array
    {
        return null;
    }

    public function afterQuery(ProductSearchApi $productSearchApi, ?Result $result): ?Result
    {
        return null;
    }
}
