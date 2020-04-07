<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwarePaymentMethod;

class PaymentMethodsMapper implements DataMapperInterface
{
    public const MAPPER_NAME = 'payment-methods';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $result = [];
        foreach ($resource as $paymentMethodData) {
            $result[] = $this->mapDataToShopwarePaymentMethod($paymentMethodData);
        }

        return $result;
    }

    private function mapDataToShopwarePaymentMethod(array $paymentMethodData): ShopwarePaymentMethod
    {
        $paymentMethod = new ShopwarePaymentMethod($paymentMethodData, true);
        $paymentMethod->name = $paymentMethodData['translated']['name'] ?? $paymentMethodData['name'];
        $paymentMethod->description = $paymentMethodData['translated']['description'] ?? $paymentMethodData['description'];

        return $paymentMethod;
    }
}
