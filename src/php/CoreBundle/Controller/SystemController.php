<?php

namespace Frontastic\Common\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Kore\DataObject\DataObject;

use Frontastic\UserBundle\Domain\MetaData;

class SystemController extends AbstractController
{
    public function versionAction(): JsonResponse
    {
        return new JsonResponse([
            'version' => getenv('version') ?: $this->getParameter('version'),
            'environment' => $this->getParameter('env'),
        ]);
    }
}
