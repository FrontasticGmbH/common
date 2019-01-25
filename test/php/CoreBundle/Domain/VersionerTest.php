<?php

namespace Frontastic\Common\CoreBundle\Domain;

class VersionerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Versioner
     */
    private $versioner;

    public function setUp()
    {
        $this->versioner = new Versioner();
    }

    public function testDetectVersionable()
    {
        $versionable = new \stdClass();
        $versionable->versions = [];

        $this->assertTrue($this->versioner->supports($versionable));
    }

    public function testDetectNonVersionable()
    {
        $versionable = new \stdClass();

        $this->assertFalse($this->versioner->supports($versionable));
    }

    public function testCreatesSnapshot()
    {
        $versionable = new \stdClass();
        $versionable->data = 23;
        $versionable->versions = [];

        $this->versioner->versionSnapshot($versionable);

        $this->assertCount(1, $versionable->versions);
        $this->assertEquals(23, $versionable->versions[0]->data);

        return $versionable;
    }

    /**
     * @depends testCreatesSnapshot
     */
    public function testPrependsNewestVersion($versionable)
    {
        $versionable->data = 'new version';

        $this->versioner->versionSnapshot($versionable);

        $this->assertCount(2, $versionable->versions);
        $this->assertEquals('new version', $versionable->versions[0]->data);

        return $versionable;
    }

    /**
     * @depends testPrependsNewestVersion
     */
    public function testRemovesVersionsFromSnapshot($versionable)
    {
        foreach ($versionable->versions as $snapshot) {
            $this->assertEquals([], $snapshot->versions);
        }
    }

    public function testKeepsMaximum32Versions()
    {
        $versionable = new \stdClass();
        $versionable->data = 42;
        $versionable->versions = [];

        for ($i = 0; $i < 40; $i++) {
            $this->versioner->versionSnapshot($versionable);
        }

        $this->assertCount(32, $versionable->versions);
    }
}
