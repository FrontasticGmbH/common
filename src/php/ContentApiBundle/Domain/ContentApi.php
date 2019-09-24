<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Content;
use GuzzleHttp\Promise\PromiseInterface;

interface ContentApi
{
    /**
     * @TODO Deprecate the sync version because of the added complexity. It makes the interface odd to use and the
     *     async version can be made synchronous by calling `.wait()`.
     */
    const QUERY_SYNC = 'sync';
    const QUERY_ASYNC = 'async';

    /**
     * @return ContentType[]
     */
    public function getContentTypes(): array;

    /**
     * Fetch content with $contentId in $locale. If $locale is null, project default locale is used.
     *
     * @param string $contentId
     * @param string|null $locale
     * @param string $mode One of the QUERY_* connstants. Execute the query synchronously or asynchronously?
     * @return Content|PromiseInterface|null A product or null when the mode is sync and a promise if the mode is async.
     */
    public function getContent(string $contentId, string $locale = null, string $mode = self::QUERY_SYNC): ?object;

    /**
     * Fetch content with by a $query in $locale. Interpretation of the query
     * attributes depend on the content API implementation. If $locale is null,
     * project default locale is used.
     *
     * @param Query $query
     * @param string|null $locale
     * @param string $mode One of the QUERY_* connstants. Execute the query synchronously or asynchronously?
     * @return Result|PromiseInterface|null A product or null when the mode is sync and a promise if the mode is async.
     */
    public function query(Query $query, string $locale = null, string $mode = self::QUERY_SYNC): ?object;

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
