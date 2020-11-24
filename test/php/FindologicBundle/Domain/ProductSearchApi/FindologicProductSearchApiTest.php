<?php

namespace Frontastic\Common\FindologicBundle\Domain\ProductSearchApi;

use Frontastic\Common\FindologicBundle\Domain\FindologicClient;
use Frontastic\Common\FindologicBundle\Domain\FindologicEndpointConfig;
use Frontastic\Common\CoreBundle\Domain\RequestProvider;
use Frontastic\Common\FindologicBundle\Domain\SearchRequest;
use Frontastic\Common\FindologicBundle\Exception\ServiceNotAliveException;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\RejectedPromise;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class FindologicProductSearchApiTest extends TestCase
{
    public function testUsesFallbackOnUnavailableService()
    {
        $serviceDeadResult = new RejectedPromise(new ServiceNotAliveException('dead'));
        $locale = 'en_GB@GBP';
        $query = new ProductQuery(['locale' => $locale]);
        $searchRequest = new SearchRequest();
        $clientConfig = new FindologicEndpointConfig(['hostUrl' => 'foo', 'shopkey' => 'bar']);

        $client = \Phake::partialMock(
            FindologicClient::class,
            \Phake::mock(HttpClient::class),
            \Phake::mock(RequestProvider::class),
            [$locale => $clientConfig]
        );

        $originalDataSource = \Phake::mock(ProductSearchApi::class);
        $mapper = \Phake::mock(Mapper::class);
        $validator = \Phake::mock(QueryValidator::class);

        \Phake::when($client)->isAlive($locale)->thenReturn($serviceDeadResult);
        \Phake::when($validator)->isSupported($query)->thenReturn(ValidationResult::createValid());
        \Phake::when($mapper)->queryToRequest($query)->thenReturn($searchRequest);
        \Phake::when($originalDataSource)->query($query)->thenReturn(new FulfilledPromise('result'));

        $api = new FindologicProductSearchApi(
            $client,
            $originalDataSource,
            $mapper,
            $validator,
            \Phake::mock(LoggerInterface::class),
            [$locale]
        );

        $api->query($query)->wait();

        \Phake::verify($client, \Phake::times(1))->search($locale, $searchRequest);
        \Phake::verify($client, \Phake::times(1))->isAlive($locale);
        \Phake::verifyNoOtherInteractions($client);

        \Phake::verify($originalDataSource, \Phake::times(1))->query($query);
        \Phake::verifyNoOtherInteractions($originalDataSource);
    }
}
