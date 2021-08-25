<?php

namespace Frontastic\Common\AlgoliaBundle\Domain;

use Kore\DataObject\DataObject;

class AlgoliaIndexConfig extends DataObject
{
    /**
     * @var string
     */
    public $appId;

    /**
     * @var string
     */
    public $appKey;

    /**
     * @var string
     */
    public $indexName;
}
