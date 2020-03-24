<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

class DataMapperResolver
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\DataMapperInterface[]
     */
    private $mappers;

    /**
     * @param \Frontastic\Common\ShopwareBundle\Domain\DataMapperInterface[] $mappers
     */
    public function __construct(iterable $mappers)
    {
        foreach ($mappers as $mapper) {
            $this->addMapper($mapper);
        }
    }

    public function getMapper(string $name): DataMapperInterface
    {
        if (!isset($this->mappers[$name])) {
            throw new \RuntimeException(sprintf('Mapper not found by name: %s', $name));
        }

        return $this->mappers[$name];
    }

    private function addMapper(DataMapperInterface $mapper): void
    {
        $this->mappers[$mapper->getName()] = $mapper;
    }
}
