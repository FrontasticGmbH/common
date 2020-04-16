<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\AccountApi\SalutationHelper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class CustomerPatchRequestDataMapper extends AbstractDataMapper implements ProjectConfigApiAwareDataMapperInterface
{
    use ProjectConfigApiAwareDataMapperTrait;

    public const MAPPER_NAME = 'customer-patch-request';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\Account $account
     *
     * @return string[]
     */
    public function map($account)
    {
        return [
            'salutationId' => $this->resolveSalutationId($account->salutation),
            'firstName' => $account->firstName,
            'lastName' => $account->lastName,
            'birthdayDay' => $account->birthday ? $account->birthday->format('d') : null,
            'birthdayMonth' => $account->birthday ? $account->birthday->format('m') : null,
            'birthdayYear' => $account->birthday ? $account->birthday->format('Y') : null,
        ];
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
