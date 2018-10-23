<?php

namespace Frontastic\Common\DevelopmentBundle;

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

    public static function log(... $args)
    {
        self::$debugMessages[] = $args;
    }

    public static function getMessages(): array
    {
        return self::$debugMessages;
    }
}
