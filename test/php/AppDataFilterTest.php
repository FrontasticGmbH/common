<?php

declare(strict_types=1);

namespace Frontastic\Common;

use PHPUnit\Framework\TestCase;

class AppDataFilterTest extends TestCase
{
    public function testFilterAppData(): void
    {
        $filter = new AppDataFilter(['children'], ['_type'], ['[foo][bar]']);

        $result = $filter->filterAppData([
            'foo' => [
                'bar' => 'baz',
            ],
            'children' => [],
            '_type' => 'TestType',
            'nullValue' => null,
            'emptyString' => '',
        ]);

        self::assertEquals(['children' => [], 'emptyString' => ''], $result);
    }
}
