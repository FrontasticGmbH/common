<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\ShippingInfo;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressMapper;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\ShopwareCartApi;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class CartMapper extends AbstractDataMapper implements
    LocaleAwareDataMapperInterface,
    ProjectConfigApiAwareDataMapperInterface
{
    use LocaleAwareDataMapperTrait;
    use ProjectConfigApiAwareDataMapperTrait;

    public const MAPPER_NAME = 'cart';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressMapper
     */
    private $addressMapper;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\LineItemsMapper
     */
    private $lineItemsMapper;

    public function __construct(AddressMapper $addressMapper, LineItemsMapper $lineItemsMapper)
    {
        $this->addressMapper = $addressMapper;
        $this->lineItemsMapper = $lineItemsMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $cartData = $this->extractData($resource, $resource);

        $locationData = $this->extractFromDeliveries($cartData, 'location')['address'] ?? null;
        $shippingMethodData = $this->extractFromDeliveries($cartData, 'shippingMethod');

        $lineItems = $this->mapDataToLineItems($cartData['lineItems'] ?? []);

        return new Cart([
            'cartId' => (string)$cartData['token'],
            'cartVersion' => (string)$cartData['name'],
            'sum' => $this->convertPriceToCent($cartData['price']['totalPrice']),
            'currency' => $this->resolveCurrencyCodeFromLocale(),
            'lineItems' => $lineItems,
            'shippingAddress' => empty($locationData) ? null : $this->addressMapper->map($locationData),
            'shippingInfo' => empty($shippingMethodData) ? null : $this->mapDataToShippingInfo($shippingMethodData),
            'shippingMethod' => empty($shippingMethodData) ? null : $this->mapDataToShippingInfo($shippingMethodData),
            'discountCodes' => $this->extractDiscountCodesFromLineItems($lineItems),
            // @TODO: resolve billing address?
        ]);
    }

    private function extractFromDeliveries(array $cartData, string $deliveryItemKey): array
    {
        return $cartData['deliveries'][0][$deliveryItemKey] ?? [];
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem[] $lineItems
     *
     * @return string[]
     */
    private function extractDiscountCodesFromLineItems(array $lineItems): array
    {
        $result = [];
        foreach ($lineItems as $lineItem) {
            if ($lineItem->type === ShopwareCartApi::LINE_ITEM_TYPE_PROMOTION) {
                $result[] = $lineItem->name;
            }
        }

        return $result;
    }

    private function getLineItemsMapper(): LineItemsMapper
    {
        return $this->lineItemsMapper
            ->setProjectConfigApi($this->getProjectConfigApi())
            ->setLocale($this->getLocale());
    }

    /**
     * @param array $lineItemsData
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\LineItem[]
     */
    private function mapDataToLineItems(array $lineItemsData): array
    {
        return $this->getLineItemsMapper()->map($lineItemsData);
    }

    private function mapDataToShippingInfo(array $shippingMethodData): ?ShippingInfo
    {
        if (empty($shippingMethodData)) {
            return null;
        }

        return new ShippingInfo([
            'shippingMethodId' => $shippingMethodData['id'] ?? null,
            'name' => $shippingMethodData['name'] ?? null,
            'price' => $this->convertPriceToCent(
                $shippingMethodData['prices'][0]['currencyPrice'][0]['gross'] ??
                $shippingMethodData['prices'][0]['price'] ??
                0
            ),
        ]);
    }

    private function resolveCurrencyCodeFromLocale(): ?string
    {
        $shopwareCurrency = $this->projectConfigApi->getCurrency($this->getLocale()->currencyId);

        return $shopwareCurrency ? $shopwareCurrency->isoCode : null;
    }
}
