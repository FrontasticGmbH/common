<?php

namespace Frontastic\Common\Exception;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class NextJsOnlyException extends NotAcceptableHttpException
{
    public function __construct()
    {
        return parent::__construct('You have no nextjs features enabled.');
    }
}
