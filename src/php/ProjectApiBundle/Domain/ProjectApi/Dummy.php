<?php

namespace Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;

use Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;

class Dummy implements ProjectApi
{
    public function getSearchableAttributes(): array
    {
        return [];
    }
}
