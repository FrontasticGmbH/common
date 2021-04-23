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
        $version = false;
        $dir = __DIR__;
        do {
            if (file_exists($dir . '/environment') &&
                $version = parse_ini_file($dir . '/environment')['version'] ?? false) {
                break;
            }

            $dir = dirname($dir);
        } while (!$version && $dir);

        return new JsonResponse([
            'version' => $version,
            'environment' => $this->env,
        ]);
    }
}
