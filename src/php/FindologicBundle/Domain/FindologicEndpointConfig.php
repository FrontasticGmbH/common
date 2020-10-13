<?php

namespace Frontastic\Common\FindologicBundle\Domain;

use Kore\DataObject\DataObject;

class FindologicEndpointConfig extends DataObject
{
    /**
     * @var string
     */
    public $hostUrl;

    /**
     * @var string
     */
    public $shopkey;
}
