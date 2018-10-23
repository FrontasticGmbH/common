<?php

namespace Frontastic\Common\ProductApiBundle\Controller;

use Frontastic\Backstage\ApiBundle\Domain\Context;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    public function listAction(Request $request, Context $context): array
    {
        /** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory $productApiFactory */
        $productApiFactory = $this->get('Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory');

        $productApi = $productApiFactory->factorFromConfiguration(
            (isset($context->project) ? $context->project->configuration : $context->customer->configuration)
        );

        $query = new ProductQuery(
            array_merge(
                ['locale' => $context->locale],
                json_decode($request->getContent(), true)
            )
        );

        return [
            'result' => $productApi->query($query),
        ];
    }
}
