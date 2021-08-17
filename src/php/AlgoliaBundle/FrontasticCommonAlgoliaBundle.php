<?php

namespace Frontastic\Common\AlgoliaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FrontasticCommonAlgoliaBundle extends Bundle
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
