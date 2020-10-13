<?php declare(strict_types = 1);

namespace Frontastic\Common\SprykerBundle\Domain\Exception;

use GuzzleHttp\Exception\ServerException;

class SprykerServerException extends \RuntimeException
{
    /**
     * @param \GuzzleHttp\Exception\ServerException $clientException
     * @param string|null $endpoint
     *
     * @return static
     */
    public static function createFromGuzzleClientException(
        ServerException $clientException,
        ?string $endpoint = null
    ): self {
        $message = $clientException->getResponse()
            ? (string)$clientException->getResponse()->getBody()
            : $clientException->getMessage();

        $extendedMessage = json_encode(
            [
                'message' => $message,
                'endpoint' => $endpoint,
            ]
        );

        return new self($extendedMessage, $clientException->getCode(), $clientException);
    }
}
