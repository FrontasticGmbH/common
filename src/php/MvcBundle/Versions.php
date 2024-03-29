<?php

namespace Frontastic\Common\MvcBundle;

use Composer\InstalledVersions;

class Versions
{
    public static function isSecurityVersion6(): bool
    {
        $version = null;
        if (InstalledVersions::isInstalled('symfony/symfony')) {
            $version = InstalledVersions::getVersion('symfony/symfony');
        } elseif (InstalledVersions::isInstalled('symfony/security')) {
            $version = InstalledVersions::getVersion('symfony/security');
        }

        if ($version !== null && version_compare($version, '6.0.0') >= 0) {
            return true;
        }

        return false;
    }
}
