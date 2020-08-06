<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

class PaginatedQuery extends Query
{
    /**
     * The default limit is <b>24</b>, because it is divisble * by 2, 3, 4 & 6 – which are common numbers or products
     * per row in * frontends.
     */
    const DEFAULT_LIMIT = 24;

    /**
     * Optional limit, the default value is <b>24</b>, because it is divisble
     * by 2, 3, 4 & 6 – which are common numbers or products per row in
     * frontends.
     *
     * @var integer
     */
    public $limit = self::DEFAULT_LIMIT;

    /**
     * Optional start offset, default is <b>0</b>.
     *
     * @var integer
     * @deprecated Use $cursor instead
     */
    public $offset = 0;

    /**
     * Optional item reference.
     *
     * @var string
     */
    public $cursor;

    /**
     * Optional flag that indicates if there are more pages to fetch.
     *
     * @var boolean
     */
    public $hasNextPage = false;

    /**
     * Option flag that indicates if there are any pages prior to the current page.
     * @var boolean
     */
    public $hasPreviousPage = false;
}
