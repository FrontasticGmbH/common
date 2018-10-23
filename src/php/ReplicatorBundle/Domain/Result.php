<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Kore\DataObject\DataObject;

class Result extends DataObject
{
    /**
     * @var bool
     */
    public $ok = true;

    /**
     * @var array
     */
    public $payload = [];

    /**
     * @var string
     */
    public $message = null;

    /**
     * @var string
     */
    public $file;

    /**
     * @var int
     */
    public $line;

    /**
     * @var array
     */
    public $stack;

    public static function fromThrowable(\Throwable $e)
    {
        $result = new static([
            'ok' => false,
            'message' => $e->getMessage(),
        ]);

        if (\AppKernel::getDebug()) {
            $result->file = $e->getFile();
            $result->line = $e->getLine();
            $result->stack = $e->getTraceAsString();
        }

        return $result;
    }
}
