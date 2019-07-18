<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Content;

interface ContentApi
{
    /**
     * @return ContentType[]
     */
    public function getContentTypes(): array;

    /**
     * Fetch content with $contentId in $locale. If $locale is null, project default locale is used.
     *
     * @param string $contentId
     * @param string|null $locale
     * @return Content
     */
    public function getContent(string $contentId, string $locale = null): Content;

    /**
     * Fetch content with by a $query in $locale. Interpretation of the query
     * attributes depend on the content API implementation. If $locale is null,
     * project default locale is used.
     *
     * @param Query $query
     * @param string|null $locale
     * @return Result
     */
    public function query(Query $query, string $locale = null): Result;

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
