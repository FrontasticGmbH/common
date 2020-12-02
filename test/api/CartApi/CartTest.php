<?php

namespace Frontastic\Common\ApiTests\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\CartApiBundle\Domain\Cart;
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

    private function getAccount(Project $project, string $language): Account
    {
        $account = new Account([
            'email' => 'integration-tests-not-exists+account-' . uniqid('', true) . '@frontastic.com',
            'salutation' => 'Frau',
            'firstName' => 'Ashley',
            'lastName' => 'Stoltenberg',
            'birthday' => new \DateTimeImmutable('1961-11-6'),
            'confirmed' => false,
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

        $cartApi = $this->getCartApiForProject($project);
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
