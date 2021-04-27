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

        $time = microtime(true) - $start;

        $host = parse_url($uri, PHP_URL_HOST);
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
                ],
            ]
        );

        return $apiResponse;
    }
}
