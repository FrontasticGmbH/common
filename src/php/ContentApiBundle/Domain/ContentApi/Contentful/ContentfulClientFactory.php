<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful;

use Contentful\Delivery\ClientOptions;
use GuzzleHttp\Client as HttpClient;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Contentful\Delivery\Client as ContentfulClient;
use Psr\Log\LoggerInterface;

class ContentfulClientFactory
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function factorForConfigs(
        object $typeSpecificConfiguration,
        ?object $contentfulConfig = null
    ): ContentfulClient {
        $config = [];
        foreach (['accessToken', 'spaceId'] as $option) {
            $value = $typeSpecificConfiguration->$option ?? $contentfulConfig->$option ?? null;

            if ($value === null) {
                throw new \RuntimeException('Contentful config option ' . $option . ' is not set');
            }
            if (!is_string($value)) {
                throw new \RuntimeException('Contentful config option ' . $option . ' is no string');
            }
            if ($value === '') {
                throw new \RuntimeException('Contentful config option ' . $option . ' is empty');
            }

            $config[$option] = $value;
        }

        $options = new ClientOptions();
        $options->withLogger($this->logger);
        $options->withHttpClient(new HttpClient([
            'timeout'  => max(2, (int)getenv('http_client_timeout')),
        ]));

        return new ContentfulClient(
            $config['accessToken'],
            $config['spaceId'],
            'master',
            $options
        );
    }

    public function factorForProjectAndType(Project $project, string $typeName): ContentfulClient
    {
        $typeSpecificConfiguration = $project->getConfigurationSection($typeName);
        $contentfulConfig = $project->getConfigurationSection('contentful');

        return $this->factorForConfigs($typeSpecificConfiguration, $contentfulConfig);
    }
}
