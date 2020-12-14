<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\HttpClient\Response;
use Throwable;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

class SapRequestException extends \RuntimeException
{
    /** @var string[] */
    private $errorTypes;

    /**
     * @param string[] $errorTypes
     */
    public function __construct(string $message = '', array $errorTypes = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorTypes = $errorTypes;
    }

    public function hasErrorType(string $type): bool
    {
        return in_array($type, $this->errorTypes, true);
    }

    public static function fromResponse(Response $response): SapRequestException
    {
        $status = $response->status ?? 'unknown';

        $errorMessages = [];
        $errorTypes = [];
        $errorData = Json::decode($response->body, true);
        if (is_array($errorData) && is_array($errorData['errors'] ?? null)) {
            foreach ($errorData['errors'] as $errorItem) {
                $type = $errorItem['type'] ?? 'Unknown';
                $message = $errorItem['message'] ?? 'An unknown error occurred';

                $errorTypes[] = $type;
                $errorMessages[] = sprintf('[%s] %s', $type, $message);
            }
        }

        return new SapRequestException(
            sprintf(
                'An SAP request failed with status code %s: %s',
                $status,
                implode('; ', $errorMessages)
            ),
            $errorTypes,
            $response->status ?? 0
        );
    }
}
