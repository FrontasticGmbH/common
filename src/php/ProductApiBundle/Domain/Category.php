<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class Category extends ApiDataObject
{
    /**
     * @var string
     * @required
     */
    public $categoryId;

    /**
     * @var string
     * @required
     */
    public $name;

    /**
     * @var int
     * @required
     */
    public $depth = 0;

    /**
     * The materialized id path for this category.
     *
     * @var string
     * @required
     */
    public $path;

    /**
     * @var string
     * @required
     */
    public $slug;

    /**
     * Access original object from backend
     *
     * This should only be used if you need very specific features
     * right NOW. Please notify Frontastic about your need so that
     * we can integrate those twith the common API. Any usage off
     * this property might make your code unstable against future
     * changes.
     *
     * @var mixed
     */
    public $dangerousInnerCategory;

    /**
     * @return string[]
     */
    public function getPathAsArray(): array
    {
        return array_values(array_filter(explode('/', $this->path)));
    }

    /**
     * @return string[]
     */
    public function getAncestorIds(): array
    {
        $pathArray = $this->getPathAsArray();
        array_pop($pathArray);
        return $pathArray;
    }

    public function getParentCategoryId(): ?string
    {
        $ancestorIds = $this->getAncestorIds();
        return array_pop($ancestorIds);
    }
}
