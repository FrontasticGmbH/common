<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

interface ProjectApi
{
    /**
     * @return Attribute[] Attributes mapped by ID
     */
    public function getSearchableAttributes(): array;
}
