<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi\Commercetools;

use Frontastic\Common\AccountApiBundle\Domain\Address;

class MapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Mapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new Mapper();
    }

    /**
     * @dataProvider provideMapAddressToDataExamples
     */
    public function testMapAddressToData($addressFixture, $expectedAddress)
    {
        $actualAddress = $this->mapper->mapAddressToData($addressFixture);

        $this->assertEquals($expectedAddress, $actualAddress);
    }

    public function provideMapAddressToDataExamples()
    {
        return [
            'Empty address' => [
                new Address(),
                [
                    'id' => null,
                    'salutation' => null,
                    'firstName' => null,
                    'lastName' => null,
                    'streetName' => null,
                    'streetNumber' => null,
                    'additionalStreetInfo' => null,
                    'additionalAddressInfo' => null,
                    'postalCode' => null,
                    'city' => null,
                    'country' => null,
                    'state' => null,
                    'phone' => null,
                ],
            ],
            'Full address' => [
                $this->getAddress(),
                $this->getAddressFixture(),
            ],
        ];
    }

    /**
     * @return Address
     */
    private function getAddress(): Address
    {
        return new Address([
            'addressId' => 'vSO4VhF-',
            'salutation' => 'Herr',
            'firstName' => 'Max',
            'lastName' => 'Mustermann',
            'streetName' => 'Musterstrasse',
            'streetNumber' => '23',
            'additionalStreetInfo' => '',
            'additionalAddressInfo' => '',
            'postalCode' => '12345',
            'city' => 'Musterstadt',
            'country' => 'DE',
            'state' => null,
            'phone' => '',
        ]);
    }

    /**
     * @return array
     */
    private function getAddressFixture(): array
    {
        return $this->loadFixture('addressFixture.json');
    }

    /**
     * @return mixed
     */
    private function loadFixture(string $fileName)
    {
        return json_decode(file_get_contents(__DIR__ . '/_fixtures/' . $fileName), true);
    }
}
