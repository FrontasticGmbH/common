<?php

namespace Frontastic\Common\ApiTests\AccountApi;

use DateTimeImmutable;
use DateTimeZone;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class AccountCreationTest extends FrontasticApiTestCase
{
    /**
     * @dataProvider project
     */
    public function testCreateNewAccountWithoutCart(Project $project): void
    {
        $accountApi = $this->getAccountApiForProject($project);
        $accountData = $this->getTestAccountData();

        // create the account
        $createdAccount = $accountApi->create($accountData);

        $this->assertNotEmptyString($createdAccount->accountId);
        $this->assertSameAccountData($accountData, $createdAccount);
        $this->assertFalse($createdAccount->confirmed);

        if ($createdAccount->confirmationToken !== null) {
            $this->assertNotEmptyString($createdAccount->confirmationToken);
            $this->assertNotNull($createdAccount->tokenValidUntil);
            $this->assertGreaterThan(new DateTimeImmutable('+10 minutes'), $createdAccount->tokenValidUntil);
        }

        if ($this->hasProjectFeature($project, 'canAuthenticateWithToken')) {
            $this->assertTrue($accountApi->login($accountData));
            $fetchedAccount = $accountApi->get($accountData->getToken($project->configuration['test']->authTokenType));
        } else {
            $fetchedAccount = $accountApi->get($accountData->email);
        }

        // fetch the just created account
        $this->assertSame($createdAccount->accountId, $fetchedAccount->accountId);
        $this->assertSameAccountData($accountData, $fetchedAccount);

        if ($createdAccount->confirmationToken !== null) {
            $this->assertFalse($fetchedAccount->confirmed);
            // confirm the email address
            $confirmedAccount = $accountApi->confirmEmail($createdAccount->confirmationToken);
            $this->assertSame($createdAccount->accountId, $confirmedAccount->accountId);
            $this->assertSameAccountData($accountData, $confirmedAccount);
            $this->assertTrue($confirmedAccount->confirmed);

            // fetch the confirmed account
            $fetchedVerifiedAccount = $accountApi->get($accountData->email);
            $this->assertSame($createdAccount->accountId, $fetchedVerifiedAccount->accountId);
            $this->assertSameAccountData($accountData, $fetchedVerifiedAccount);
            $this->assertTrue($fetchedVerifiedAccount->confirmed);
        }
    }

    private function getTestAccountData(): Account
    {
        $account = new Account([
            'email' => 'integration-tests-not-exists+account-' . uniqid('', true) . '@frontastic.cloud',
            'salutation' => 'Frau',
            'firstName' => 'Ashley',
            'lastName' => 'Stoltenberg',
            'birthday' => new DateTimeImmutable('1961-11-6', new DateTimeZone('UTC')),
            'confirmed' => false,
            'addresses' => [
                new Address([
                    'salutation' => 'Herr',
                    'streetName' => 'Test str.',
                    'streetNumber' => '11',
                    'additionalAddressInfo' => 'Additional addr info',
                    'additionalStreetInfo' => 'Additional str info',
                    'postalCode' => '123456',
                    'city' => 'Berlin',
                    'country' => 'Germany',
                    'phone' => '+49 12 1234 12234',
                ]),
            ],
        ]);
        $account->setPassword('cHAaL4Pd.4yCcwLR');
        return $account;
    }

    private function assertSameAccountData(Account $expected, Account $actual): void
    {
        $this->assertNotEmpty($actual->accountId);
        $this->assertSame($expected->email, $actual->email);
        $this->assertSame($expected->salutation, $actual->salutation);
        $this->assertSame($expected->firstName, $actual->firstName);
        $this->assertSame($expected->lastName, $actual->lastName);
        $this->assertEquals($expected->birthday, $actual->birthday);
        $this->assertEquals($expected->data, $actual->data);
        $this->assertEquals($expected->groups, $actual->groups);

        $this->assertSameAccountAddressData($expected->addresses[0], $expected->addresses[0]);
    }

    private function assertSameAccountAddressData(Address $expected, Address $actual): void
    {
        $this->assertNotEmpty($actual->addressId);
        $this->assertSame($expected->salutation, $actual->salutation);
        $this->assertSame($expected->firstName, $actual->firstName);
        $this->assertSame($expected->lastName, $actual->lastName);
        //$this->assertSame($expected->streetName, $actual->streetName);
        //$this->assertSame($expected->streetNumber, $actual->streetNumber);
        $this->assertSame($expected->city, $actual->city);
        $this->assertSame($expected->postalCode, $actual->postalCode);
        $this->assertSame($expected->phone, $actual->phone);
        $this->assertSame($expected->country, $actual->country);
    }
}
