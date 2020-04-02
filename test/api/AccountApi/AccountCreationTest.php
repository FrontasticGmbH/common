<?php

use Frontastic\Common\AccountApiBundle\Domain\Account;
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
        $this->assertNotEmptyString($createdAccount->confirmationToken);
        $this->assertNotNull($createdAccount->tokenValidUntil);
        $this->assertGreaterThan(new DateTimeImmutable('+10 minutes'), $createdAccount->tokenValidUntil);

        // fetch the just created account
        $fetchedAccount = $accountApi->get($accountData->email);
        $this->assertSame($createdAccount->accountId, $fetchedAccount->accountId);
        $this->assertFalse($fetchedAccount->confirmed);
        $this->assertSameAccountData($accountData, $fetchedAccount);

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

    private function getTestAccountData(): Account
    {

        $account = new Account([
            'email' => 'integration-tests-not-exists+account-' . uniqid('', true) . '@frontastic.cloud',
            'salutation' => 'Mrs.',
            'firstName' => 'Ashley',
            'lastName' => 'Stoltenberg',
            'birthday' => new DateTimeImmutable('1961-11-6'),
            'confirmed' => false,
        ]);
        $account->setPassword('cHAaL4Pd4yCcwLR');
        return $account;
    }

    private function assertSameAccountData(Account $expected, Account $actual): void
    {
        $this->assertSame($expected->email, $actual->email);
        $this->assertSame($expected->salutation, $actual->salutation);
        $this->assertSame($expected->firstName, $actual->firstName);
        $this->assertSame($expected->lastName, $actual->lastName);
        $this->assertEquals($expected->birthday, $actual->birthday);
        $this->assertEquals($expected->data, $actual->data);
        $this->assertEquals($expected->groups, $actual->groups);
        $this->assertEquals($expected->addresses, $actual->addresses);
    }
}
