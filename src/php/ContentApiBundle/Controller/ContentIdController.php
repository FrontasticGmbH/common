<?php

namespace Frontastic\Common\ContentApiBundle\Controller;

use Frontastic\Common\ContentApiBundle\Domain;
use Frontastic\Common\ContentApiBundle\Domain\ContentQueryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;

class ContentIdController extends Controller
{
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
