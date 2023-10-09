<?php

namespace Frontastic\Common\Mvc\Exception;

use RuntimeException;

class FormAlreadyHandledException extends RuntimeException
{
    public function __construct(?string $name)
    {
        parent::__construct(sprintf(
            'The \Frontastic\Common\Mvc\FormRequest was already handled with form %s earlier. ' .
            'You can only use a FormRequest with exactly one form.',
            $name ?: 'unnamed'
        ));
    }
}
