<?php

namespace Frontastic\Common\CoreBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use PHPUnit\Framework\TestCase;

class PaginationAdapterTest extends TestCase
{
    public function testResultOffsetToCursorFirstPage()
    {
        $resultFixture = new Result();
        $resultFixture->offset = 0;
        $resultFixture->count = 100;
        $resultFixture->total = 1000;

        PaginationAdapter::resultOffsetToCursor($resultFixture);

        $this->assertSame('offset:100', $resultFixture->nextCursor);
        $this->assertNull($resultFixture->previousCursor);
    }

    public function testResultOffsetToCursorPageInBetween()
    {
        $resultFixture = new Result();
        $resultFixture->offset = 300;
        $resultFixture->count = 100;
        $resultFixture->total = 1000;

        PaginationAdapter::resultOffsetToCursor($resultFixture);

        $this->assertSame('offset:400', $resultFixture->nextCursor);
        $this->assertSame('offset:200', $resultFixture->previousCursor);
    }

    public function testResultOffsetToCursorPageLastPage()
    {
        $resultFixture = new Result();
        $resultFixture->offset = 900;
        $resultFixture->count = 100;
        $resultFixture->total = 1000;

        PaginationAdapter::resultOffsetToCursor($resultFixture);

        $this->assertNull($resultFixture->nextCursor);
        $this->assertSame('offset:800', $resultFixture->previousCursor);
    }

    public function testResultOffsetToCursorFixCount0()
    {
        $resultFixture = new Result();
        $resultFixture->offset = 400;
        $resultFixture->count = 0;
        $resultFixture->total = 1000;

        PaginationAdapter::resultOffsetToCursor($resultFixture);

        $this->assertNull($resultFixture->nextCursor);
        $this->assertNull($resultFixture->previousCursor);
    }
}
