<?php declare(strict_types = 1);

namespace Frontastic\Common\SprykerBundle\Domain\Exception;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

interface ExceptionFactoryInterface
{
    /**
     * @param \GuzzleHttp\Exception\ClientException $clientException
     * @param string|null $endpoint
     *
     * @return \Frontastic\Common\SprykerBundle\Domain\Exception\SprykerClientException
     */
    public function createFromGuzzleClientException(
        ClientException $clientException,
        ?string $endpoint = null
    ): SprykerClientException;

    /**
     * @param \GuzzleHttp\Exception\ServerException $serverException
     * @param string|null $endpoint
     *
     * @return \Frontastic\Common\SprykerBundle\Domain\Exception\SprykerServerException
     */
    public function createFromGuzzleServerException(
        ServerException $serverException,
        ?string $endpoint = null
    ): SprykerServerException;
}
