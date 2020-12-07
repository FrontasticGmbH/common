<?php

namespace Frontastic\Common\SprykerBundle\Domain\Exception;

use GuzzleHttp\Exception\ClientException;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

class SprykerClientException extends \RuntimeException
{
    /**
     * @param \GuzzleHttp\Exception\ClientException $clientException
     * @param string|null $endpoint
     *
     * @return self
     */
    public static function createFromGuzzleClientException(
        ClientException $clientException,
        ?string $endpoint = null
    ): self {
        $message = $clientException->getResponse()
            ? (string) $clientException->getResponse()->getBody()
            : $clientException->getMessage();

        $extendedMessage = Json::encode([
            'message' => $message,
            'endpoint' => $endpoint,
        ]);

        return new self($extendedMessage, $clientException->getCode(), $clientException);
    }
}
