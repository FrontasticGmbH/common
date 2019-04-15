<?php

namespace Frontastic\Common\CoreBundle\Domain;

use Kore\DataObject\DataObject;

class ErrorResult extends DataObject
{
    /**
     * @var boolean
     */
    public $ok = false;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $endpoint;

    /**
     * @var string
     */
    public $file;

    /**
     * @var integer
     */
    public $line;

    /**
     * @var string[]
     */
    public $stack;

    /**
     * @var string
     */
    public $code;

    /**
     * @var object
     */
    public $parameters;
}
