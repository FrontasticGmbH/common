<?php

namespace Frontastic\Common\TestUtilities;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerTestTrait
{
    abstract protected static function getContainer(): ContainerInterface;

    public function testContainerServicesAllFunctional()
    {
        /** @var Container $container */
        $container = self::getContainer();

        foreach ($container->getServiceIds() as $serviceId) {
            if ($this->shouldIgnore($serviceId)) {
                continue;
            }

            try {
                $service = $container->get($serviceId);
            } catch (\Throwable $e) {
                $this->fail(sprintf(
                    "Failed creating service '%s' with message '%s'. Error details:\n\n%s",
                    $serviceId,
                    $e->getMessage(),
                    (string) $e
                ));
            }

            $this->assertIsObject(
                $service,
                sprintf("Service '%s' was not build as an object.", $serviceId)
            );
        }
    }

    protected function shouldIgnore(string $serviceId): bool
    {
        return (stripos($serviceId, 'frontastic') === false);
    }
}
