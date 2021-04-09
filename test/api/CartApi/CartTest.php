<?php

namespace Frontastic\Common\ApiTests\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class CartTest extends FrontasticApiTestCase
{
    /**
     * @dataProvider projectAndLanguage
     */
    public function testNewCartIsEmpty(Project $project, string $language): void
    {
        $account = $this->getAccount($project, $language);
        $cart = $this->getCart($project, $account, $language);

        $this->assertNotEmptyString($cart->cartId);
        $this->assertNotEmptyString($cart->cartVersion);

        if ($cart->projectSpecificData !== null) {
            $this->assertSame([], $cart->projectSpecificData);
        }

        $this->assertSame([], $cart->lineItems);
        $this->assertSame(0, $cart->sum);
        $this->assertNotEmptyString($cart->currency);
        $this->assertSame([], $cart->payments);
        $this->assertSame([], $cart->discountCodes);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testOrderSingleProduct(Project $project, string $language): void
    {
        $account = $this->getAccount($project, $language);
        $cart = $this->getCart($project, $account, $language);

        $cartApi = $this->getCartApiForProject($project);

        $cartApi->startTransaction($cart);
        $cartApi->addToCart($cart, $this->getLineItemForProduct($this->getAProduct($project, $language)), $language);
        try {
            $cartApi->setShippingAddress($cart, $this->getFrontasticAddress(), $language);
        } catch (\RuntimeException $e) {
            // In case that the backend didn't implement CartApi::setShippingAddress,
            // catch the exception but do nothing
        }
        $cart = $cartApi->commit($language);

        $order = $cartApi->order($cart, $language);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertNotNull($order->orderId);
        $this->assertNotNull($order->orderVersion);
        $this->assertNotNull($order->orderState);
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

    private function getTestAddressData(?string $salutation = null): array
    {
        return [
            'salutation' => $salutation,
            'firstName' => 'Ashley',
            'lastName' => 'Stoltenberg',
            'streetName' => 'Test str.',
            'streetNumber' => '11',
            'additionalAddressInfo' => 'Additional addr info',
            'additionalStreetInfo' => 'Additional str info',
            'postalCode' => '123456',
            'city' => 'Berlin',
            'country' => 'DE',
            'phone' => '+49 12 1234 12234',
            'isDefaultBillingAddress' => true,
            'isDefaultShippingAddress' => true,
        ];
    }

    private function getAccount(Project $project, string $language): Account
    {
        $salutation = 'Frau';
        if (($salutations = $this->getAccountApiForProject($project)->getSalutations($language)) !== null) {
            $salutation = $salutations[array_rand($salutations)];
        }

        $account = new Account([
            'email' => 'integration-tests-not-exists+account-' . uniqid('', true) . '@frontastic.com',
            'salutation' => $salutation,
            'firstName' => 'Ashley',
            'lastName' => 'Stoltenberg',
            'birthday' => new \DateTimeImmutable('1961-11-6'),
            'confirmed' => false,
            'addresses' => [
                new Address($this->getTestAddressData($salutation)),
            ],
        ]);
        $account->setPassword('cHAaL4Pd.4yCcwLR');

        return $this->getAccountApiForProject($project)->create($account, null, $language);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetAvailableShippingMethods(Project $project, string $language): void
    {
        $account = $this->getAccount($project, $language);
        $cart = $this->getCart($project, $account, $language);
        $product = $this->getAProduct($project, $language);

        $cartApi = $this->getCartApiForProject($project);
        $cart = $cartApi->addToCart($cart, $this->getLineItemForProduct($product), $language);
        $cart = $cartApi->setShippingAddress($cart, $this->getFrontasticAddress(), $language);

        $shippingMethods = $cartApi->getAvailableShippingMethods($cart, $language);

        $this->assertContainsOnlyInstancesOf(ShippingMethod::class, $shippingMethods);
        foreach ($shippingMethods as $shippingMethod) {
            $this->assertNotEmptyString($shippingMethod->shippingMethodId);
        }
    }

    private function getCart(Project $project, Account $account, string $language): Cart
    {
        return $this->getCartApiForProject($project)->getForUser($account, $language);
    }
}
