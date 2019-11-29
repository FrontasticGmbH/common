<?php

namespace Frontastic\Common\DevelopmentBundle;

use DeepCopy\DeepCopy;

/**
 * ATTENTION: This is only for development purposes!
 *
 * This class deals as a var_dump() style gateway to logging debug-messages during development. IT SHOULD NEVER BE
 * USED IN PRODUCTION.
 *
 * TODO: Use a CI scan to identify accidental commits of Debugger:: calls.
 */
class Debugger
{
    /**
     * @var array
     */
    private static $debugMessages = [];

    /**
     * @var DeepCopy
     */
    private static $copier;

    public static function log(...$args)
    {
        // Copy logged objects to avoid instance changes
        self::$debugMessages[] = self::getCopier()->copy($args);
    }

    public static function getMessages(): array
    {
        return self::$debugMessages;
    }

    private static function getCopier(): DeepCopy
    {
        if (self::$copier === null) {
            self::$copier = new DeepCopy();
        }
        return self::$copier;
    }
}
