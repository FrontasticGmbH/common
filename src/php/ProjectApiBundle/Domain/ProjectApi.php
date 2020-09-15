<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

/**
 * @deprecated Use the `ProductSearchApi` instead.
 */
interface ProjectApi
{
    /**
     * @return Attribute[] Attributes mapped by ID
     * @deprecated Use the `ProductSearchApi` instead.
     */
    public function getSearchableAttributes(): array;
}
