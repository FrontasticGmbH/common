<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper;

use DateTimeImmutable;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\SalutationHelper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class AccountMapper extends AbstractDataMapper implements ProjectConfigApiAwareDataMapperInterface
{
    use ProjectConfigApiAwareDataMapperTrait;

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

    public function map($resource)
    {
        $accountData = $this->extractData($resource);

        return new Account([
            'accountId' => (string)$accountData['id'],
            'email' => $accountData['email'],
            'salutation' => $this->resolveSalutation($accountData),
            'firstName' => $accountData['firstName'] ?? null,
            'lastName' => $accountData['lastName'] ?? null,
            'birthday' => isset($accountData['birthday']) ? new DateTimeImmutable($accountData['birthday']) : null,
            'confirmed' => $this->resolveConfirmation($accountData),
            'addresses' => $this->getAccountAddressesMapper()->map($accountData),
            'dangerousInnerAccount' => $accountData,
        ]);
    }

    private function getAccountAddressesMapper(): AccountAddressesMapper
    {
        return $this->accountAddressesMapper->setProjectConfigApi($this->getProjectConfigApi());
    }

    private function resolveConfirmation(array $accountData): bool
    {
        if ($accountData['doubleOptInRegistration'] === true) {
            return $accountData['doubleOptInConfirmDate'] !== null;
        }

        return $accountData['active'];
    }

    private function resolveSalutation(array $accountData): string
    {
        $shopwareSalutation = null;
        if (isset($accountData['salutationId'])) {
            $shopwareSalutation = $this->getProjectConfigApi()->getSalutation($accountData['salutationId']);
        }

        return SalutationHelper::resolveFrontasticSalutation($shopwareSalutation);
    }
}
