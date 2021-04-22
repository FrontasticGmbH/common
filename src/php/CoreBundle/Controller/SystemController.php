<?php

namespace Frontastic\Common\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Kore\DataObject\DataObject;

use Frontastic\UserBundle\Domain\MetaData;

class SystemController extends AbstractController
{
    private string $version;
    private string $env;

    public function __construct(string $version, string $env)
    {
        $this->version = $version;
        $this->env = $env;
    }

    public function versionAction(): JsonResponse
    {
        return new JsonResponse([
            'version' => getenv('version') ?: $this->version,
            'environment' => $this->env,
        ]);
    }
}
