<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class DefaultProjectApiFactory implements ProjectApiFactory
{
    /**
     * @var ClientFactory
     */
    private $commercetoolsClientFactory;

    /**
     * @var CommercetoolsLocaleCreatorFactory
     */
    private $localeCreatorFactory;

    public function __construct(
        ClientFactory $commercetoolsClientFactory,
        CommercetoolsLocaleCreatorFactory $localeCreatorFactory
    ) {
        $this->commercetoolsClientFactory = $commercetoolsClientFactory;
        $this->localeCreatorFactory = $localeCreatorFactory;
    }

    public function factor(Project $project): ProjectApi
    {
        $productConfig = $project->getConfigurationSection('product');

        switch ($productConfig->engine) {
            case 'commercetools':
                $client = $this->commercetoolsClientFactory->factorForProjectAndType($project, 'product');
                return new ProjectApi\Commercetools(
                    $client,
                    $this->localeCreatorFactory->factor($project, $client),
                    $project->languages
                );
        }

        throw new \OutOfBoundsException(
            "No product API configured for project {$project->name}. " .
            "Check the provisioned customer configuration in app/config/customers/."
        );
    }
}
