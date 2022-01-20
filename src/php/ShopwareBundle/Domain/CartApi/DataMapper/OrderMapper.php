<?php

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use DateTimeImmutable;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\ShippingInfo;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class OrderMapper extends AbstractDataMapper implements
    LocaleAwareDataMapperInterface,
    ProjectConfigApiAwareDataMapperInterface
{
    use LocaleAwareDataMapperTrait,
        ProjectConfigApiAwareDataMapperTrait;

    public const MAPPER_NAME = 'order';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressMapper
     */
    private $addressMapper;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\LineItemsMapper
     */
    private $lineItemsMapper;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\ShippingInfoMapper
     */
    private $shippingInfoMapper;

    public function __construct(
        AddressMapper $addressMapper,
        LineItemsMapper $lineItemsMapper,
        ShippingInfoMapper $shippingInfoMapper
    ) {
        $this->addressMapper = $addressMapper;
        $this->lineItemsMapper = $lineItemsMapper;
        $this->shippingInfoMapper = $shippingInfoMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $orderData = $this->extractData($resource, $resource);

        $shippingAddress = [];
        $billingAddress = [];
        $shippingInfo = [];

        if ($orderData['addresses']) {
            $shippingAddress = $this->mapShippingAddress($orderData['addresses'], $orderData['billingAddressId']);
            $billingAddress = $this->mapBillingAddress($orderData['addresses'], $orderData['billingAddressId']);
        }

        if ($orderData['deliveries']) {
            $shippingAddress = $this->mapDeliveriesToShippingAddress($orderData['deliveries']);
            $shippingInfo = $this->mapShippingInfo($orderData['deliveries']);
        }

        $order = new Order([
            'cartId' => $resource['headers']['sw-context-token'] ?? null,
            'currency' => $this->resolveCurrencyCode($orderData['currencyId']),
            'orderState' => $orderData['stateMachineState']['technicalName'],
            'createdAt' => new DateTimeImmutable($orderData['orderDateTime']),
            'orderId' => $orderData['orderNumber'],
            'orderVersion' => $orderData['versionId'],
            'lineItems' => $this->mapDataToLineItems($orderData['lineItems']),
            'email' => $orderData['orderCustomer']['email'] ?? null,
            'shippingAddress' => $shippingAddress,
            'billingAddress' => $billingAddress,
            'shippingInfo' => $shippingInfo,
            'sum' => $this->convertPriceToCent($orderData['price']['totalPrice']),
// @TODO: no data yet
//            'payments' => $this->mapPayments($order),
// @TODO: no data, lineItems are not returned together with other order information
            'discountCodes' => $this->mapDiscounts($orderData),
            'dangerousInnerOrder' => $orderData,
        ]);

        //@TODO: Should we handle this data here or delegate it to the client?
        $order->projectSpecificData = $orderData['customFields'];

        return $order;
    }

    private function getAddressMapper(): AddressMapper
    {
        return $this->addressMapper->setProjectConfigApi($this->getProjectConfigApi());
    }

    private function getLineItemsMapper(): LineItemsMapper
    {
        return $this->lineItemsMapper->setLocale($this->getLocale());
    }

    private function getShippingInfoMapper(): ShippingInfoMapper
    {
        return $this->shippingInfoMapper;
    }

    /**
     * @param array|null $lineItemsData
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\LineItem[]
     */
    private function mapDataToLineItems(?array $lineItemsData): array
    {
        if (empty($lineItemsData)) {
            return [];
        }

        return $this->getLineItemsMapper()->map($lineItemsData);
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

    private function mapShippingAddress(array $addresses, string $billingAddressId): ?Address
    {
        $shippingAddressData = $addresses[0] ?? null;

        if (count($addresses) > 1) {
            foreach ($addresses as $addressData) {
                if ($addressData['id'] !== $billingAddressId) {
                    $shippingAddressData = $addressData;
                }
            }
        }

        if ($shippingAddressData === null) {
            return null;
        }

        return $this->getAddressMapper()->map($shippingAddressData);
    }

    private function mapShippingInfo(?array $deliveries): ?ShippingInfo
    {
        if (empty($deliveries)) {
            return null;
        }

        // We will only map the first delivery info
        return $this->getShippingInfoMapper()->map($deliveries[0]);
    }

    private function mapDeliveriesToShippingAddress(?array $deliveries): ?Address
    {
        $shippingOrderAddress = $deliveries[0]['shippingOrderAddress'] ?? null;

        if ($shippingOrderAddress === null) {
            return null;
        }

        return $this->getAddressMapper()->map($shippingOrderAddress);
    }

    private function resolveCurrencyCode(string $currencyId): ?string
    {
        $shopwareCurrency = $this->projectConfigApi->getCurrency($currencyId);

        return $shopwareCurrency ? $shopwareCurrency->isoCode : null;
    }
}
