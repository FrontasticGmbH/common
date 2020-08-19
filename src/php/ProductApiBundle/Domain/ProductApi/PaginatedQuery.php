<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

/**
 * Class PaginatedQuery can be used for both, offset and cursor-based pagination.
 *
 * In general terms, REST APIs use offset pagination whereas GraphQL APIs use cursor-based pagination.
 *
 * Regardless the pagination implemented by your backend of choice, we highly recommend you to use in both cases
 * the property $cursor to store the position where the pagination should start.
 *
 * NOTE: the property $offset will be deprecated in a further commit.
 */
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
     */
    public $offset = 0;

    /**
     * Optional item reference where the pagination should start.
     *
     * @var string
     */
    public $cursor;
}
