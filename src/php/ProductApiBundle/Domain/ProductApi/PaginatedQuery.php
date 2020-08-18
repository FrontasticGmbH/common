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
     */
    public $offset = 0;

    /**
     * Optional item reference.
     *
     * @var string
     */
    public $cursor;
}
