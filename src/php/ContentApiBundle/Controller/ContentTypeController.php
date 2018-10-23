<?php

namespace Frontastic\Common\ContentApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Frontastic\Backstage\ApiBundle\Domain\Context;

class ContentTypeController extends Controller
{
    public function listAction(Context $context): array
    {
        $contentApiFactory = $this->get('Frontastic\Common\ContentApiBundle\Domain\ContentApiFactory');
        $contentApi = $contentApiFactory->factor($context->customer);

        return [
            'contentTypes' => $contentApi->getContentTypes(),
        ];
    }
}
