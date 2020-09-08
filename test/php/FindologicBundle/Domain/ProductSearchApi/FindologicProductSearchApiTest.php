<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\FindologicBundle\Domain\FindologicClient;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\FindologicProductSearchApi;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\Mapper;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\QueryValidator;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\ValidationResult;
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
        $serviceDeadResult = (new RejectedPromise(new ServiceNotAliveException('asdf')));
        $query = new ProductQuery();
        $searchRequest = new SearchRequest();

        $client = \Phake::partialMock(FindologicClient::class, \Phake::mock(HttpClient::class), 'foo', 'bar');
        $originalDataSource = \Phake::mock(ProductSearchApi::class);
        $mapper = \Phake::mock(Mapper::class);
        $validator = \Phake::mock(QueryValidator::class);

        \Phake::when($client)->isAlive()->thenReturn($serviceDeadResult);
        \Phake::when($validator)->isSupported($query)->thenReturn(ValidationResult::createValid());
        \Phake::when($mapper)->queryToRequest($query)->thenReturn($searchRequest);
        \Phake::when($originalDataSource)->query($query)->thenReturn(new FulfilledPromise('result'));

        $api = new FindologicProductSearchApi(
            $client,
            $originalDataSource,
            $mapper,
            $validator,
            $this->createMock(LoggerInterface::class)
        );

        $api->query($query)->wait();

        \Phake::verify($client, \Phake::times(1))->search($searchRequest);
        \Phake::verify($client, \Phake::times(1))->isAlive();
        \Phake::verifyNoOtherInteractions($client);

        \Phake::verify($originalDataSource, \Phake::times(1))->query($query);
        \Phake::verifyNoOtherInteractions($originalDataSource);
    }
}
