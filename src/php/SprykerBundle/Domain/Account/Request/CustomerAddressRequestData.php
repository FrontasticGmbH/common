<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account\Request;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;
use Frontastic\Common\SprykerBundle\Domain\Account\SalutationHelper;

class CustomerAddressRequestData extends AbstractRequestData
{
    /**
     * @var string
     */
    private $salutation;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $address1;

    /**
     * @var string
     */
    private $address2;

    /**
     * @var string
     */
    private $zipCode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $iso2Code;

    /**
     * @var bool
     */
    private $isDefaultBilling;

    /**
     * @var bool
     */
    private $isDefaultShipping;

    /**
     * @var string|null
     */
    public $company;

    public function __construct(
        ?string $salutation,
        ?string $firstName,
        ?string $lastName,
        ?string $address1,
        ?string $address2,
        ?string $zipCode,
        ?string $city,
        ?string $country,
        ?string $iso2Code,
        bool $isDefaultBilling,
        bool $isDefaultShipping
    ) {
        $this->salutation = $salutation;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address1 = $address1;
        $this->address2 = $address2;
        $this->zipCode = $zipCode;
        $this->city = $city;
        $this->country = $country;
        $this->iso2Code = $iso2Code;
        $this->isDefaultBilling = $isDefaultBilling;
        $this->isDefaultShipping = $isDefaultShipping;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        return [
            'salutation' => $this->salutation,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'zipCode' => $this->zipCode,
            'city' => $this->city,
            'country' => $this->country,
            'iso2Code' => $this->iso2Code,
            'isDefaultBilling' => $this->isDefaultBilling,
            'isDefaultShipping' => $this->isDefaultShipping,
            'company' => $this->company,
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'addresses';
    }

    /**
     * @param Address $address
     * @return CustomerAddressRequestData
     */
    public static function createFromAddress(Address $address): self
    {
        $result = new self(
            SalutationHelper::getSprykerSalutation($address->salutation),
            $address->firstName,
            $address->lastName,
            $address->streetName,
            $address->streetNumber,
            $address->postalCode,
            $address->city,
            $address->country,
            mb_substr($address->country, 0, 2),
            $address->isDefaultBillingAddress,
            $address->isDefaultShippingAddress
        );

        if (isset($address->company)) {
            $result->company = $address->company;
        }

        return $result;
    }
}
