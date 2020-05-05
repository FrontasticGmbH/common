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

    public function setUp()
    {
        $this->factory = new Factory(
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );
    }

    public function testCreateDefault()
    {
        $httpClient = $this->factory->create('foo');

        $aggregates = $this->unrollAggregates($httpClient);

        $this->assertCount(2, $aggregates);
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
        $result = null;
        foreach ($aggregates as $aggregate) {
            if ($aggregate['class'] === $class) {
                $result = $aggregate['instance'];
                break;
            }
        }

        if ($result === null) {
            $this->fail('Aggregate client of class "' . $class . '" not found.');
        }

        return $result;
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
