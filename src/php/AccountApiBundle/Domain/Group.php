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
     */
    public $groupId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string[]
     */
    public $permissions = [];
}
