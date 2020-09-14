<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class Group extends DataObject
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
