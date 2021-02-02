<?php

namespace Frontastic\Common\ProductApiBundle\Controller;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQueryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

/**
 * @deprecated use "Frontastic\Catwalk\FrontendBundle\Controller\ProductSearchController" instead.
 */
class LegacySearchController extends Controller
{
    public function listAction(Request $request, Context $context): array
    {
        $productSearchApi = $this->get('frontastic.catwalk.product_search_api');

        $query = ProductQueryFactory::queryFromParameters(
            ['locale' => $context->locale],
            Json::decode($request->getContent(), true)
        );

        return [
            'result' => $productSearchApi->query($query)->wait(),
        ];
    }
}
