<?php

namespace Frontastic\Common\Mvc;

class Headers
{
    /** @var array<string,string> */
    public $values = [];

    /**
     * @param array<string,string> $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }
}
