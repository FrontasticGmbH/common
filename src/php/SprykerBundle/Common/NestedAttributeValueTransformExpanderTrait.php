<?php


namespace Frontastic\Common\SprykerBundle\Common;

use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\ProductApiBundle\Domain\Variant;

trait NestedAttributeValueTransformExpanderTrait
{
    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem\Variant|mixed $item
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\LineItem\Variant|mixed
     */
    protected function expandLineItem($item)
    {
        $this->expandVariant($item->variant);

        return $item;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Variant $variant
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Variant
     */
    protected function expandVariant(Variant $variant): Variant
    {
        foreach ($variant->attributes as $key => $value) {
            if (strpos($key, '_label_') === 0) {
                // TODO: Handle labels with localisation
                // $realKey = str_replace('_label_', '', $key);
                // $this->attributeValueToNested($variant, $realKey);
                // $variant->attributes[$realKey]['label'] = $value;
                unset($variant->attributes[$key]);
            } else {
                $this->attributeValueToNested($variant, $key);
            }
        }

        return $variant;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Variant $variant
     * @param string $key
     *
     * @return void
     */
    private function attributeValueToNested(Variant $variant, string $key): void
    {
        if (strpos($key, '_') === 0) {
            return;
        }

        $value = $variant->attributes[$key] ?? null;

        if (!is_array($value)) {
            $variant->attributes[$key] = [
                'key' => $value,
                'label' => $value
            ];
        }
    }
}
