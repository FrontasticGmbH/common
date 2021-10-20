<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class InvalidCustomDataSourceTypeException extends \InvalidArgumentException
{
    public $message;
    public $error;

    public function __construct(string $message, string $error)
    {
        parent::__construct('Custom data source with type: ' . $message . 'does not exist');

        $this->message = $message;
        $this->error = $error;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
