<?php declare(strict_types = 1);

namespace Frontastic\Common\CoreBundle\Domain\Api;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class FactoryServiceLocator implements ContainerInterface, ServiceSubscriberInterface
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\DependencyInjection\ServiceLocator
     */
    private $taggedServiceContainer;

    /**
     * @var string[]
     */
    private static $subscribedServices = [];

    public function __construct(ContainerInterface $container, ServiceLocator $taggedServiceContainer)
    {
        $this->container = $container;
        $this->taggedServiceContainer = $taggedServiceContainer;
    }

    public function has($id): bool
    {
        return $this->taggedServiceContainer->has($id);
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get($id)
    {
        if ($this->taggedServiceContainer->has($id)) {
            return $this->taggedServiceContainer->get($id);
        }

        return $this->container->get($id);
    }

    public static function addSubscribedService(string $serviceId): void
    {
        self::$subscribedServices[] = $serviceId;
    }

    public static function getSubscribedServices(): array
    {
        return self::$subscribedServices;
    }
}
