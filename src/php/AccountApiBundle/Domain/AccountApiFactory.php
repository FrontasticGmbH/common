<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AccountApiFactory
{
    /** @var ContainerInterface */
    private $container;

    private $decorators = [];

    public function __construct(ContainerInterface $container, iterable $decorators)
    {
        $this->container = $container;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): AccountApi
    {
        $accountConfig = $project->getConfigurationSection('account');

        switch ($accountConfig->engine) {
            case 'commercetools':
                $client = $this->container
                    ->get(ClientFactory::class)
                    ->factorForProjectAndType($project, 'account');

                $accountApi = new AccountApi\Commercetools($client);
                break;

            default:
                throw new \OutOfBoundsException(
                    "No account API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new AccountApi\LifecycleEventDecorator($accountApi, $this->decorators);
    }
}
