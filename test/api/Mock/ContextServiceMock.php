<?php

namespace Frontastic\Common\ApiTests\Mock;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;

class ContextServiceMock
{
    public function createContextFromRequest()
    {
        return new Context;
    }
}
