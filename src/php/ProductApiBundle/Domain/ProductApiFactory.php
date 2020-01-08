<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;

interface ProductApiFactory
{
    public function factor(Project $project): ProductApi;
}
