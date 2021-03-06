<?php

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Frontastic\Common\ShopwareBundle\Domain\AbstractShopwareApi;
use Frontastic\Common\ShopwareBundle\Domain\Exception\MapperNotFoundException;
use Frontastic\Common\ShopwareBundle\Domain\Exception\ResourceNotFoundException;
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
        $parameters = [];
        if (preg_match('/^([A-Z]{2})$/', $criteria, $matches)) {
            $parameters['filter[iso]'] = $matches[0];
        } elseif (preg_match('/^([A-Z]{3})$/', $criteria, $matches)) {
            $parameters['filter[iso3]'] = $matches[0];
        } elseif (preg_match('/^([a-z0-9]{32})$/', $criteria, $matches)) {
            $parameters['filter[id]'] = $matches[0];
        } else {
            $parameters['filter[name]'] = $criteria;
        }

        try {
            return $this->client
                ->get('/sales-channel-api/v2/country', $parameters)
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
        $parameters = [
            'filter[id]' => $currencyId,
        ];

        return $this->client
            ->get('/sales-channel-api/v2/currency', $parameters)
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
            ->get('/sales-channel-api/v2/payment-method?associations[media][]')
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
            self::RESOURCE_COUNTRIES => '/sales-channel-api/v2/country',
            self::RESOURCE_CURRENCIES => '/sales-channel-api/v2/currency',
            self::RESOURCE_LANGUAGES => '/sales-channel-api/v2/language?associations[locale][]',
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
        $parameters = [];

        if ($criteria !== null) {
            if (preg_match('/^([a-z0-9]{32})$/', $criteria, $matches)) {
                $parameters['filter[id]'] = $matches[0];
            } else {
                $parameters['filter[salutationKey]'] = $criteria;
            }
        }

        $locale = $this->parseLocaleString($locale);

        return $this->client
            ->forLanguage($locale->languageId)
            ->get('/sales-channel-api/v2/salutation', $parameters)
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
