<?php

namespace Frontastic\Common\HttpClient;

use Frontastic\Common\HttpClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FactoryTest extends TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var ContainerInterface|MockObject
     */
    private $containerMock;

    public function setUp()
    {
        $this->containerMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $this->factory = new Factory($this->containerMock);

        $this->containerMock->expects(self::any())->method('get')->willReturnCallback(
            function ($serviceId) {
                $services = [
                    'logger' => $this->getMockBuilder(LoggerInterface::class)->getMock(),
                    \Domnikl\Statsd\Client::class => $this->getMockBuilder(\Domnikl\Statsd\Client::class)
                            ->disableOriginalConstructor()->getMock(),
                ];

                if (!isset($services[$serviceId])) {
                    throw new \Exception('Service not found!');
                }
                return $services[$serviceId];
            });
    }

    public function testCreateDefault()
    {
        $httpClient = $this->factory->create('foo');

        $aggregates = $this->unrollAggregates($httpClient);

        $this->assertCount(3, $aggregates);
    }

    public function testCreateSigning()
    {
        $httpClient = $this->factory->create('foo', new Configuration([
            'signatureSecret' => 'some_secret',
        ]));

        $aggregates = $this->unrollAggregates($httpClient);

        $signingClient = $this->getClient($aggregates, Signing::class);

        self::assertAttributeEquals(
            'some_secret',
            'sharedSecret',
            $signingClient
        );
    }

    private function getClient(array $aggregates, string $class): HttpClient
    {
        foreach ($aggregates as $aggregate) {
            if ($aggregate['class'] === $class) {
                return $aggregate['instance'];
            }
        }
        $this->fail('Aggregate client of class "' . $class . '" not found.');
    }

    private function unrollAggregates(HttpClient $client)
    {
        $aggregates = [
            [
                'class' => get_class($client),
                'instance' => $client,
            ]
        ];

        while (($reflection = new \ReflectionObject($client)) && $reflection->hasProperty('aggregate')) {
            $property = $reflection->getProperty('aggregate');
            $property->setAccessible(true);

            $client = $property->getValue($client);

            $aggregates[] = [
                'class' => get_class($client),
                'instance' => $client,
            ];
        }

        return $aggregates;
    }
}
