<?php

namespace Frontastic\Common;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Proxy\Proxy;
use Frontastic\Common\JsonSerializer\ObjectEnhancer;

class JsonSerializer
{
    /**
     * @var string[]
     */
    private $propertyBlacklist;

    /**
     * @var ObjectEnhancer[]
     */
    private $objectEnhancers = [];

    /**
     * @param string[] $propertyBlacklist
     * @param ObjectEnhancer[] $objectEnhancers
     */
    public function __construct(array $propertyBlacklist = [], iterable $objectEnhancers = [])
    {
        $this->propertyBlacklist = $propertyBlacklist;

        foreach ($objectEnhancers as $enhancer) {
            $this->addEnhancer($enhancer);
        }
    }

    public function addEnhancer(ObjectEnhancer $enhancer): void
    {
        $this->objectEnhancers[] = $enhancer;
    }

    /**
     * Is there a sensible refactoring to reduce this methods compleixty?
     * Otherwise we consider it fine, since its tested anyways:
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function serialize($item, $visitedIds = array())
    {
        if (is_null($item) || is_scalar($item)) {
            return $item;
        }

        if (is_resource($item)) {
            throw new \UnexpectedValueException("Resources may not be serialized.");
        }

        if (is_array($item) || ($item instanceof Collection)) {
            $result = [];
            foreach ($item as $key => $value) {
                $result[$key] = $this->serialize($value, $visitedIds);
            }
            return $result;
        }

        if ($item instanceof \DateTimeInterface) {
            return $item->format(\DateTime::ATOM);
        }

        if ($item instanceof Proxy) {
            return $this->convertProxy($item);
        }

        $result = ['_type' => get_class($item)];
        foreach (get_object_vars($item) as $key => $value) {
            if (in_array($key, $this->propertyBlacklist)) {
                $result[$key] = '_FILTERED_';
            } else {
                $result[$key] = $this->serialize($value, $visitedIds);
            }
        }

        $result = array_merge(
            $result,
            $this->enhanceSerialization($item)
        );

        return $result;
    }

    private function enhanceSerialization(object $object): array
    {
        $properties = [];
        foreach ($this->objectEnhancers as $enhancer) {
            $properties = array_merge($properties, $enhancer->enhance($object));
        }
        return $properties;
    }

    protected function convertProxy(Proxy $item)
    {
        $proxy = [
            '_proxy' => true,
            '_type' => get_parent_class($item),
        ];

        if (property_exists($item, 'id')) {
            $proxy['id'] = $item->id;
            return $proxy;
        }

        if (property_exists($item, 'email')) {
            $proxy['email'] = $item->email;
            return $proxy;
        }

        return null;
    }
}
