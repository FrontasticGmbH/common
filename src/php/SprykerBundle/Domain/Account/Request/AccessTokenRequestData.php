<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account\Request;

use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;

class AccessTokenRequestData extends AbstractRequestData
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'access-tokens';
    }
}
