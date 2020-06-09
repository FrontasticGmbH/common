<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class ShopwareCurrency extends DataObject
{
    /**
     * @var string
     */
    public $id;

    /**
     * Factor against euro
     *
     * @var float
     */
    public $factor;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $shortName;

    /**
     * @var string
     */
    public $symbol;

    /**
     * @var string
     */
    public $isoCode;

    /**
     * @var bool
     */
    public $isSystemDefault;

    /**
     * @var int
     */
    public $decimalPrecision;
}
