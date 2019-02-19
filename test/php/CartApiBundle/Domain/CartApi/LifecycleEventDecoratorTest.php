<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi;

use Frontastic\Common\CartApiBundle\Domain;

class LifecycleEventDecoratorTest extends \PHPUnit\Framework\TestCase
{
    public function testCallBeforeFunctionWithArguments()
    {
        $cartApi = $this->getMockBuilder(Domain\CartApi::class)->getMock();
        $listener = $this->getMockBuilder(\StdClass::class)->setMethods(['beforeAddToCart'])->getMock();
        $listener
            ->expects($this->once())
            ->method('beforeAddToCart')
            ->with(
                $this->equalTo($cartApi),
                $this->equalTo(new Domain\Cart()),
                $this->equalTo(new Domain\LineItem())
            );

        $decorator = new LifecycleEventDecorator($cartApi, [$listener]);
        $decorator->addToCart(
            new Domain\Cart(),
            new Domain\LineItem()
        );
    }

    public function testCallAfterFunctionWithArguments()
    {
        $cartApi = $this->getMockBuilder(Domain\CartApi::class)->getMock();
        $cartApi
            ->expects($this->once())
            ->method('addToCart')
            ->willReturn(new Domain\Cart());

        $listener = $this->getMockBuilder(\StdClass::class)->setMethods(['afterAddToCart'])->getMock();
        $listener
            ->expects($this->once())
            ->method('afterAddToCart')
            ->with(
                $this->equalTo($cartApi),
                $this->equalTo(new Domain\Cart())
            )->will($this->returnArgument(1));

        $decorator = new LifecycleEventDecorator($cartApi, [$listener]);
        $decorator->addToCart(
            new Domain\Cart(),
            new Domain\LineItem()
        );
    }

    public function testWorkWithListenerWithoutMethods()
    {
        $cartApi = $this->getMockBuilder(Domain\CartApi::class)->getMock();

        $decorator = new LifecycleEventDecorator($cartApi, [new \StdClass]);
        $decorator->addToCart(
            new Domain\Cart(),
            new Domain\LineItem()
        );

        // We cannot assert that methods which do not exist are not called. But
        // the test would cause a fatal error when such methods would be called
        // anyways…
        $this->assertTrue(true);
    }
}
