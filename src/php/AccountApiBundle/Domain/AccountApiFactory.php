<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreatorFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapAccountApi;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapDataMapper;
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

            case 'sap-commerce-cloud':
                $client = $this->container
                    ->get(SapClientFactory::class)
                    ->factorForProjectAndType($project, 'account');

                $accountApi = new SapAccountApi(
                    $client,
                    $this->container
                        ->get(SapLocaleCreatorFactory::class)
                        ->factor($project, $client),
                    new SapDataMapper($client)
                );
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
