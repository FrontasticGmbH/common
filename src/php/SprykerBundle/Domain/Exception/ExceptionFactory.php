<?php declare(strict_types = 1);

namespace Frontastic\Common\SprykerBundle\Domain\Exception;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class ExceptionFactory implements ExceptionFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createFromGuzzleClientException(
        ClientException $clientException,
        ?string $endpoint = null
    ): SprykerClientException {
        return SprykerClientException::createFromGuzzleClientException($clientException, $endpoint);
    }

    /**
     * @inheritDoc
     */
    public function createFromGuzzleServerException(
        ServerException $serverException,
        ?string $endpoint = null
    ): SprykerServerException {
        return SprykerServerException::createFromGuzzleClientException($serverException, $endpoint);
    }
}
