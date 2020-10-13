<?php

namespace Frontastic\Common\HttpClient;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class Configuration extends DataObject
{
    /**
     * @var Options
     */
    public $options = null;

    /**
     * List (not hashmap!) of headers
     *
     * @var string[]
     */
    public $defaultHeaders = [];

    /**
     * @var ?string
     */
    public $signatureSecret = null;

    /**
     * @var bool
     */
    public $collectStats = true;

    /**
     * @var bool
     */
    public $collectProfiling = true;
}
