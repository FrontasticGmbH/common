<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreatorFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapDataMapper;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapProductApi;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultProductApiFactory implements ProductApiFactory
{
    /**
     * @var ClientFactory
     */
    private $commercetoolsClientFactory;

    /**
     * @var Commercetools\Locale\CommercetoolsLocaleCreatorFactory
     */
    private $commercetoolsLocaleCreatorFactory;

    /**
     * @var SapClientFactory
     */
    private $sapClientFactory;

    /**
     * @var SapLocaleCreatorFactory
     */
    private $sapLocaleCreatorFactory;

    /**
     * @var array
     */
    private $decorators;

    public function __construct(
        ClientFactory $commercetoolsClientFactory,
        Commercetools\Locale\CommercetoolsLocaleCreatorFactory $commercetoolsLocaleCreatorFactory,
        SapClientFactory $sapClientFactory,
        SapLocaleCreatorFactory $sapLocaleCreatorFactory,
        iterable $decorators = []
    ) {
        $this->commercetoolsClientFactory = $commercetoolsClientFactory;
        $this->commercetoolsLocaleCreatorFactory = $commercetoolsLocaleCreatorFactory;
        $this->sapClientFactory = $sapClientFactory;
        $this->sapLocaleCreatorFactory = $sapLocaleCreatorFactory;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): ProductApi
    {
        $productConfig = $project->getConfigurationSection('product');

        switch ($productConfig->engine) {
            case 'commercetools':
                $client = $this->commercetoolsClientFactory->factorForProjectAndType($project, 'product');
                $productApi = new Commercetools(
                    $client,
                    new Commercetools\Mapper(),
                    $this->commercetoolsLocaleCreatorFactory->factor($project, $client),
                    $project->defaultLanguage
                );
                break;

            case 'sap-commerce-cloud':
                $client = $this->sapClientFactory->factorForProjectAndType($project, 'product');
                $productApi = new SapProductApi(
                    $client,
                    $this->sapLocaleCreatorFactory->factor($project, $client),
                    new SapDataMapper($client)
                );
                break;

            default:
                throw new \OutOfBoundsException(
                    "No product API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new ProductApi\LifecycleEventDecorator($productApi, $this->decorators);
    }
}
