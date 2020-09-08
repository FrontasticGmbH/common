<?php

namespace Frontastic\Common\SprykerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FrontasticCommonSprykerBundle extends Bundle
{
    /**
     * Compatibility with QafooLabs/NoFrameworkBundle
     *
     * @return ?string
     */
    public function getParent()
    {
        return null;
    }
}
