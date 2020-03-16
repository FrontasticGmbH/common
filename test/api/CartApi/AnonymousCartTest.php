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
    const FRONTASTIC_ADDRESS = [
        'lastName' => 'FRONTASTIC GmbH',
        'streetName' => 'Hafenweg',
        'streetNumber' => '16',
        'postalCode' => '48155',
        'city' => 'Münster',
        'country' => 'DE',
    ];

    /**
     * @dataProvider projectAndLanguage
     */
    public function testNewAnonymousCartIsEmpty(Project $project, string $language): void
    {
        $cart = $this->getAnonymousCart($project, $language);

        $this->assertNotEmptyString($cart->cartId);
        $this->assertNotEmptyString($cart->cartVersion);

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
    public function testGetCartByIdReturnsSameCart(Project $project, string $language): void
    {
        $originalCart = $this->getAnonymousCart($project, $language);
        $this->assertNotEmptyString($originalCart->cartId);

        $cartById = $this->getCartApiForProject($project)->getById($originalCart->cartId, $language);
        $this->assertEquals($originalCart, $cartById);
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

    /**
     * @dataProvider projectAndLanguage
     */
    public function testSettingTheShippingAddressOfACart(Project $project, string $language): void
    {
        $originalCart = $this->getAnonymousCart($project, $language);

        $cartApi = $this->getCartApiForProject($project);

        $cartApi->startTransaction($originalCart);
        $cartApi->setShippingAddress($originalCart, self::FRONTASTIC_ADDRESS, $language);
        $updatedCart = $cartApi->commit($language);

        $this->assertGreaterThan($originalCart->cartVersion, $updatedCart->cartVersion);
        $this->assertFrontasticAddress($updatedCart->shippingAddress);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testOrderSingleProduct(Project $project, string $language): void
    {
        $cart = $this->getAnonymousCart($project, $language);
        $cartApi = $this->getCartApiForProject($project);

        $cartApi->startTransaction($cart);
        $cartApi->addToCart($cart, $this->lineItemForProduct($this->getAProduct($project, $language)), $language);
        $cartApi->setShippingAddress(
            $cart,
            self::FRONTASTIC_ADDRESS,
            $language
        );
        $cart = $cartApi->commit($language);

        $order = $cartApi->order($cart);

        $this->assertInstanceOf(Order::class, $order);

        $this->assertNotEmptyString($order->orderId);

        $this->assertInternalType('int', $order->orderVersion);
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
                'sku' => $product->variants[0]->sku,
                'attributes' => $product->variants[0]->attributes,
            ]),
            'count' => $count,
        ]);
    }

    private function assertFrontasticAddress(?Address $address): void
    {
        $this->assertInstanceOf(Address::class, $address);

        $this->assertSame(self::FRONTASTIC_ADDRESS['lastName'], $address->lastName);
        $this->assertSame(self::FRONTASTIC_ADDRESS['streetName'], $address->streetName);
        $this->assertSame(self::FRONTASTIC_ADDRESS['streetNumber'], $address->streetNumber);
        $this->assertSame(self::FRONTASTIC_ADDRESS['postalCode'], $address->postalCode);
        $this->assertSame(self::FRONTASTIC_ADDRESS['city'], $address->city);
        $this->assertSame(self::FRONTASTIC_ADDRESS['country'], $address->country);

        $this->assertNull($address->addressId);
        $this->assertNull($address->salutation);
        $this->assertNull($address->firstName);
        $this->assertNull($address->additionalStreetInfo);
        $this->assertNull($address->additionalAddressInfo);
        $this->assertNull($address->phone);

        $this->assertFalse($address->isDefaultBillingAddress);
        $this->assertFalse($address->isDefaultShippingAddress);
    }
}
