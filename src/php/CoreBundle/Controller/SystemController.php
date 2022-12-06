<?php

namespace Frontastic\Common\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SystemController extends AbstractController
{
    public function __construct(private readonly string $env)
    {
        
    }

    /**
     * This endpoint exists as a health check for deployments. Do NOT remove.
     */
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
