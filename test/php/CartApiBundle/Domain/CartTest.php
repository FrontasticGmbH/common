<?php

namespace Frontastic\CartApiBundle\Domain;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator;

class CartTest extends \PHPUnit\Framework\TestCase
{
    public function testVerifyCartIsComplete()
    {
		$cart = $this->getCart();
        $this->assertTrue($cart->isComplete());
    }

    public function testVerifyCartIsNotCompleteWithoutFullPayment()
    {
		$cart = $this->getCart();
        unset($cart->payments[1]);

        $this->assertFalse($cart->isComplete());
    }

    private function getCart(): Cart
    {
        $cartApi = new Commercetools(
            $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock(),
            new Mapper(),
            $this->getMockBuilder(OrderIdGenerator::class)->getMock()
        );

        $reflector = new \ReflectionObject($cartApi);
        $method = $reflector->getMethod('mapCart');
        $method->setAccessible(true);

        return $method->invoke($cartApi, json_decode(file_get_contents(__DIR__ . '/cartFixture.json'), true));
    }
}
