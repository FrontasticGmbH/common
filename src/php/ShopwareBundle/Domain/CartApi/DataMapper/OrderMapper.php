<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class OrderMapper extends AbstractDataMapper  implements ProjectConfigApiAwareDataMapperInterface
{
    use ProjectConfigApiAwareDataMapperTrait;

    public const MAPPER_NAME = 'order';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressMapper
     */
    private $addressMapper;

    public function __construct(AddressMapper $addressMapper)
    {
        $this->addressMapper = $addressMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $orderData = $this->extractData($resource);

        return new Order([
            'cartId' => $orderData['id'],
            'custom' => $orderData['customFields'],
            'currency' => $this->resolveCurrencyCode($orderData['currencyId']),
            'orderState' => $orderData['stateMachineState']['technicalName'],
            'createdAt' => new \DateTimeImmutable($orderData['orderDateTime']),
            'orderId' => $orderData['orderNumber'],
            'orderVersion' => $orderData['versionId'],
// @TODO: no data
//            'lineItems' => $this->mapDataToLineItems($order, $locale),
            'email' => $orderData['orderCustomer']['email'] ?? null,
// @TODO: no data
//            'shippingMethod' => $this->mapShippingMethod($orderData['shippingInfo'] ?? []),
// @TODO: no data?
//            'shippingAddress' => $this->mapAddress($orderData['shippingAddress'] ?? []),
            'billingAddress' => $this->mapBillingAddress($orderData['addresses'], $orderData['billingAddressId']),
            'sum' => $this->convertPriceToCent($orderData['price']['totalPrice']),
// @TODO: no data yet
//            'payments' => $this->mapPayments($order),
// @TODO: no data
//            'discountCodes' => $this->mapDiscounts($order),
            'dangerousInnerOrder' => $orderData,
        ]);
    }

    private function getAddressMapper(): AddressMapper
    {
        return $this->addressMapper->setProjectConfigApi($this->getProjectConfigApi());
    }

    private function mapBillingAddress(array $addresses, string $billingAddressId): ?Address
    {
        foreach ($addresses as $addressData) {
            if ($addressData['id'] === $billingAddressId) {
                return $this->getAddressMapper()->map($addressData);
            }
        }

        return null;
    }

    private function resolveCurrencyCode(string $currencyId): ?string
    {
        $shopwareCurrency = $this->projectConfigApi->getCurrency($currencyId);

        return $shopwareCurrency ? $shopwareCurrency->isoCode : null;
    }
}
