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
    private $propertyExcludeList;

    /**
     * @var ObjectEnhancer[]
     */
    private $objectEnhancers = [];

    /**
     * @param string[] $propertyExcludeList
     * @param ObjectEnhancer[] $objectEnhancers
     */
    public function __construct(array $propertyExcludeList = [], iterable $objectEnhancers = [])
    {
        $this->propertyExcludeList = $propertyExcludeList;

        foreach ($objectEnhancers as $enhancer) {
            $this->addEnhancer($enhancer);
        }
    }

    public function addEnhancer(ObjectEnhancer $enhancer): void
    {
        $this->objectEnhancers[] = $enhancer;
    }

    /**
     * Prepares an object for json serialization. Does *not* actually encode it as JSON.
     *
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
                if (strpos($key, 'dangerousInner') === 0) {
                    continue;
                }

                if ($key === 'trace') {
                    $value = $this->stripDownTrace($value);
                }

                if (in_array($key, $this->propertyExcludeList, true)) {
                    $result[$key] = '_FILTERED_';
                } else {
                    $result[$key] = $this->serialize($value, $visitedIds);
                }
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
            if (strpos($key, 'dangerousInner') === 0) {
                continue;
            }

            if ($key === 'trace') {
                $value = $this->stripDownTrace($value);
            }

            if (in_array($key, $this->propertyExcludeList)) {
                $result[$key] = '_FILTERED_';
            } else {
                $result[$key] = $this->serialize($value, $visitedIds);
            }
        }

        $result = array_replace(
            $result,
            $this->enhanceSerialization($item)
        );

        if ($result['_type'] === 'stdClass') {
            unset($result['_type']);
        }

        return $result;
    }

    private function stripDownTrace(array $trace): array
    {
        foreach ($trace as $levelIndex => $traceLevel) {
            if (!isset($traceLevel['args']) || !is_array($traceLevel['args'])) {
                continue;
            }

            foreach ($traceLevel['args'] as $argumentIndex => $argument) {
                if (is_object($argument)) {
                    $trace[$levelIndex]['args'][$argumentIndex] = get_class($argument);
                }
                if (is_array($argument)) {
                    $trace[$levelIndex]['args'][$argumentIndex] = 'array';
                }
            }
        }
        return $trace;
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
