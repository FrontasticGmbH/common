<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\DataMapper;

trait LanguageAwareDataMapperTrait
{
    /**
     * @var string
     */
    private $language;

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}
