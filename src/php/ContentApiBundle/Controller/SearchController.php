<?php

namespace Frontastic\Common\ContentApiBundle\Controller;

use Frontastic\Common\ContentApiBundle\Domain;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;

class SearchController extends Controller
{
    public function listAction(Request $request, Context $context): array
    {
        $contentApiFactory = $this->get('Frontastic\Common\ContentApiBundle\Domain\ContentApiFactory');
        $contentApi = $contentApiFactory->factor($context->project);

        $query = Domain\Query::fromArray(json_decode($request->getContent(), true));

        return [
            'result' => $contentApi->query($query),
        ];
    }
}
