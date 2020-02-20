<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Kore\DataObject\DataObject;

class Category extends DataObject
{
    /**
     * @var string
     */
    public $categoryId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $depth = 0;

    /**
     * The materialized id path for this category.
     *
     * @var string
     */
    public $path;

    /**
     * @var string
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
}
