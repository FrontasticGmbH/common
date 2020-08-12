<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account\Request;

use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;

class CustomerPasswordRequestData extends AbstractRequestData
{
    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $newPassword;

    /**
     * @var string
     */
    private $confirmPassword;

    public function __construct(string $password, string $newPassword, string $confirmPassword)
    {
        $this->password = $password;
        $this->newPassword = $newPassword;
        $this->confirmPassword = $confirmPassword;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        return [
            'password' => $this->password,
            'newPassword' => $this->newPassword,
            'confirmPassword' => $this->confirmPassword,
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'customer-password';
    }
}
