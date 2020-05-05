<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\AccountApi\SalutationHelper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class AddressCreateRequestDataMapper extends AbstractDataMapper implements ProjectConfigApiAwareDataMapperInterface
{
    use ProjectConfigApiAwareDataMapperTrait;

    public const MAPPER_NAME = 'address-create-request';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\Address $address
     *
     * @return string[]
     */
    public function map($address)
    {
        return [
            'id' => $address->addressId ?? null,
            'salutationId' => $this->resolveSalutationId($address->salutation),
            'title' => null, // Not part of Frontastic Address,
            'firstName' => $address->firstName,
            'lastName' => $address->lastName,
            'company' => null, // Not part of Frontastic Address
            'department' => null, // Not part of Frontastic Address
            'vatId' => null, // Not part of Frontastic Address
            'street' => trim(sprintf('%s %s', $address->streetName, $address->streetNumber)),
            'additionalAddressLine1' => $address->additionalAddressInfo ?? '',
            'additionalAddressLine2' => $address->additionalStreetInfo ?? '',
            'zipcode' => $address->postalCode,
            'city' => $address->city,
            'countryId' => $this->resolveCountryId($address->country),
            'countryStateId' => null, // Not part of Frontastic address
            'phoneNumber' => $address->phone,
        ];
    }

    private function resolveCountryId(?string $frontasticCountry): ?string
    {
        if ($frontasticCountry === null) {
            return null;
        }

        $shopwareCountry = $this->getProjectConfigApi()->getCountryByCriteria($frontasticCountry);

        return $shopwareCountry ? $shopwareCountry->id : null;
    }

    private function resolveSalutationId(?string $frontasticSalutation): ?string
    {
        if ($frontasticSalutation === null) {
            return null;
        }

        $shopwareSalutation = $this->getProjectConfigApi()->getSalutation(
            SalutationHelper::resolveShopwareSalutation($frontasticSalutation)
        );

        return $shopwareSalutation ? $shopwareSalutation->id : null;
    }
}
