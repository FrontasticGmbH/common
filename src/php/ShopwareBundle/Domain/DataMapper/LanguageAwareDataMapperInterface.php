<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\DataMapper;

interface LanguageAwareDataMapperInterface extends DataMapperInterface
{
    public function setLanguage(string $language);

    public function getLanguage(): string;
}
