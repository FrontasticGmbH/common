<?php

namespace Frontastic\Common\CoreBundle\Domain;

class ClassToCatwalkPackageMigrationHandler
{
    public static function handleClass(
        string $className,
        string $originalNameSpace,
        string $newNameSpace,
        string $newClassName = null
    ) {
        $newClass = "$newNameSpace\\" . $newClassName ?? $className;
        $originalClass = "$originalNameSpace\\$className";
        $legacyClass = "$originalNameSpace\\Legacy$className";

        if (class_exists($newClass, true)) {
            if (!self::isProductionEnvironment()) {
                trigger_error("$originalClass is deprecated, please switch to $newClass");
            }
            class_alias($newClass, $originalClass);
        } else {
            if (!self::isProductionEnvironment()) {
                trigger_error(
                    'You are running a version of common library that requires an update of catwalk.
                    Please update to the latest catwalk version!'
                );
            }
            class_alias($legacyClass, $originalClass);
        }
    }

    private static function isProductionEnvironment(): bool
    {
        $environment = getenv('env');
        if (empty($environment) || $environment === 'prod') {
            return true;
        }

        return false;
    }
}
