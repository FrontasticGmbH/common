<?php

namespace Frontastic\Common;

use Symfony\Component\Dotenv\Dotenv;

class EnvironmentResolver
{
    public function loadEnvironmentVariables(array $directories, array $baseConfig = []): void
    {
        $dotEnv = new Dotenv(true);

        $dotEnv->populate($baseConfig);

        foreach ($directories as $directory) {
            if (file_exists($directory . '/environment')) {
                $dotEnv->load($directory . '/environment');
            }
            if (file_exists($directory . '/environment.local')) {
                $dotEnv->load($directory . '/environment.local');
            }
            if (getenv('CONFIG') && file_exists(getenv('CONFIG'))) {
                $files[] = getenv('CONFIG');
                $dotEnv->load(getenv('CONFIG'));
            }
        }
    }
}
