<?php


namespace Frontastic\Common\ShopifyBundle\Domain\Mapper;


use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;

class ShopifyAccountMapper
{
    public function mapDataToAccount(array $accountData): Account
    {
        $addresses = [];

        if (!empty($accountData['addresses']['edges'])) {
            $addresses = array_map(
                function (array $addressData) : Address {
                    return $this->mapDataToAddress($addressData['node']);
                },
                $accountData['addresses']['edges']
            );
        }

        return new Account([
            'accountId' => $accountData['id'] ?? null,
            'firstName' => $accountData['firstName'] ?? null,
            'lastName' => $accountData['lastName'] ?? null,
            'email' => $accountData['email'] ?? null,
            'addresses' => $addresses,
            'confirmed' => true,
        ]);
    }

    public function mapDataToAddress(array $addressData): ?Address
    {
        if (empty($addressData)) {
            return null;
        }

        return new Address([
            'addressId' => $addressData['id'] ?? null,
            'streetName' => $addressData['address1'] ?? null,
            'streetNumber' => $addressData['address2'] ?? null,
            'city' => $addressData['city'] ?? null,
            'country' => $addressData['country'] ?? null,
            'firstName' => $addressData['firstName'] ?? null,
            'lastName' => $addressData['lastName'] ?? null,
            'phone' => $addressData['phone'] ?? null,
            'state' => $addressData['province'] ?? null,
            'postalCode' => $addressData['zip'] ?? null,
        ]);
    }

    public function mapAddressToData(Address $address): string
    {
        return "
            address1: \"$address->streetName\",
            address2: \"$address->streetNumber\",
            city: \"$address->city\",
            country: \"$address->country\",
            firstName: \"$address->firstName\",
            lastName: \"$address->lastName\",
            phone: \"$address->phone\",
            province: \"$address->state \",
            zip: \"$address->postalCode\",
        ";
    }
}
