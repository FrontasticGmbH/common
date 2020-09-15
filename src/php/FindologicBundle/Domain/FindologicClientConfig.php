<?php

namespace Frontastic\Common\FindologicBundle\Domain;

use Kore\DataObject\DataObject;

class FindologicClientConfig extends DataObject
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
