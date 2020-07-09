<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\SprykerBundle\BaseApi\Factory\AbstractSprykerBaseFactory;

class SprykerAccountApiFactory extends AbstractSprykerBaseFactory
{
    /**
     * @param Project $project
     * @return AccountApi
     */
    public function factor(Project $project): AccountApi
    {
        return new SprykerAccountApi(
            $this->createSprykerClient($project->configuration),
            $this->getMapperResolver(),
            $this->getAccountHelper(),
            $this->getTokenDecoder()
        );
    }

    /**
     * @return AccountHelper
     */
    protected function getAccountHelper(): AccountHelper
    {
        return $this->container->get(AccountHelper::class);
    }

    /**
     * @return TokenDecoder
     */
    protected function getTokenDecoder(): TokenDecoder
    {
        return $this->container->get(TokenDecoder::class);
    }
}
