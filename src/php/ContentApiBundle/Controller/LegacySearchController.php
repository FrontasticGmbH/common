<?php

namespace Frontastic\Common\ContentApiBundle\Controller;

use Frontastic\Common\ContentApiBundle\Domain;
use Frontastic\Common\ContentApiBundle\Domain\ContentQueryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

/**
 * @deprecated use "Frontastic\Catwalk\FrontendBundle\Controller\ContentSearchController" instead.
 */
class LegacySearchController extends Controller
{
    public function showAction(Request $request, Context $context): array
    {
        $contentApiFactory = $this->get('Frontastic\Common\ContentApiBundle\Domain\ContentApiFactory');
        /** @var Domain\ContentApi $contentApi */
        $contentApi = $contentApiFactory->factor($context->project);

        $requestParameters = Json::decode($request->getContent(), true);
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
            'result' => $result,
        ];
    }

    public function listAction(Request $request, Context $context): array
    {
        $contentApiFactory = $this->get('Frontastic\Common\ContentApiBundle\Domain\ContentApiFactory');
        /** @var Domain\ContentApi $contentApi */
        $contentApi = $contentApiFactory->factor($context->project);

        $query = ContentQueryFactory::queryFromRequest($request);

        return [
            'result' => $contentApi->query($query, $context->locale),
        ];
    }
}
