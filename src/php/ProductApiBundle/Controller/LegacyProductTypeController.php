<?php

namespace Frontastic\Common\ProductApiBundle\Controller;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @deprecated use "Frontastic\Catwalk\FrontendBundle\Controller\ProductTypeController" instead.
 */
class LegacyProductTypeController extends Controller
{
    public function listAction(Context $context): array
    {
        /** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory $productApiFactory */
        $productApiFactory = $this->get('Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory');

        $productApi = $productApiFactory->factor($context->project);

        $query = new ProductTypeQuery([
            'locale' => $context->locale,
        ]);

        return [
            'productTypes' => $productApi->getProductTypes($query),
        ];
    }
}
