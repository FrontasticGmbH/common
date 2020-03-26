<?php declare(strict_types = 1);

namespace Frontastic\Common\CoreBundle\Domain\Api;

use Symfony\Component\DependencyInjection\ServiceLocator;

class FactoryServiceLocator
{
    private const SERVICE_ID_TEMPLATE_CLIENT_FACTORY = '%s.client_factory';
    private const SERVICE_ID_TEMPLATE_DATA_MAPPER = '%s.data_mapper';
    private const SERVICE_ID_TEMPLATE_LOCALE_CREATOR_FACTORY = '%s.locale_creator_factory';

    /**
     * @var \Symfony\Component\DependencyInjection\ServiceLocator
     */
    private $serviceLocator;

    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function resolveClientFactory(string $engine): object
    {
        $service = $this->buildClientFactoryServiceId($engine);

        return $this->serviceLocator->get($service);
    }

    public function resolveDataMapper(string $engine): object
    {
        $service = $this->buildDataMapperServiceId($engine);

        return $this->serviceLocator->get($service);
    }

    public function resolveLocaleCreatorFactory(string $engine): object
    {
        $service = $this->buildLocaleCreatorFactoryServiceId($engine);

        return $this->serviceLocator->get($service);
    }

    private function buildClientFactoryServiceId(string $engine): string
    {
        return sprintf(self::SERVICE_ID_TEMPLATE_CLIENT_FACTORY, $engine);
    }

    private function buildDataMapperServiceId(string $engine): string
    {
        return sprintf(self::SERVICE_ID_TEMPLATE_DATA_MAPPER, $engine);
    }

    private function buildLocaleCreatorFactoryServiceId(string $engine): string
    {
        return sprintf(self::SERVICE_ID_TEMPLATE_LOCALE_CREATOR_FACTORY, $engine);
    }


}
