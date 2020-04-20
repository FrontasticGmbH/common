<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;
use Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools\Mapper as CartMapper;
use Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreator;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper as ProductMapper;

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
        $productMapper = new ProductMapper();

        $cartApi = new Commercetools(
            $this->createMock(Client::class),
            $productMapper,
            new CartMapper($productMapper),
            $this->createMock(CommercetoolsLocaleCreator::class),
            $this->createMock(OrderIdGenerator::class)
        );

        $reflector = new \ReflectionObject($cartApi);
        $method = $reflector->getMethod('mapCart');
        $method->setAccessible(true);

        return $method->invoke(
            $cartApi,
            json_decode(file_get_contents(__DIR__ . '/cartFixture.json'), true),
            new CommercetoolsLocale([
                'language' => 'de',
                'country' => 'DE',
                'currency' => 'EUR',
            ])
        );
    }
}
