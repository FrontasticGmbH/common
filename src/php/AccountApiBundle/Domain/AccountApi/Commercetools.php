<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Payment;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;

class Commercetools implements AccountApi
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function login(string $email, string $password): Account
    {
        return $this->mapAccount($this->client->fetch('/accounts', [
            'customerId' => $userId,
        ]));
    }

    private function mapAccount(array $account): Account
    {
        /**
         * @TODO:
         *
         * [ ] Map (and sort) custom line items
         * [ ] Map delivery costs / properties
         * [ ] Map product discounts
         * [ ] Map discount codes
         * [ ] Map tax information
         * [ ] Map discount text locales to our scheme
         */
        return new Account([
            'accountId' => $account['id'],
            'accountVersion' => $account['version'],
            'lineItems' => $this->mapLineItems($account),
            'sum' => $account['totalPrice']['centAmount'],
        ]);
    }

    /**
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
