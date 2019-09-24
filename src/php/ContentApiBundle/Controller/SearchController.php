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
        if (isset($requestParameters['contentId'])) {
            $contentId = $requestParameters['contentId'];
            $result = $contentApi->getContent($contentId, $context->locale);
        } elseif (isset($requestParameters['contentIds'])) {
            $contentIds = $requestParameters['contentIds'];
            $query = new Domain\Query(['contentIds' => $contentIds]);
            $result = $contentApi->query($query, $context->locale);
        } else {
            throw new \RuntimeException("either contentId nor contentIds is set in request");
        }

        return [
            'result' => $result->wait(),
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
