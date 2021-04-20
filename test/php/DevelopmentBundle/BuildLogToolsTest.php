<?php

namespace Frontastic\Common\DevelopmentBundle;

use PHPUnit\Framework\TestCase;

class BuildLogToolsTest extends TestCase
{
    public function testRemovesParallelPieces()
    {
        $logFixture = file_get_contents(__DIR__ . '/_fixtures/ant_with_paralell.log');

        // Make sure pre-conditions of the fixture are met
        $this->assertStringContainsString('[PARALLEL BUILD START]', $logFixture);
        $this->assertStringContainsString('[PARALLEL BUILD END]', $logFixture);

        $cleanedLog = BuildLogTools::trimParallelBuildLog($logFixture);

        $this->assertStringNotContainsString('[PARALLEL BUILD START]', $cleanedLog);
        $this->assertStringNotContainsString('[PARALLEL BUILD END]', $cleanedLog);
    }

    public function testDoesNotChangeWhenThereIsNoParallel()
    {
        $logFixture = file_get_contents(__DIR__ . '/_fixtures/ant_without_paralell.log');

        $cleanedLog = BuildLogTools::trimParallelBuildLog($logFixture);

        $this->assertEquals(
            // Our strtok() removes blank lines
            preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $logFixture),
            $cleanedLog
        );
    }
}
