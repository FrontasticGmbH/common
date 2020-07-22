<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account\Request;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;
use Frontastic\Common\SprykerBundle\Domain\Account\SalutationHelper;

class CustomerRequestData extends AbstractRequestData
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var string
     */
    private $salutation;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @var string|null
     */
    private $confirmPassword;

    /**
     * @var bool
     */
    private $acceptedTerms;

    public function __construct(
        string $firstName,
        string $lastName,
        string $gender,
        string $salutation,
        string $email,
        ?string $password,
        ?string $confirmPassword,
        bool $acceptedTerms
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->gender = $gender;
        $this->salutation = $salutation;
        $this->email = $email;
        $this->password = $password;
        $this->confirmPassword = $confirmPassword;
        $this->acceptedTerms = $acceptedTerms;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        $data = [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'gender' => $this->gender,
            'salutation' => $this->salutation,
            'email' => $this->email,
            'acceptedTerms' => $this->acceptedTerms,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        if ($this->confirmPassword) {
            $data['confirmPassword'] = $this->confirmPassword;
        }

        return $data;
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'customers';
    }

    /**
     * @param Account $account
     * @return CustomerRequestData
     */
    public static function createFromAccount(Account $account): self
    {
        $salutation = $account->salutation;

        return new self(
            $account->firstName,
            $account->lastName,
            SalutationHelper::resolveGenderFromSalutation($salutation),
            SalutationHelper::DEFAULT_SPRYKER_SALUTATION,
            $account->email,
            $account->getPassword(),
            $account->getPassword(),
            true
        );
    }
}
