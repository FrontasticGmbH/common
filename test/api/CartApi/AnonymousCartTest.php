<?php

namespace Frontastic\Common\ApiTests\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
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

        $this->assertNotEmptyString($cart->cartVersion);

        if ($cart->projectSpecificData !== null) {
            $this->assertSame([], $cart->projectSpecificData);
        }
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
    public function testGetCartByIdReturnsSameCart(Project $project, string $language): void
    {
        $originalCart = $this->getAnonymousCart($project, $language);

        $cartById = $this->getCartApiForProject($project)->getById($originalCart->cartId, $language);
        $this->assertEquals($originalCart, $cartById);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testAddUpdateAndRemoveSingleProductToCart(Project $project, string $language): void
    {
        $originalCart = $this->getAnonymousCart($project, $language);
        $product = $this->getAProduct($project, $language);

        $cartApi = $this->getCartApiForProject($project);

        $cartApi->startTransaction($originalCart);
        $cartApi->addToCart($originalCart, $this->lineItemForProduct($product), $language);
        $cartWithProductAdded = $cartApi->commit($language);

        $this->assertIsArray($cartWithProductAdded->lineItems);
        $this->assertCount(1, $cartWithProductAdded->lineItems);

        $addedLineItem = $cartWithProductAdded->lineItems[0];
        $this->assertInstanceOf(LineItem\Variant::class, $addedLineItem);
        $this->assertNotEmptyString($addedLineItem->lineItemId);
        $this->assertSame($product->name, $addedLineItem->name);
        $this->assertNotEmptyString($addedLineItem->type);
        $this->assertSame(1, $addedLineItem->count);
        $this->assertInstanceOf(Variant::class, $addedLineItem->variant);
        $this->assertProductVariantIsWellFormed($addedLineItem->variant, false);

        $cartApi->startTransaction($cartWithProductAdded);
        $cartApi->updateLineItem($cartWithProductAdded, $addedLineItem, 3, null, $language);
        $cartWithProductCountModified = $cartApi->commit($language);

        $this->assertIsArray($cartWithProductCountModified->lineItems);
        $this->assertCount(1, $cartWithProductCountModified->lineItems);

        $lineItemWithModifiedCount = $cartWithProductCountModified->lineItems[0];
        $this->assertInstanceOf(LineItem::class, $lineItemWithModifiedCount);
        $this->assertSame($addedLineItem->lineItemId, $lineItemWithModifiedCount->lineItemId);
        $this->assertSame($addedLineItem->name, $lineItemWithModifiedCount->name);
        $this->assertSame($addedLineItem->type, $lineItemWithModifiedCount->type);
        $this->assertSame(3, $lineItemWithModifiedCount->count);

        $cartApi->startTransaction($cartWithProductCountModified);
        $cartApi->removeLineItem($cartWithProductCountModified, $lineItemWithModifiedCount, $language);
        $cartWithProductRemoved = $cartApi->commit($language);

        $this->assertIsArray($cartWithProductRemoved->lineItems);
        $this->assertEmpty($cartWithProductRemoved->lineItems);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testSettingTheEmailOfACart(Project $project, string $language): void
    {
        $this->requireAnonymousCheckout($project);
        $originalCart = $this->getAnonymousCart($project, $language);

        $cartApi = $this->getCartApiForProject($project);

        $email = 'integration-tests-not-exists+account-' . uniqid('', true) . '@frontastic.com';
        $cartApi->startTransaction($originalCart);
        $cartApi->setEmail($originalCart, $email, $language);
        $updatedCart = $cartApi->commit($language);

        $this->assertEquals($email, $updatedCart->email);
        $this->assertNotEquals($email, $originalCart->email);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testSettingTheShippingAddressOfACart(Project $project, string $language): void
    {
        $this->requireAnonymousCheckout($project);
        $originalCart = $this->getAnonymousCart($project, $language);

        $cartApi = $this->getCartApiForProject($project);

        $frontasticAddress = $this->getFrontasticAddress();
        $cartApi->startTransaction($originalCart);
        $cartApi->setShippingAddress($originalCart, $frontasticAddress, $language);
        $updatedCart = $cartApi->commit($language);

        $this->assertInstanceOf(Address::class, $updatedCart->shippingAddress);
        $this->assertEquals($frontasticAddress->lastName, $updatedCart->shippingAddress->lastName);
        $this->assertEquals($frontasticAddress->streetName, $updatedCart->shippingAddress->streetName);
        $this->assertEquals($frontasticAddress->streetNumber, $updatedCart->shippingAddress->streetNumber);
        $this->assertEquals($frontasticAddress->postalCode, $updatedCart->shippingAddress->postalCode);
        $this->assertEquals($frontasticAddress->city, $updatedCart->shippingAddress->city);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testOrderSingleProduct(Project $project, string $language): void
    {
        $this->requireAnonymousCheckout($project);
        $cart = $this->getAnonymousCart($project, $language);
        $cartApi = $this->getCartApiForProject($project);

        $cartApi->startTransaction($cart);
        $cartApi->addToCart($cart, $this->lineItemForProduct($this->getAProduct($project, $language)), $language);
        $cartApi->setShippingAddress($cart, $this->getFrontasticAddress(), $language);
        $cart = $cartApi->commit($language);

        $order = $cartApi->order($cart);

        $this->assertInstanceOf(Order::class, $order);

        $this->assertNotEmptyString($order->orderId);

        $this->assertIsInt($order->orderVersion);
        $this->assertGreaterThanOrEqual(0, $order->orderVersion);

        $this->assertNotEmptyString($order->orderState);

        $this->assertInstanceOf(\DateTimeImmutable::class, $order->createdAt);
        $this->assertGreaterThan(
            (new \DateTimeImmutable())->sub(new \DateInterval('PT2H')),
            $order->createdAt
        );
        $this->assertLessThan(
            (new \DateTimeImmutable())->add(new \DateInterval('PT2H')),
            $order->createdAt
        );
    }

    private function getAnonymousCart(Project $project, string $language): Cart
    {
        return $this->getCartApiForProject($project)->getAnonymous(uniqid(), $language);
    }

    private function lineItemForProduct(Product $product, int $count = 1): LineItem
    {
        return new LineItem\Variant([
            'variant' => new Variant([
                'id' => $product->variants[0]->id,
                'sku' => $product->variants[0]->sku,
                'attributes' => $product->variants[0]->attributes,
            ]),
            'count' => $count,
        ]);
    }

    private function getFrontasticAddress(): Address
    {
        return new Address([
            'lastName' => 'FRONTASTIC GmbH',
            'streetName' => 'Hafenweg',
            'streetNumber' => '16',
            'postalCode' => '48155',
            'city' => 'Münster',
            'country' => 'DE',
        ]);
    }

    private function requireAnonymousCheckout(Project $project): void
    {
        $this->requireProjectFeature($project, 'anonymousCheckout');
    }
}
