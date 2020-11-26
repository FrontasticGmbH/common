<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class Group extends ApiDataObject
{
    const GROUP_NAME_ALL = '__SYSTEM_ALL';

    /**
     * @var string
     * @required
     */
    public $groupId;

    /**
     * @var string
     * @required
     */
    public $name;

    /**
     * @var string[]
     * @required
     */
    public $permissions = [];
}
