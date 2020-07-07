<?php

namespace Frontastic\Common\SprykerBundle\Domain\Project;

use Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SprykerBundle\BaseApi\Factory\AbstractSprykerBaseFactory;

class SprykerProjectApiFactory extends AbstractSprykerBaseFactory implements ProjectApiFactory
{
    /**
     * @param \Frontastic\Common\ReplicatorBundle\Domain\Project $project
     * @return \Frontastic\Common\ProjectApiBundle\Domain\ProjectApi
     */
    public function factor(Project $project): ProjectApi
    {
        return new SprykerProjectApi(
            $this->createSprykerClient($project->configuration),
            $this->getMapperResolver()
        );
    }
}
