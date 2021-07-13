<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\DataMapper;

abstract class AbstractDataMapper implements DataMapperInterface
{
    private const KEY_DATA = 'data';
    private const KEY_AGGREGATIONS = 'aggregations';
    private const KEY_ELEMENTS = 'elements';

    protected function convertPriceToCent($price): int
    {
        return (int)bcmul((string)$price, '100');
    }

    protected function extractData(array $resource, array $fallback = []): array
    {
        return $resource[self::KEY_DATA] ?? $this->stripHeaders($fallback);
    }

    protected function extractAggregations(array $resource): array
    {
        return $resource[self::KEY_AGGREGATIONS] ?? [];
    }

    protected function extractElements(array $resource, array $fallback = []): array
    {
        return $resource[self::KEY_ELEMENTS] ?? $this->stripHeaders($fallback);
    }

    protected function mapDangerousInnerData(array $innerData): ?array
    {
        if (!($this instanceof QueryAwareDataMapperInterface) || $this->getQuery()->loadDangerousInnerData === false) {
            return null;
        }

        return $innerData;
    }

    /**
     * @param string[] $data
     * @param string $key
     *
     * @return mixed
     */
    protected function resolveTranslatedValue(array $data, string $key)
    {
        return $data['translated'][$key] ?? $data[$key] ?? null;
    }

    protected function stripHeaders(array $data): array
    {
        unset($data['headers']);
        return $data;
    }
}
