<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\SalutationHelper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class AddressMapper extends AbstractDataMapper implements ProjectConfigApiAwareDataMapperInterface
{
    use ProjectConfigApiAwareDataMapperTrait;

    public const MAPPER_NAME = 'address';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $addressData = $this->extractElements($resource, $resource);

        if (key_exists('attributes', $resource)) {
            $addressData = array_merge($addressData, $addressData['attributes']);
        }

        return new Address([
            'addressId' => $addressData['id'] ? (string)$addressData['id'] : null,
            'salutation' => $this->resolveSalutation($addressData),
            'firstName' => $addressData['firstName'] ?? null,
            'lastName' => $addressData['lastName'] ?? null,
            'streetName' => $addressData['street'] ?? null,
            'additionalAddressInfo' => $addressData['additionalAddressLine1'] ?? null,
            'additionalStreetInfo' => $addressData['additionalAddressLine2'] ?? null,
            'postalCode' => $addressData['zipcode'] ?? null,
            'city' => $addressData['city'] ?? null,
            'country' => $this->resolveCountry($addressData),
            'phone' => $addressData['phoneNumber'] ?? null,
            'dangerousInnerAddress' => $addressData,
        ]);
    }

    private function resolveCountry(array $addressData): ?string
    {
        $resolvedIso = null;
        if (isset($addressData['country'])) {
            $resolvedIso = $addressData['country']['iso'] ?? null;
        }

        if ($resolvedIso === null && isset($addressData['countryId'])) {
            $shopwareCountry = $this->getProjectConfigApi()->getCountryByCriteria($addressData['countryId']);

            return $shopwareCountry ? $shopwareCountry->iso : null;
        }

        return $resolvedIso;
    }

    private function resolveSalutation(array $addressData): ?string
    {
        $shopwareSalutation = null;
        if (isset($addressData['salutationId'])) {
            $shopwareSalutation = $this->getProjectConfigApi()->getSalutation($addressData['salutationId']);
        }

        return SalutationHelper::resolveFrontasticSalutation($shopwareSalutation);
    }
}
