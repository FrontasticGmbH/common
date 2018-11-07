<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
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

    public function get(string $email): Account
    {
        $result = $this->client->fetch('/customers', [
            'where' => 'email="' . $email . '"',
        ]);

        if ($result->count >= 1) {
            return $this->mapAccount($result->results[0]);
        } else {
            throw new \OutOfBoundsException('Could not find user with email ' . $email);
        }
    }

    public function create(Account $account): Account
    {
        return $this->mapAccount($this->client->post(
            '/customers',
            [],
            [],
            json_encode([
                'email' => $account->email,
                'password' => $account->getPassword(),
                'isEmailVerified' => $account->confirmed,
            ])
        )['customer']);
    }

    public function login(Account $account): bool
    {
        try {
            $account = $this->mapAccount($this->client->post(
                '/login',
                [],
                [],
                json_encode([
                    // @TODO: We should pass existing anonymous cart IDs so
                    // that this cart is merged into the users cart.
                    'email' => $account->email,
                    'password' => $account->getPassword(),
                ])
            )['customer']);

            return $account->confirmed;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function mapAccount(array $account): Account
    {
        return new Account([
            'accountId' => $account['id'],
            'email' => $account['email'],
            'confirmed' => $account['isEmailVerified'],
            // Do NOT map the password back
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
