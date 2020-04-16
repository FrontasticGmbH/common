<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwarePaymentMethod;

class PaymentMethodsMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'payment-methods';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $paymentMethodsData = $this->extractData($resource);

        $result = [];
        foreach ($paymentMethodsData as $paymentMethodData) {
            $result[] = $this->mapDataToShopwarePaymentMethod($paymentMethodData);
        }

        return $result;
    }

    private function mapDataToShopwarePaymentMethod(array $paymentMethodData): ShopwarePaymentMethod
    {
        $paymentMethod = new ShopwarePaymentMethod($paymentMethodData, true);
        $paymentMethod->name = $this->resolveTranslatedValue($paymentMethodData, 'name');
        $paymentMethod->description = $this->resolveTranslatedValue($paymentMethodData, 'description');

        return $paymentMethod;
    }
}
