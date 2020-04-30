<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\Locale\ShopwareLocale;

trait LocaleAwareDataMapperTrait
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\Locale\ShopwareLocale
     */
    private $locale;

    public function setLocale(ShopwareLocale $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): ShopwareLocale
    {
        return $this->locale;
    }
}
