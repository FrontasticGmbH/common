<?php


namespace Frontastic\Common\CoreBundle\Domain;



use Frontastic\Common\CoreBundle\Domain\Json\InvalidJsonDecodeException;
use Frontastic\Common\CoreBundle\Domain\Json\Json;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    public function testEncodeShouldReturnJsonString() {
        $result = Json::encode(['name' => 'foo', 'number' => 5.123, 'boolean' => false]);

        $this->assertEquals('{"name":"foo","number":5.123,"boolean":false}', $result);
    }

    public function testDecodeShouldReturnArray()  {
        $result = Json::decode('{"name":"foo","number":5.123,"boolean":false}', true);

        $this->assertIsArray($result);
        $this->assertEquals(['name' => 'foo', 'number' => 5.123, "boolean" => false], $result);
    }

    public function testDecodeShouldReturnObject()  {
        $result = Json::decode('{"name":"foo","number":5.123,"boolean":false}');

        $this->assertIsObject($result);
        $this->assertEquals('foo', $result->name);
        $this->assertEquals(5.123, $result->number);
        $this->assertEquals(false, $result->boolean);
    }

    public function testDecodeWithInvalidJsonShouldThrowException() {
        $this->markTestSkipped('Exception is not yet implemented');
        $this->expectException(InvalidJsonDecodeException::class);

        Json::decode('{"name":"foo","number":5.123,"boolean":false"');
    }

    public function testDecodeWithInvalidJsonShouldThrowExceptionWithNativeDecoder() {
        $this->markTestSkipped('Exception is not yet implemented');
        $this->expectException(InvalidJsonDecodeException::class);

        Json::decode('{"name":"foo","number":5.123,"boolean":false"', false, 512, 0, true);
    }

}
