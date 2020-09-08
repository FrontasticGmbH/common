<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account\Request;

use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;

class RestorePasswordRequestData extends AbstractRequestData
{
    /**
     * @var string
     */
    private $restorePasswordKey;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $confirmPassword;

    public function __construct(string $restorePasswordKey, string $password, string $confirmPassword)
    {
        $this->restorePasswordKey = $restorePasswordKey;
        $this->password = $password;
        $this->confirmPassword = $confirmPassword;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        return [
            'restorePasswordKey' => $this->restorePasswordKey,
            'password' => $this->password,
            'confirmPassword' => $this->confirmPassword,
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'customer-restore-password';
    }
}
