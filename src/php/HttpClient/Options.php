<?php

namespace Frontastic\Common\HttpClient;

use Kore\DataObject\DataObject;

class Options extends DataObject
{
    /** @var int|float timeout for the complete request in seconds */
    public $timeout = 1;
}
