<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper;

use DateTimeImmutable;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\CoreBundle\Domain\Json\Json;
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
        $accountData = $this->extractElements($resource, $resource);

        if (key_exists('attributes', $accountData)) {
            $accountData = array_merge($accountData, $accountData['attributes']);
        }

        return new Account([
            'accountId' => (string)$accountData['id'],
            'email' => $accountData['email'],
            'salutation' => $this->resolveSalutation($accountData),
            'firstName' => $accountData['firstName'] ?? null,
            'lastName' => $accountData['lastName'] ?? null,
            'birthday' => isset($accountData['birthday']) ? new DateTimeImmutable($accountData['birthday']) : null,
            'confirmed' => $this->resolveConfirmation($accountData),
            'addresses' => $this->getAccountAddressesMapper()->map($accountData),
            'confirmationToken' => $this->resolveConfirmationToken($accountData),
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

    private function resolveConfirmationToken(array $accountData): ?string
    {
        if (key_exists('active', $accountData) && $accountData['active']) {
            return null;
        }

        if (key_exists('hash', $accountData)) {
            return Json::encode(
                [
                    'em' => sha1($accountData['email']),
                    'hash' => $accountData['hash'],
                ]
            );
        }

        return null;
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
