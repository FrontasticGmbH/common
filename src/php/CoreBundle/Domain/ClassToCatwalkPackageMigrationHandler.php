<?php

namespace Frontastic\Common\CoreBundle\Domain;

class ClassToCatwalkPackageMigrationHandler
{
    public static function handleClass(
        string $className,
        string $originalNameSpace,
        string $newNameSpace,
        ?string $newClassName = null
    ) {
        $newClass = "$newNameSpace\\" . $newClassName ?? $className;
        $originalClass = "$originalNameSpace\\$className";

        if (!self::isProductionEnvironment()) {
            trigger_error("$originalClass is deprecated, please switch to $newClass");
        }
        class_alias($newClass, $originalClass);
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
