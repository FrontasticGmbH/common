<?php

namespace Frontastic\Common\ProductApiBundle\Controller;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQueryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    public function listAction(Request $request, Context $context): array
    {
        $productApi = $this->get('frontastic.catwalk.product_api');

        $query = ProductQueryFactory::queryFromParameters(
            ['locale' => $context->locale],
            json_decode($request->getContent(), true)
        );

        return [
            'result' => $productApi->query($query),
        ];
    }
}
