<?php declare(strict_types = 1);

namespace Frontastic\Common\ProjectApiBundle\Domain;

interface ProjectConfigApi
{
    /**
     * The result has to contain following keys: 'languages', 'countries', 'currencies'
     *
     * @return array
     */
    public function getProjectConfig(): array;
}
