<?php

namespace Frontastic\Common\SprykerBundle\Domain\Project;

use Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;
use Frontastic\Common\SprykerBundle\Domain\SprykerSalutation;

interface SprykerProjectApiInterface extends ProjectApi
{
    /**
     * @return SprykerSalutation[]
     */
    public function getSalutations(): array;
}
