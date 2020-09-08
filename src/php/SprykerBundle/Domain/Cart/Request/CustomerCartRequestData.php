<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Request;

use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;

class CustomerCartRequestData extends AbstractRequestData
{
    /**
     * @var string
     */
    private $priceMode;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $store;

    public function __construct(string $priceMode, string $currency, string $store)
    {
        $this->priceMode = $priceMode;
        $this->currency = $currency;
        $this->store = $store;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        return [
            'priceMode' => $this->priceMode,
            'currency' => $this->currency,
            'store' => $this->store,
            'name' => 'Shopping cart',
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'carts';
    }
}
