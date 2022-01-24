<?php

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery;
use Frontastic\Common\ShopwareBundle\Domain\AbstractShopwareApi;
use Frontastic\Common\ShopwareBundle\Domain\Exception\MapperNotFoundException;
use Frontastic\Common\ShopwareBundle\Domain\Exception\ResourceNotFoundException;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper\CountryMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper\CurrenciesMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper\PaymentMethodsMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper\SalutationsMapper;
use GuzzleHttp\Promise\EachPromise;

class ShopwareProjectConfigApi extends AbstractShopwareApi implements ShopwareProjectConfigApiInterface
{
    /**
     * @inheritDoc
     */
    public function getCountryByCriteria(string $criteria): ?ShopwareCountry
    {
        $requestData = [
            'page' => 1,
            'limit' => PaginatedQuery::DEFAULT_LIMIT,
        ];

        if (preg_match('/^([A-Z]{2})$/', $criteria, $matches)) {
            $requestData['filter'][] = new Filter\Equals([
                'field' => 'iso',
                'value' => $matches[0],
            ]);
        } elseif (preg_match('/^([A-Z]{3})$/', $criteria, $matches)) {
            $requestData['filter'][] = new Filter\Equals([
                'field' => 'iso3',
                'value' => $matches[0],
            ]);
        } elseif (preg_match('/^([a-z0-9]{32})$/', $criteria, $matches)) {
            $requestData['filter'][] = new Filter\Equals([
                'field' => 'id',
                'value' => $matches[0],
            ]);
        } else {
            $requestData['filter'][] = new Filter\Equals([
                'field' => 'name',
                'value' => $criteria,
            ]);
        }

        try {
            return $this->client
                ->post('/store-api/country', [], $requestData)
                ->then(function ($response) {
                    return $this->mapResponse($response, CountryMapper::MAPPER_NAME);
                })->wait();
        } catch (ResourceNotFoundException $exception) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getCurrency(string $currencyId): ?ShopwareCurrency
    {
        $requestData = [
            'page' => 1,
            'limit' => 1,
            'filter' => [
                new Filter\Equals([
                    'field' => 'id',
                    'value' => $currencyId,
                ])
            ]
        ];

        return $this->client
            ->post('/store-api/currency', [], $requestData)
            ->then(function ($response) {
                return $this->mapResponse($response, CurrenciesMapper::MAPPER_NAME)[0] ?? null;
            })->wait();
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethods(): array
    {
        return $this->client
            ->get('/store-api/payment-method?associations[media][]')
            ->then(function ($response) {
                return $this->mapResponse($response, PaymentMethodsMapper::MAPPER_NAME);
            })->wait();
    }

    /**
     * @inheritDoc
     */
    public function getProjectConfig(): array
    {
        $contextResources = [
            self::RESOURCE_COUNTRIES => '/store-api/country',
            self::RESOURCE_CURRENCIES => '/store-api/currency',
            self::RESOURCE_LANGUAGES => '/store-api/language?associations[locale][]',
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
                $result[$resourceName] = $this->mapResponse($resource, $resourceName);
            },
            'rejected' => static function ($reason) {
                throw $reason;
            }
        ]))->promise()->wait();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getSalutation(string $criteria): ?ShopwareSalutation
    {
        try {
            return $this->getSalutations($criteria)[0] ?? null;
        } catch (ResourceNotFoundException $exception) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getSalutations(?string $criteria = null, ?string $locale = null): array
    {
        $requestData = [];

        if ($criteria !== null) {
            if (preg_match('/^([a-z0-9]{32})$/', $criteria, $matches)) {
                $requestData['filter'][] = new Filter\Equals([
                    'field' => 'id',
                    'value' => $matches[0],
                ]);
            } else {
                $requestData['filter'][] = new Filter\Equals([
                    'field' => 'salutationKey',
                    'value' => $criteria,
                ]);
            }
        }

        $locale = $this->parseLocaleString($locale);

        if ($locale === null) {
            return [];
        }

        return $this->client
            ->forLanguage($locale->languageId)
            ->post('/store-api/salutation', [], $requestData)
            ->then(function ($response) {
                return $this->mapResponse($response, SalutationsMapper::MAPPER_NAME);
            })->wait();
    }

    protected function mapResponse($response, string $mapperName)
    {
        try {
            return parent::mapResponse($response, $mapperName);
        } catch (MapperNotFoundException $exception) {
            // If no mapper is defined just return raw data
            return $response;
        }
    }
}
