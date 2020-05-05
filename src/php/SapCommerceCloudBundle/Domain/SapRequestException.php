<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\HttpClient\Response;

class SapRequestException extends \RuntimeException
{
    public static function fromResponse(Response $response): SapRequestException
    {
        $status = $response->status ?? 'unknown';

        $errorMessages = [];
        $errorData = json_decode($response->body, true);
        if (is_array($errorData) && is_array($errorData['errors'] ?? null)) {
            foreach ($errorData['errors'] as $errorItem) {
                $type = $errorItem['type'] ?? 'Unknown';
                $message = $errorItem['message'] ?? 'An unknown error occurred';

                $errorMessages[] = sprintf('[%s] %s', $type, $message);
            }
        }

        return new SapRequestException(
            sprintf(
                'An SAP request failed with status code %s: %s',
                $status,
                implode('; ', $errorMessages)
            ),
            $response->status ?? 0
        );
    }
}
