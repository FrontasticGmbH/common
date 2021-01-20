<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Request;

use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;

class CheckoutDataRequestData extends AbstractRequestData
{
    /**
     * @var string
     */
    private $idCart;

    public function __construct(string $idCart)
    {
        $this->idCart = $idCart;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        return [
            'idCart' => $this->idCart
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'checkout-data';
    }
}
