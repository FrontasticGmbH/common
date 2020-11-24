<?php

namespace Frontastic\Common\ApiTests\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\DuplicateAccountException;
use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class AccountCreationTest extends FrontasticApiTestCase
{
    /**
     * @dataProvider projectAndLanguage
     */
    public function testSalutationsAreNullOrDistinctStrings(Project $project, string $language): void
    {
        $accountApi = $this->getAccountApiForProject($project);

        $salutations = $accountApi->getSalutations($language);
        if ($salutations !== null) {
            $this->assertIsArray($salutations);
            $this->assertContainsOnly('string', $salutations);
            $this->assertArrayHasDistinctValues($salutations);
        } else {
            $this->assertNull($salutations);
        }
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCreateNewAccountWithoutCart(Project $project, string $language): void
    {
        $accountApi = $this->getAccountApiForProject($project);
        $accountData = $this->getTestAccountData($accountApi, $language);

        // create the account
        $createdAccount = $accountApi->create($accountData, null, $language);

        $this->assertNotEmptyString($createdAccount->accountId);
        $this->assertSameAccountData($accountData, $createdAccount);

        // confirm account
        if ($createdAccount->confirmationToken !== null) {
            $this->assertNotEmptyString($createdAccount->confirmationToken);
            $this->assertInstanceOf(\DateTimeInterface::class, $createdAccount->tokenValidUntil);
            $this->assertGreaterThan(new \DateTimeImmutable('+10 minutes'), $createdAccount->tokenValidUntil);
            $this->assertFalse($createdAccount->confirmed);

            // confirm the email address
            $confirmedAccount = $accountApi->confirmEmail($createdAccount->confirmationToken, $language);
            $this->assertSame($createdAccount->accountId, $confirmedAccount->accountId);
            $this->assertSameAccountData($accountData, $confirmedAccount);
            $this->assertTrue($confirmedAccount->confirmed);
        } else {
            $this->assertTrue($createdAccount->confirmed);
            $this->assertNull($createdAccount->tokenValidUntil);
        }

        // log in to the just created account
        $fetchedAccount = $accountApi->login($this->getLoginDataFromAccount($accountData), null, $language);
        $this->assertNotNull($fetchedAccount);
        $this->assertSame($createdAccount->accountId, $fetchedAccount->accountId);
        $this->assertSameAccountData($accountData, $fetchedAccount);
        $this->assertTrue($fetchedAccount->confirmed);

        // re-fetch the account data
        $refreshedAccount = $accountApi->refreshAccount($fetchedAccount, $language);
        $this->assertEquals($fetchedAccount, $refreshedAccount);

        // log in with wrong password returns null
        $loginDataWithWrongPassword = $this->getLoginDataFromAccount($accountData);
        $loginDataWithWrongPassword->setPassword('This is the wrong password for this account');
        $accountWithWrongPassword = $accountApi->login($loginDataWithWrongPassword, null, $language);
        $this->assertNull($accountWithWrongPassword);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCreateNewAccountWithInputData(Project $project, string $language): void
    {
        $accountApi = $this->getAccountApiForProject($project);
        $accountData = $this->getTestAccountDataWithInputData($accountApi, $language);

        // create the account
        $createdAccount = $accountApi->create($accountData, null, $language);

        $this->assertNotEmptyString($createdAccount->accountId);
        $this->assertSameAccountData($accountData, $createdAccount);

        // confirm account
        if ($createdAccount->confirmationToken !== null) {
            $this->assertNotEmptyString($createdAccount->confirmationToken);
            $this->assertInstanceOf(\DateTimeInterface::class, $createdAccount->tokenValidUntil);
            $this->assertGreaterThan(new \DateTimeImmutable('+10 minutes'), $createdAccount->tokenValidUntil);
            $this->assertFalse($createdAccount->confirmed);

            // confirm the email address
            $confirmedAccount = $accountApi->confirmEmail($createdAccount->confirmationToken, $language);
            $this->assertSame($createdAccount->accountId, $confirmedAccount->accountId);
            $this->assertSameAccountData($accountData, $confirmedAccount);
            $this->assertTrue($confirmedAccount->confirmed);
        } else {
            $this->assertTrue($createdAccount->confirmed);
            $this->assertNull($createdAccount->tokenValidUntil);
        }

        // log in to the just created account
        $fetchedAccount = $accountApi->login($this->getLoginDataFromAccount($accountData), null, $language);
        $this->assertNotNull($fetchedAccount);
        $this->assertSame($createdAccount->accountId, $fetchedAccount->accountId);
        $this->assertSameAccountData($accountData, $fetchedAccount);
        $this->assertTrue($fetchedAccount->confirmed);

        // re-fetch the account data
        $refreshedAccount = $accountApi->refreshAccount($fetchedAccount, $language);
        $this->assertEquals($fetchedAccount, $refreshedAccount);

        // log in with wrong password returns null
        $loginDataWithWrongPassword = $this->getLoginDataFromAccount($accountData);
        $loginDataWithWrongPassword->setPassword('This is the wrong password for this account');
        $accountWithWrongPassword = $accountApi->login($loginDataWithWrongPassword, null, $language);
        $this->assertNull($accountWithWrongPassword);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testAddAddressToAccount(Project $project, string $language): void
    {
        $accountApi = $this->getAccountApiForProject($project);
        $accountData = $this->getTestAccountDataWithInputData($accountApi, $language);

        // Create the account
        $account = $accountApi->create($accountData, null, $language);

        $salutation = 'Frau';
        $address = new Address($this->getTestAddressData($salutation));
        $address->firstName = 'Judit';
        $address->lastName = 'Benson';

        // Add address
        $accountWithNewAddress = $accountApi->addAddress($account, $address);
        $this->assertSameAccountAddressData($address, $accountWithNewAddress->addresses[0]);

        $address02 = new Address($this->getTestAddressData($salutation));
        $address02->firstName = 'Molly';
        $address02->lastName = 'Chambers';

        $accountWithSecondAddress = $accountApi->addAddress($account, $address02);
        $this->assertSameAccountAddressData($address02, $accountWithSecondAddress->addresses[1]);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetAddresses(Project $project, string $language): void
    {
        $accountApi = $this->getAccountApiForProject($project);
        $accountData = $this->getTestAccountDataWithInputData($accountApi, $language);

        // Create the account
        $account = $accountApi->create($accountData, null, $language);

        $salutation = 'Frau';
        $address = new Address($this->getTestAddressData($salutation));
        $address->firstName = 'Judit';
        $address->lastName = 'Benson';

        // Add first address
        $account = $accountApi->addAddress($account, $address);

        $address02 = new Address($this->getTestAddressData($salutation));
        $address02->firstName = 'Molly';
        $address02->lastName = 'Chambers';

        // Add second address
        $account = $accountApi->addAddress($account, $address02);

        // Fetch addresses
        $fetchedAddresses = $accountApi->getAddresses($account);

        $this->assertCount(2, $fetchedAddresses);
        $this->assertSameAccountAddressData($address, $fetchedAddresses[0]);
        $this->assertSameAccountAddressData($address02, $fetchedAddresses[1]);
    }

        /**
     * @dataProvider projectAndLanguage
     */
    public function testCreateAccountWithExistingMailThrowsException(Project $project, string $language): void
    {
        $accountApi = $this->getAccountApiForProject($project);
        $accountData = $this->getTestAccountData($accountApi, $language);

        $accountApi->create($accountData, null, $language);

        $this->expectException(DuplicateAccountException::class);
        $accountApi->create($accountData, null, $language);
    }

    private function getTestAccountData(AccountApi $accountApi, string $language): Account
    {
        $salutation = 'Frau';
        if (($salutations = $accountApi->getSalutations($language)) !== null) {
            $salutation = $salutations[array_rand($salutations)];
        }

        $account = new Account([
            'email' => 'integration-tests-not-exists+account-' . uniqid('', true) . '@frontastic.com',
            'salutation' => $salutation,
            'firstName' => 'Ashley',
            'lastName' => 'Stoltenberg',
            'birthday' => new \DateTimeImmutable('1961-11-6'),
            'confirmed' => false,
            'addresses' => [new Address($this->getTestAddressData($salutation))],
        ]);
        $account->setPassword('cHAaL4Pd.4yCcwLR');
        return $account;
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
            'addresses' => [Address::newWithProjectSpecificData($this->getTestAddressData($salutation))],
        ]);
        $account->setPassword('cHAaL4Pd.4yCcwLR');
        return $account;
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

    private function getLoginDataFromAccount(Account $account): Account
    {
        $loginData = new Account([
            'email' => $account->email,
        ]);
        $loginData->setPassword($account->getPassword());
        return $loginData;
    }

    private function assertSameAccountData(Account $expected, Account $actual): void
    {
        $this->assertNotEmpty($actual->accountId);
        $this->assertSame($expected->email, $actual->email);
        $this->assertSame($expected->firstName, $actual->firstName);
        $this->assertSame($expected->lastName, $actual->lastName);
        if ($actual->salutation !== null) {
            $this->assertSame($expected->salutation, $actual->salutation);
        }
        if ($actual->birthday !== null) {
            $this->assertSameDate($expected->birthday, $actual->birthday);
        }
        if ($actual->projectSpecificData !== null) {
            $this->assertEquals($expected->projectSpecificData, $actual->projectSpecificData);
        }
        $this->assertEquals($expected->groups, $actual->groups);

        if (count($actual->addresses) > 0) {
            $this->assertCount(count($expected->addresses), $actual->addresses);
            $this->assertSameAccountAddressData($expected->addresses[0], $actual->addresses[0]);
        }
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

    private function assertSameDate(\DateTimeInterface $expected, $actual): void
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $actual);
        $formatString = 'Y-m-d';
        $this->assertSame($expected->format($formatString), $actual->format($formatString));
    }
}
