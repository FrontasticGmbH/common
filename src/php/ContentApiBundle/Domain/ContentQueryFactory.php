<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Symfony\Component\HttpFoundation\Request;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

class ContentQueryFactory
{
    /**
     * @param array $parameters Query parameters (typically from HTTP request)
     * @return Query
     */
    public static function queryFromParameters(array $parameters): Query
    {
        return Query::fromArray($parameters);
    }

    /**
     * Creates a Query from a Request and will ignore additional parameters send to the request
     *
     * @param Request $request
     * @return Query
     */
    public static function queryFromRequest(Request $request): Query
    {
        return Query::fromArray(Json::decode($request->getContent(), true), true);
    }
}
