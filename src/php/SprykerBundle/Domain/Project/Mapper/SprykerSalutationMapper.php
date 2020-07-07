<?php

namespace Frontastic\Common\SprykerBundle\Domain\Project\Mapper;

use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use Frontastic\Common\SprykerBundle\Domain\SprykerSalutation;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class SprykerSalutationMapper implements MapperInterface
{
    public const MAPPER_NAME = 'salutations';
    private const DEFAULT_SALUTATION = 'Mrs';

    /**
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $resource
     *
     * @return \Frontastic\Common\SprykerBundle\Domain\SprykerSalutation[]
     */
    public function mapResource(ResourceObject $resource): array
    {
        $salutations = array_map([$this, 'mapSalutation'], $resource->attribute('salutations', []));
        usort($salutations, [$this, 'compareSalutationByDefaultDesc']);

        return $salutations;
    }

    /**
     * @param array $data
     *
     * @return \Frontastic\Common\SprykerBundle\Domain\SprykerSalutation
     */
    private function mapSalutation(array $data): SprykerSalutation
    {
        $salutation = new SprykerSalutation();
        $salutation->value = $data['name'];
        $salutation->label = $data['translation'] ?? $data['name'];
        $salutation->default = $salutation->value === self::DEFAULT_SALUTATION;

        return $salutation;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\SprykerSalutation $a
     * @param \Frontastic\Common\SprykerBundle\Domain\SprykerSalutation $b
     *
     * @return int
     */
    protected function compareSalutationByDefaultDesc(SprykerSalutation $a, SprykerSalutation $b): int
    {
        return $b->default <=> $a->default;
    }
}
