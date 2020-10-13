<?php

namespace Frontastic\Common\ProjectApiBundle\Controller;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\AccountApiBundle\Domain\Session;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\RequestVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AttributesController extends Controller
{
    public function searchableAttributesAction(Request $request, Context $context): array
    {
        $requestVerifier = $this->get(RequestVerifier::class);
        $requestVerifier->ensure($request, $this->getParameter('secret'));

        /* HACK: This request is stateless, so let the ContextService know that we do not need a session. */
        $request->attributes->set(Session::STATELESS, true);

        $productSearchApiFactory = $this->get(ProductSearchApiFactory::class);
        $productSearchApi = $productSearchApiFactory->factor($context->project);

        $attributes = $productSearchApi->getSearchableAttributes()->wait();

        return [
            'attributes' => $attributes,
            'ok' => true,
        ];
    }
}
