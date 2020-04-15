<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper;

use DateTimeImmutable;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;

class AccountMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'account';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AccountAddressesMapper
     */
    private $accountAddressesMapper;

    public function __construct(AccountAddressesMapper $accountAddressesMapper)
    {
        $this->accountAddressesMapper = $accountAddressesMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $accountData = $this->extractData($resource);

        return new Account([
            'accountId' => $accountData['id'],
            'email' => $accountData['email'],
            'salutation' => $accountData['salutationId'] ?? null, // @TODO: map to frontastic salutation
            'firstName' => $accountData['firstName'] ?? null,
            'lastName' => $accountData['lastName'] ?? null,
            'birthday' => isset($accountData['birthday']) ? new DateTimeImmutable($accountData['birthday']) : null,
            'confirmed' => $this->resolveConfirmation($accountData),
            'addresses' => $this->accountAddressesMapper->map($accountData),
            'dangerousInnerAccount' => $accountData,
        ]);
    }

    private function resolveConfirmation(array $accountData): bool
    {
        if ($accountData['doubleOptInRegistration'] === true) {
            return $accountData['doubleOptInConfirmDate'] !== null;
        }

        return $accountData['active'];
    }
}
