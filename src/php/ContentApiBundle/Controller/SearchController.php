<?php

namespace Frontastic\Common\ContentApiBundle\Controller;

use Frontastic\Common\ContentApiBundle\Domain;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;

class SearchController extends Controller
{
    public function showAction(Request $request, Context $context): array
    {
        $contentApiFactory = $this->get('Frontastic\Common\ContentApiBundle\Domain\ContentApiFactory');
        /** @var Domain\ContentApi $contentApi */
        $contentApi = $contentApiFactory->factor($context->project);

        $requestParameters = json_decode($request->getContent(), true);
        if (!isset($requestParameters['contentId'])) {
            throw new Exception("contentId is not set in request");
        }
        $contentId = $requestParameters['contentId'];

        return [
            'result' => $contentApi->getContent($contentId, $context->locale)->wait(),
        ];
    }

    public function listAction(Request $request, Context $context): array
    {
        $contentApiFactory = $this->get('Frontastic\Common\ContentApiBundle\Domain\ContentApiFactory');
        /** @var Domain\ContentApi $contentApi */
        $contentApi = $contentApiFactory->factor($context->project);

        $query = Domain\Query::fromArray(json_decode($request->getContent(), true));

        return [
            'result' => $contentApi->query($query, $context->locale)->wait(),
        ];
    }
}
