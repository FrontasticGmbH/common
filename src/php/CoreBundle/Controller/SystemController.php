<?php

namespace Frontastic\Common\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Kore\DataObject\DataObject;

use Frontastic\UserBundle\Domain\MetaData;

class SystemController extends AbstractController
{
    private string $env;

    public function __construct(string $env)
    {
        $this->env = $env;
    }

    public function versionAction(): JsonResponse
    {
        return new JsonResponse([
            'version' => $this->container->getParameter('version'),
            'environment' => $this->env,
        ]);
    }
}
