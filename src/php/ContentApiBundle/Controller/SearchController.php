<?php

namespace Frontastic\Common\ContentApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Frontastic\Backstage\ApiBundle\Domain\Context;
use Frontastic\Common\ContentApiBundle\Domain\Query;

class SearchController extends Controller
{
    public function listAction(Request $request, Context $context): array
    {
        $contentApiFactory = $this->get('Frontastic\Common\ContentApiBundle\Domain\ContentApiFactory');
        $contentApi = $contentApiFactory->factor($context->customer);

        $query = new Query(json_decode($request->getContent(), true));

        return [
            'result' => $contentApi->query($query),
        ];
    }
}
