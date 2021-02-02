<?php

namespace Frontastic\Common\ProductApiBundle\Controller;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated use "Frontastic\Catwalk\FrontendBundle\Controller\ProductCategoryController" instead.
 */
class LegacyCategoryController extends Controller
{
    public function listAction(Request $request, Context $context): array
    {
        /** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory $productApiFactory */
        $productApiFactory = $this->get('Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory');

        $productApi = $productApiFactory->factor($context->project);

        $query = new CategoryQuery([
            'locale' => $context->locale,
            'limit' => $request->query->getInt('limit', 250),
            'offset' => $request->query->getInt('offset', 0),
            'parentId' => $request->query->get('parentId'),
            'slug' => $request->query->get('slug'),
        ]);

        return [
            'categories' => $productApi->getCategories($query),
        ];
    }
}
