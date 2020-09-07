<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;

interface ProductSearchApiFactory
{
    public function factor(Project $project): ProductSearchApi;
}
