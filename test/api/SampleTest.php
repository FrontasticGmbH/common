<?php

namespace Frontastic\Common\ApiTests;

use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    public function testArraySum()
    {
        $this->assertEquals(3, array_sum([1, 2]));
    }
}
