<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectApi;

use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;
use Frontastic\Common\ShopwareBundle\Domain\Client;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;

class ShopwareProjectApi implements ProjectApi
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\Client
     */
    private $client;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator
     */
    private $localeCreator;

    /**
     * @var array
     */
    private $languages;

    public function __construct(Client $client, LocaleCreator $localeCreator, array $languages)
    {
        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->languages = $languages;
    }

    /**
     * @return Attribute[] Attributes mapped by ID
     */
    public function getSearchableAttributes(): array
    {
        foreach ($this->languages as $language) {

        }


        // TODO: implement
        return [];
    }
}
