<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class InvalidSchemaException extends \DomainException
{
    public $message;
    public $error;

    public function __construct(string $message, string $error)
    {
        parent::__construct('Invalid Schema: ' . $message);

        $this->message = $message;
        $this->error = $error;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
