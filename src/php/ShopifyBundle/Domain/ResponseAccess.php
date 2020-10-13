<?php

namespace Frontastic\Common\ShopifyBundle\Domain;

use Kore\DataObject\DataObject;

class ResponseAccess extends DataObject
{
    /**
     * The response data.
     *
     * @var mixed
     */
    public $container;

    /**
     * Check if errors are in response.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return isset($this->container['errors']) || isset($this->container['error']);
    }

    /**
     * Get the errors.
     *
     * @return mixed
     */
    public function getErrors()
    {
        if (!$this->hasErrors()) {
            return;
        }

        return isset($this->container['errors']) ? $this->container['errors'] : $this->container['error'];
    }
}
