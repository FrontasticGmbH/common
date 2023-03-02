<?php

declare(strict_types=1);

namespace Frontastic\Common;

use Symfony\Component\PropertyAccess\PropertyAccess;

class AppDataFilter
{
    /**
     * @var string[]
     */
    private array $keysToKeepList;

    /**
     * @var string[]
     */
    private array $keysToAlwaysRemoveList;

    /**
     * @var string[]
     */
    private array $propertiesToAlwaysRemoveList;

    /**
     * @param string[] $keysToKeepList
     * @param string[] $keysToAlwaysRemoveList
     * @param string[] $propertiesToAlwaysRemoveList
     */
    public function __construct(
        array $keysToKeepList = [],
        array $keysToAlwaysRemoveList = [],
        array $propertiesToAlwaysRemoveList = []
    ) {
        $this->keysToKeepList = $keysToKeepList;
        $this->keysToAlwaysRemoveList = $keysToAlwaysRemoveList;
        $this->propertiesToAlwaysRemoveList = $propertiesToAlwaysRemoveList;
    }

    public function filterAppData(array $appData): array
    {
        $appData = $this->filterByPropertyPath($appData);

        return $this->removeNullValuesAndEmptyArrays($appData);
    }

    private function removeNullValuesAndEmptyArrays(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $this->keysToKeepList, true)) {
                $result[$key] = $value;
                continue;
            }

            if ($value === null || in_array($key, $this->keysToAlwaysRemoveList, true)) {
                continue;
            }

            if (is_array($value)) {
                $value = $this->removeNullValuesAndEmptyArrays($value);

                if (count($value) === 0) {
                    continue;
                }
            }

            $result[$key] = $value;
        }

        return count($result) > 0 ? $result : [];
    }

    private function filterByPropertyPath(array $appData): array
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->propertiesToAlwaysRemoveList as $propertyPath) {
            if ($propertyAccessor->isWritable($appData, $propertyPath)) {
                $propertyAccessor->setValue($appData, $propertyPath, null);
            }
        }

        return $appData;
    }
}
