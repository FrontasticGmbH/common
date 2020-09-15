<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class ShopwareLanguage extends DataObject
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $localeId;

    /**
     * @var string
     */
    public $localeCode;

    /**
     * @var string
     */
    public $localeName;

    /**
     * @var string
     */
    public $localeTerritory;
}
