<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreatorFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapProjectApi;

class DefaultProjectApiFactory implements ProjectApiFactory
{
    /**
     * @var ClientFactory
     */
    private $commercetoolsClientFactory;

    /**
     * @var CommercetoolsLocaleCreatorFactory
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

    public function __construct(
        ClientFactory $commercetoolsClientFactory,
        CommercetoolsLocaleCreatorFactory $commercetoolsLocaleCreatorFactory,
        SapClientFactory $sapClientFactory,
        SapLocaleCreatorFactory $sapLocaleCreatorFactory
    ) {
        $this->commercetoolsClientFactory = $commercetoolsClientFactory;
        $this->commercetoolsLocaleCreatorFactory = $commercetoolsLocaleCreatorFactory;
        $this->sapClientFactory = $sapClientFactory;
        $this->sapLocaleCreatorFactory = $sapLocaleCreatorFactory;
    }

    public function factor(Project $project): ProjectApi
    {
        $productConfig = $project->getConfigurationSection('product');

        switch ($productConfig->engine) {
            case 'commercetools':
                $client = $this->commercetoolsClientFactory->factorForProjectAndType($project, 'product');
                return new ProjectApi\Commercetools(
                    $client,
                    $this->commercetoolsLocaleCreatorFactory->factor($project, $client),
                    $project->languages
                );
            case 'sap-commerce-cloud':
                $client = $this->sapClientFactory->factorForProjectAndType($project, 'product');
                return new SapProjectApi(
                    $client,
                    $this->sapLocaleCreatorFactory->factor($project, $client),
                    $project->languages
                );
        }

        throw new \OutOfBoundsException(
            "No product API configured for project {$project->name}. " .
            "Check the provisioned customer configuration in app/config/customers/."
        );
    }
}
