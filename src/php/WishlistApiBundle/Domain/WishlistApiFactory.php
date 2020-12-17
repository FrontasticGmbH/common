<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;

interface WishlistApiFactory
{
    public function factor(Project $project): WishlistApi;
}
