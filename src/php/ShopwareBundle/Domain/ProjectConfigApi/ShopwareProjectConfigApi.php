<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Exception\MapperNotFoundException;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper\CountryMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper\PaymentMethodsMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper\SalutationsMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper\ShippingMethodsMapper;
use GuzzleHttp\Promise\EachPromise;

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

    /**
     * @inheritDoc
     */
    public function getCountryByCriteria(string $criteria): ShopwareCountry
    {
        $parameters = [];
        if (preg_match('/^([A-Z]{2})$/', $criteria, $matches)) {
            $parameters['filter[iso]'] = $matches[0];
        } elseif (preg_match('/^([A-Z]{3})$/', $criteria, $matches)) {
            $parameters['filter[iso3]'] = $matches[0];
        } else {
            $parameters['filter[name]'] = $criteria;
        }

        return $this->client
            ->get('/country', $parameters)
            ->then(function ($response) {
                return $this->mapResource($response, CountryMapper::MAPPER_NAME);
            })->wait();
    }

    /**
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwarePaymentMethod[]
     */
    public function getPaymentMethods(): array
    {
        return $this->client
            ->get('/payment-method?associations[media][]')
            ->then(function ($response) {
                return $this->mapResource($response, PaymentMethodsMapper::MAPPER_NAME);
            })->wait();
    }

    public function getProjectConfig(): array
    {
        $contextResources = [
            self::RESOURCE_COUNTRIES => '/country',
            self::RESOURCE_CURRENCIES => '/currency',
            self::RESOURCE_LANGUAGES => '/language?associations[locale][]',
        ];

        $contextPromises = (function () use ($contextResources) {
            foreach ($contextResources as $key => $resource) {
                // don't forget using generator
                yield $key => $this->client->get($resource);
            }
        })();

        $result = [];
        (new EachPromise($contextPromises, [
            'concurrency' => 3,
            'fulfilled' => function ($resource, $resourceName) use (&$result) {
                $result[$resourceName] = $this->mapResource($resource, $resourceName);
            },
            'rejected' => static function ($reason) {
                throw $reason;
            }
        ]))->promise()->wait();

        return $result;
    }

    public function getSalutation(string $salutationKey): ?ShopwareSalutation
    {
        return $this->getSalutations($salutationKey)[0] ?? null;
    }

    /**
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareSalutation[]
     */
    public function getSalutations(?string $salutationKey = null): array
    {
        $parameters = [];

        if ($salutationKey !== null) {
            $parameters['filter[salutationKey]'] = $salutationKey;
        }

        return $this->client
            ->get('/salutation', $parameters)
            ->then(function ($response) {
                return $this->mapResource($response, SalutationsMapper::MAPPER_NAME);
            })->wait();
    }

    /**
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareShippingMethod[]
     */
    public function getShippingMethods(): array
    {
        return $this->client
            ->get('/shipping-method?associations[media][]')
            ->then(function ($response) {
                return $this->mapResource($response, ShippingMethodsMapper::MAPPER_NAME);
            })->wait();
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
