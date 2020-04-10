<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Exception\MapperNotFoundException;
use GuzzleHttp\Promise\PromiseInterface;
use function GuzzleHttp\Promise\settle;

class ShopwareProjectConfigApi implements ShopwareProjectConfigApiInterface
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ClientInterface
     */
    private $client;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver
     */
    private $mapperResolver;

    public function __construct(ClientInterface $client, DataMapperResolver $mapperResolver)
    {
        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
    }

    public function getProjectConfig(): array
    {
        $contextPromises = [
            self::RESOURCE_COUNTRIES => $this->client->get('/country'),
            self::RESOURCE_CURRENCIES => $this->client->get('/currency'),
            self::RESOURCE_LANGUAGES => $this->client->get('/language?associations[locale][]'),
            self::RESOURCE_PAYMENT_METHODS => $this->client->get('/payment-method?associations[media][]'),
            self::RESOURCE_SALUTATIONS => $this->client->get('/salutation'),
            self::RESOURCE_SHIPPING_METHODS => $this->client->get('/shipping-method?associations[media][]'),
        ];

        $result = [];
        foreach (settle($contextPromises)->wait() as $resource => $response) {
            if ($response['state'] === PromiseInterface::FULFILLED) {
                $result[$resource] = $this->mapResource($response['value']['data'], $resource);
            } else {
                throw $response['reason'];
            }
        }

        return $result;
    }

    private function mapResource($data, $mapperName)
    {
        try {
            $mapper = $this->mapperResolver->getMapper($mapperName);

            return $mapper->map($data);
        } catch (MapperNotFoundException $exception) {
            // If no mapper is defined just return raw data
            return $data;
        }
    }
}
