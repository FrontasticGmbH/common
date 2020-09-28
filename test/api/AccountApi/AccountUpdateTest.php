<?php

namespace Frontastic\Common\ApiTests\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class AccountUpdateTest extends FrontasticApiTestCase
{

    /**
     * @dataProvider projectAndLanguage
     */
    public function testUpdateAccountWithInputData(Project $project, string $language): void
    {
        $accountApi = $this->getAccountApiForProject($project);
        $accountData = $this->getTestAccountDataWithInputData($accountApi, $language);

        // Create the account
        $createdAccount = $accountApi->create($accountData, null, $language);

        $this->assertNotEmptyString($createdAccount->accountId);
        $this->assertSameAccountData($accountData, $createdAccount);

        $updatedData = clone $createdAccount;
        $updatedData->firstName = 'new first name';
        $updatedData->lastName = 'new last name';

        // Update te account
        $updatedAccount = $accountApi->update($updatedData, $language);

        $this->assertSameAccountData($updatedData, $updatedAccount);
        $this->assertNotSame($updatedData->firstName, $createdAccount->firstName);
        $this->assertNotSame($updatedData->lastName, $createdAccount->lastName);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testUpdatePasswordAccountWithInputData(Project $project, string $language): void
    {
        $accountApi = $this->getAccountApiForProject($project);
        $accountData = $this->getTestAccountDataWithInputData($accountApi, $language);

        // Create the account
        $createdAccount = $accountApi->create($accountData, null, $language);

        $this->assertNotEmptyString($createdAccount->accountId);
        $this->assertSameAccountData($accountData, $createdAccount);

        $newPassword = '4yCcwLR.cHAaL4Pd';

        // Update te account
        $updatedAccount = $accountApi->updatePassword($createdAccount, $accountData->getPassword(), $newPassword);

        $this->assertSameAccountData($createdAccount, $updatedAccount);

        // Login with old password
        $createdAccount->setPassword($accountData->getPassword());
        $accountLoggedInWithOldPassword = $accountApi->login($createdAccount);

        $this->assertNull($accountLoggedInWithOldPassword);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testUpdateAddressWithInputData(Project $project, string $language): void
    {
        $accountApi = $this->getAccountApiForProject($project);
        $accountData = $this->getTestAccountDataWithInputData($accountApi, $language);

        // Create the account
        $account = $accountApi->create($accountData, null, $language);

        $salutation = 'Frau';
        $address = new Address($this->getTestAddressData($salutation));

        // Add address
        $accountWithNewAddress = $accountApi->addAddress($account, $address);
        $this->assertSameAccountAddressData($address, $accountWithNewAddress->addresses[0]);

        $updatedAddress = $accountWithNewAddress->addresses[0];
        $updatedAddress->firstName = 'Molly';
        $updatedAddress->lastName = 'Chambers';
        $updatedAddress->lastName = 'Chambers';
        $updatedAddress->streetName = 'New str name';
        $updatedAddress->postalCode = '7890';

        $accountWithUpdatedAddress = $accountApi->updateAddress($account, $updatedAddress);
        $this->assertSameAccountAddressData($updatedAddress, $accountWithUpdatedAddress->addresses[0]);
    }

    private function getTestAccountDataWithInputData(AccountApi $accountApi, string $language): Account
    {
        $salutation = 'Frau';
        if (($salutations = $accountApi->getSalutations($language)) !== null) {
            $salutation = $salutations[array_rand($salutations)];
        }

        $account = Account::newWithProjectSpecificData([
            'email' => 'integration-tests-not-exists+account-' . uniqid('', true) . '@frontastic.com',
            'salutation' => $salutation,
            'firstName' => 'Ashley',
            'lastName' => 'Stoltenberg',
            'birthday' => new \DateTimeImmutable('1961-11-6'),
            'confirmed' => false,
            'addresses' => [
                Address::newWithProjectSpecificData([
                    'salutation' => $salutation,
                    'firstName' => 'Ashley',
                    'lastName' => 'Stoltenberg',
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
        $this->assertSame($expected->firstName, $actual->firstName);
        $this->assertSame($expected->lastName, $actual->lastName);
    }

    private function getTestAddressData(?string $salutation = null): array
    {
        return [
            'salutation' => $salutation,
            'firstName' => 'Ashley',
            'lastName' => 'Stoltenberg',
            'streetName' => 'Test str.',
            'streetNumber' => '11',
            'additionalAddressInfo' => 'Additional addr info',
            'additionalStreetInfo' => 'Additional str info',
            'postalCode' => '123456',
            'city' => 'Berlin',
            'country' => 'DE',
            'phone' => '+49 12 1234 12234',
        ];
    }

    private function assertSameAccountAddressData(Address $expected, Address $actual): void
    {
        $this->assertNotEmptyString($actual->addressId);
        if ($actual->salutation !== null) {
            $this->assertSame($expected->salutation, $actual->salutation);
        }
        $this->assertSame($expected->firstName, $actual->firstName);
        $this->assertSame($expected->lastName, $actual->lastName);
        if ($actual->streetName !== null) {
            $this->assertSame($expected->streetName, $actual->streetName);
        }
        if ($actual->streetNumber !== null) {
            $this->assertSame($expected->streetNumber, $actual->streetNumber);
        }
        $this->assertSame($expected->city, $actual->city);
        $this->assertSame($expected->postalCode, $actual->postalCode);
        $this->assertSame($expected->phone, $actual->phone);
        $this->assertNotEmptyString($actual->country);
    }
}
