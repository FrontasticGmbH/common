<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Content;

interface ContentApi
{
    /**
     * @return ContentType[]
     */
    public function getContentTypes(): array;

    public function getContent(string $contentId): Content;

    public function query(Query $query): Result;

    /**
     * Get *dangerous* inner client
     *
     * This method exists to enable you to use features which are not yet part
     * of the abstraction layer.
     *
     * Be aware that any usage of this method might seriously hurt backwards
     * compatibility and the future abstractions might differ a lot from the
     * vendor provided abstraction.
     *
     * Use this with care for features necessary in your customer and talk with
     * Frontastic about provising an abstraction.
     *
     * @return mixed
     */
    public function getDangerousInnerClient();
}
