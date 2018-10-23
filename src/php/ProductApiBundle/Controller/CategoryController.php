<?php

namespace Frontastic\Common\ProductApiBundle\Controller;

use Frontastic\Backstage\ApiBundle\Domain\Context;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function listAction(Context $context): array
    {
        /** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory $productApiFactory */
        $productApiFactory = $this->get('Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory');

        $productApi = $productApiFactory->factorFromConfiguration(
            (isset($context->project) ? $context->project->configuration : $context->customer->configuration)
        );

        $query = new CategoryQuery([
            'locale' => $context->locale,
            'limit' => 250
        ]);

        return [
            'categories' => $productApi->getCategories($query),
        ];
    }
}
