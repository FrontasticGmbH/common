<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account\Mapper;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\SprykerBundle\Domain\Account\SalutationHelper;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use WoohooLabs\Yang\JsonApi\Exception\DocumentException;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class AccountMapper implements MapperInterface
{
    public const MAPPER_NAME = 'account';

    /**
     * @var AddressMapper
     */
    protected $addressMapper;

    public function __construct(AddressMapper $addressMapper)
    {
        $this->addressMapper = $addressMapper;
    }

    /**
     * @param ResourceObject $resource
     * @return Account
     */
    public function mapResource(ResourceObject $resource): Account
    {
        $account = new Account();
        $account->accountId = $resource->id();
        $account->email = $resource->attribute('email');
        $account->firstName = $resource->attribute('firstName');
        $account->lastName = $resource->attribute('lastName');
        $account->salutation = SalutationHelper::getFrontasticSalutation($resource->attribute('salutation'));
        $account->birthday = $resource->attribute('dateOfBirth') ?
            new \DateTime($resource->attribute('dateOfBirth')) :
            null;
        $account->addresses = $this->mapAddresses($resource);
        $account->confirmed = true;

        $account->dangerousInnerAccount = $resource->attributes();

        return $account;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }

    /**
     * @param ResourceObject $resource
     * @return Address[]
     */
    private function mapAddresses(ResourceObject $resource): array
    {
        try {
            return $this->addressMapper->mapResourceArray($resource->relationship('addresses')->resources());
        } catch (DocumentException $e) {
            return []; // No address on registering is expected behaviour
        }
    }
}
