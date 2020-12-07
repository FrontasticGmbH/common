<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Request;

use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;

class VoucherRedeemRequestData extends AbstractRequestData
{
    /**
     * @var string
     */
    private $voucherCode;

    public function __construct(string $voucherCode)
    {
        $this->voucherCode = $voucherCode;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        return [
            'code' => $this->voucherCode,
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'vouchers';
    }
}
