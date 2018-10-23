<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

class SequenceProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testSequenceIsNumeric()
    {
        $sequenceProvider = new SequenceProvider();

        $this->assertTrue((bool) preg_match('(^[0-9]+$)', $sequenceProvider->get()));
    }

    public function testSequenceIsPadded()
    {
        $sequenceProvider = new SequenceProvider();

        $this->assertSame(
            18,
            strlen($sequenceProvider->get())
        );
    }

    public function getSequences()
    {
        return array(
            ['001505838239883786', '001505838239883787'],
            ['001505838239899999', '001505838239900000'],
            ['099999999999999999', '100000000000000000'],
        );
    }

    /**
     * @dataProvider getSequences
     */
    public function testGetNextSequence(string $input, string $expected)
    {
        $sequenceProvider = new SequenceProvider();

        $this->assertSame(
            $expected,
            $sequenceProvider->next($input)
        );
    }
}
