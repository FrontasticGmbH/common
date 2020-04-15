<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareSalutation;

class SalutationsMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'salutations';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $salutationsData = $this->extractData($resource);

        $result = [];
        foreach ($salutationsData as $salutationData) {
            $result[] = $this->mapDataToShopwareSalutation($salutationData);
        }

        return $result;
    }

    private function mapDataToShopwareSalutation(array $salutationData): ShopwareSalutation
    {
        $salutation = new ShopwareSalutation($salutationData, true);
        $salutation->displayName = $salutationData['translated']['displayName'] ?? $salutationData['displayName'];
        $salutation->letterName = $salutationData['translated']['letterName'] ?? $salutationData['letterName'];

        return $salutation;
    }
}
