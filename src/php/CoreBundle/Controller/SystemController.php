<?php

namespace Frontastic\Common\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Kore\DataObject\DataObject;

use Frontastic\UserBundle\Domain\MetaData;

class SystemController extends Controller
{
    public function versionAction(): JsonResponse
    {
        return new JsonResponse([
            'version' => $this->getParameter('version'),
            'environment' => $this->getParameter('env'),
        ]);
    }
}
