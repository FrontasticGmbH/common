<?php

namespace Frontastic\Common\CoreBundle\Domain;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestProvider
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getCurrentRequest() : ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}
