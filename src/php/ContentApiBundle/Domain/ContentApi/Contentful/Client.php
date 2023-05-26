<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful;

use Contentful\Delivery\Client as ContentfulClient;
use Contentful\Delivery\ClientOptions;

class Client extends ContentfulClient
{
    /**
     * LoggerInterface
     */
    private $frontasticLogger;

    public function __construct(
        string $token,
        string $spaceId,
        string $environmentId = 'master',
        ClientOptions $options = null
    ) {
        parent::__construct($token, $spaceId, $environmentId, $options);

        $this->frontasticLogger = $options->getLogger();
    }

    protected function callApi(string $method, string $uri, array $options = []): array
    {
        $start = microtime(true);

        $apiResponse = parent::callApi($method, $uri, $options);

        // We are calculating here the time that takes the whole parent::callApi method.
        // To get only the request time, we could use: end($messages)->getDuration();
        $time = microtime(true) - $start;

        $messages = $this->getMessages();
        $host = $this->getHost();
        $this->frontasticLogger->info(
            sprintf(
                'Request against %s took %dms',
                $host,
                $time * 1000
            ),
            [
                'outgoingRequest' => [
                    'host' => $host,
                    'path' => parse_url($uri, PHP_URL_PATH),
                    'method' => $method,
                    'responseTime' => $time,
                    'responseTimeMs' => $time * 1000,
                    'statusCode' => end($messages)->getResponse()->getStatusCode(),
                ],
            ]
        );

        return $apiResponse;
    }
}
