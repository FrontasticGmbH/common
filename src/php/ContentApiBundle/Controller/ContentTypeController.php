<?php

namespace Frontastic\Common\ContentApiBundle\Controller;

use Frontastic\Common\ContentApiBundle\Domain\ContentApiFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Frontastic\Backstage\ApiBundle\Domain\Context;

class ContentTypeController extends Controller
{
    public function listAction(Context $context): array
    {
        /** @var ContentApiFactory $contentApiFactory */
        $contentApiFactory = $this->get(ContentApiFactory::class);
        $contentApi = $contentApiFactory->factor($context->customer);

        return [
            'contentTypes' => $contentApi->getContentTypes(),
        ];
    }
}
