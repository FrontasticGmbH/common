<?php

namespace Frontastic\Common\SprykerBundle\Domain;

class MapperResolver
{
    /**
     * @var MapperInterface[]
     */
    private $mappers;

    /**
     * @param MapperInterface[] $mappers
     */
    public function __construct(iterable $mappers)
    {
        foreach ($mappers as $mapper) {
            $this->addMapper($mapper);
        }
    }

    /**
     * @param string $name
     * @return MapperInterface
     */
    public function getMapper(string $name): MapperInterface
    {
        if (!isset($this->mappers[$name])) {
            throw new \RuntimeException(sprintf('Mapper not found by name: %s', $name));
        }

        return $this->mappers[$name];
    }

    /**
     * @param string $name
     * @return ExtendedMapperInterface
     */
    public function getExtendedMapper(string $name): ExtendedMapperInterface
    {
        $mapper = $this->getMapper($name);

        if (!($mapper instanceof ExtendedMapperInterface)) {
            throw new \InvalidArgumentException(sprintf('Mapper: %s is not an ExtendedMapper', $name));
        }

        return $mapper;
    }

    /**
     * @param MapperInterface $mapper
     */
    private function addMapper(MapperInterface $mapper): void
    {
        $this->mappers[$mapper->getName()] = $mapper;
    }
}
