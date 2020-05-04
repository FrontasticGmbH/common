<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\ShopwareCartApi;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class LineItemsMapper extends AbstractDataMapper implements
    LocaleAwareDataMapperInterface,
    ProjectConfigApiAwareDataMapperInterface
{
    use LocaleAwareDataMapperTrait;
    use ProjectConfigApiAwareDataMapperTrait;

    public const MAPPER_NAME = 'line-item';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($lineItemsData)
    {
        $result = [];
        foreach ($lineItemsData as $lineItemData) {
            switch ($lineItemData['type']) {
                case ShopwareCartApi::LINE_ITEM_TYPE_PRODUCT:
                    $lineItemData = new LineItem\Variant([
                        'lineItemId' => (string)$lineItemData['id'],
                        'name' => $lineItemData['label'],
                        'count' => $lineItemData['quantity'],
                        'price' => $this->convertPriceToCent($lineItemData['price']['unitPrice']),
                        'totalPrice' => $this->convertPriceToCent($lineItemData['price']['totalPrice']),
                        'variant' => new Variant([
                            'id' => $lineItemData['referencedId'],
                            'sku' => $lineItemData['referencedId'],
                            'images' => [
                                $lineItemData['cover']['url'],
                            ],
                            'attributes' => array_map(static function ($option) {
                                return [$option['group'] => $option['option']];
                            }, $lineItemData['payload']['options'])
                        ]),
                    ]);
                    break;
                case ShopwareCartApi::LINE_ITEM_TYPE_PROMOTION:
                default:
                    $lineItemData = new LineItem([
                        'lineItemId' => (string)$lineItemData['id'],
                        'type' => $lineItemData['type'],
                        'name' => $lineItemData['label'],
                        'count' => $lineItemData['quantity'],
                        'price' => $this->convertPriceToCent($lineItemData['price']['unitPrice']),
                        'totalPrice' => $this->convertPriceToCent($lineItemData['price']['totalPrice']),
                    ]);
                    break;
            }

            $lineItemData->currency = $this->resolveCurrencyCodeFromLocale();
            $lineItemData->dangerousInnerItem = $lineItemData;

            $result[] = $lineItemData;
        }

        return $result;
    }

    private function resolveCurrencyCodeFromLocale(): ?string
    {
        $shopwareCurrency = $this->projectConfigApi->getCurrency($this->getLocale()->currencyId);

        return $shopwareCurrency ? $shopwareCurrency->isoCode : null;
    }
}
