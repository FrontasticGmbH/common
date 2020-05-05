<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Kore\DataObject\DataObject;

class ShopwareSalutation extends DataObject
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $salutationKey;

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var string
     */
    public $letterName;
}
