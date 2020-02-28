<?php

namespace Frontastic\Common\ApiTests\CartApi;

use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class AnonymousCartTest extends FrontasticApiTestCase
{
    /**
     * @dataProvider projectAndLanguage
     */
    public function testNewAnonymousCartIsEmpty(Project $project, string $language): void
    {
        $cart = $this->getAnonymousCart($project, $language);

        $this->assertNotEmptyString($cart->cartId);

        $this->assertInternalType('int', $cart->cartVersion);
        $this->assertGreaterThanOrEqual(0, $cart->cartVersion);

        $this->assertSame([], $cart->custom);
        $this->assertSame([], $cart->lineItems);

        $this->assertNull($cart->email);
        $this->assertNull($cart->birthday);
        $this->assertNull($cart->shippingMethod);
        $this->assertNull($cart->shippingAddress);
        $this->assertNull($cart->billingAddress);

        $this->assertSame(0, $cart->sum);
        $this->assertNotEmptyString($cart->currency);

        $this->assertSame([], $cart->payments);
        $this->assertSame([], $cart->discountCodes);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testAddSingleProductToCart(Project $project, string $language): void
    {
        $originalCart = $this->getAnonymousCart($project, $language);
        $product = $this->getAProduct($project, $language);

        $cartApi = $this->getCartApiForProject($project);

        $cartApi->startTransaction($originalCart);
        $cartApi->addToCart($originalCart, $this->lineItemForProduct($product), $language);
        $updatedCart = $cartApi->commit($language);

        $this->assertGreaterThan($originalCart->cartVersion, $updatedCart->cartVersion);

        $this->assertCount(1, $updatedCart->lineItems);
        foreach ($updatedCart->lineItems as $lineItem) {
            $this->assertInstanceOf(LineItem::class, $lineItem);

            $this->assertNotEmptyString($lineItem->lineItemId);
            $this->assertSame($product->name, $lineItem->name);
            $this->assertNotEmptyString($lineItem->type);
            $this->assertSame(1, $lineItem->count);
        }
    }

    private function getAnonymousCart(Project $project, string $language): Cart
    {
        return $this->getCartApiForProject($project)->getAnonymous(uniqid(), $language);
    }

    private function lineItemForProduct(Product $product, int $count = 1): LineItem
    {
        return new LineItem\Variant([
            'variant' => new Variant([
                'sku' => $product->variants[0]->sku,
                'attributes' => $product->variants[0]->attributes,
            ]),
            'count' => $count,
        ]);
    }
}
