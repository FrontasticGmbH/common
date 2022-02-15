<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Request;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;
use Frontastic\Common\SprykerBundle\Domain\Account\SalutationHelper;
use Frontastic\Common\SprykerBundle\Domain\Address as SprykerAddress;

class CheckoutRequestData extends AbstractRequestData
{
    protected const DEFAULT_COUNTRY = 'DE';

    /**
     * @var Address
     */
    private $shippingAddress;

    /**
     * @var Address
     */
    private $billingAddress;

    /**
     * @var Payment
     */
    protected $payment;

    /**
     * @var Account
     */
    private $customer;

    /**
     * @var string
     */
    private $idCart;

    /**
     * @var int
     */
    private $idShipmentMethod;

    /**
     * @param Address|\Frontastic\Common\SprykerBundle\Domain\Address $address
     *
     * @return void
     */
    public function setShippingAddress($address): void
    {
        $this->shippingAddress = $address;
    }

    /**
     * @param Address|\Frontastic\Common\SprykerBundle\Domain\Address $address
     *
     * @return void
     */
    public function setBillingAddress($address): void
    {
        $this->billingAddress = $address;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Payment $payment
     *
     * @return void
     */
    public function setPayment($payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\Account $account
     *
     * @return void
     */
    public function setCustomer(Account $account): void
    {
        $this->customer = $account;
    }

    /**
     * @param string $idCart
     *
     * @return void
     */
    public function setIdCart(string $idCart): void
    {
        $this->idCart = $idCart;
    }

    /**
     * @param int $shipment
     *
     * @return void
     */
    public function setShipmentMethod(int $shipment): void
    {
        $this->idShipmentMethod = $shipment;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        $billingAddress = $this->billingAddress;
        $shippingAddress = $this->shippingAddress;
        $customer = $this->customer;

        //Todo: map firstName and lastName from frontend when available
        return [
            'idCart' => $this->idCart,
            'customer' => [
                'email' => $customer->email,
                'salutation' => SalutationHelper::getSprykerSalutation(
                    $customer->salutation ?? SalutationHelper::DEFAULT_FRONTASTIC_SALUTATION
                ),
                'firstName' => $billingAddress->firstName,
                'lastName' => $billingAddress->lastName,
            ],
            'billingAddress' => $this->addressAttributes($billingAddress),
            'shippingAddress' => $this->addressAttributes($shippingAddress),
            'payments' => [
                $this->formatPaymentAttributes(),
            ],
            'shipment' => [
                'idShipmentMethod' => $this->idShipmentMethod,
            ]
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'checkout';
    }

    /**
     * @return array
     */
    protected function formatPaymentAttributes(): array
    {
        return [
            'paymentProviderName' => $this->payment->paymentProvider,
            'paymentMethodName' => $this->payment->paymentId,
        ];
    }

    /**
     * @param Address $address
     * @return array
     */
    private function addressAttributes(Address $address): array
    {
        $isSprykerAddress = $address instanceof SprykerAddress;

        $data = [
            'salutation' => SalutationHelper::getSprykerSalutation(
                $address->salutation ?? SalutationHelper::DEFAULT_FRONTASTIC_SALUTATION
            ),
            'firstName' => $address->firstName,
            'lastName' => $address->lastName,
            'address1' => $address->streetName,
            'address2' => $address->streetNumber,
            'address3' => $address->additionalStreetInfo,
            'zipCode' => $address->postalCode,
            'city' => $address->city,
            'iso2Code' => $address->country ?
                mb_strtoupper(mb_substr($address->country, 0, 2)) : static::DEFAULT_COUNTRY,
            'company' => $isSprykerAddress && $address->company ? $address->company : '',
            'phone' => $address->phone,
            'isDefaultBilling' => $address->isDefaultBillingAddress,
            'isDefaultShipping' => $address->isDefaultShippingAddress,
        ];

        if ($address->addressId) {
            $data['id'] = $address->addressId;
        }

        return $data;
    }
}
