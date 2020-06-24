<?php declare(strict_types=1);

namespace Frontastic\Common\SprykerBundle\Domain\Exception;

use GuzzleHttp\Exception\ClientException;

class SprykerClientException extends \RuntimeException
{
    /**
     * @param \GuzzleHttp\Exception\ClientException $clientException
     * @param string|null $endpoint
     *
     * @return self
     */
    public static function createFromGuzzleClientException(ClientException $clientException, ?string $endpoint =null): self
    {
        $message = $clientException->getResponse()
            ? (string) $clientException->getResponse()->getBody()
            : $clientException->getMessage();

        $extendedMessage = json_encode([
            'message' => $message,
            'endpoint' => $endpoint,
        ]);

        return new self($extendedMessage, $clientException->getCode(), $clientException);
    }
}
