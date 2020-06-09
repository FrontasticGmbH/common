<?php

namespace Frontastic\Common\HttpClient;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class Options extends DataObject
{
    /**
     * Timeout for the complete request in seconds
     *
     * @var int|float
     */
    public $timeout = 1;
}
