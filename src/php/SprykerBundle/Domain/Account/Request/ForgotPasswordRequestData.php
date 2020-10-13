<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account\Request;

use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;

class ForgotPasswordRequestData extends AbstractRequestData
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        return [
            'email' => $this->email,
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'customer-forgotten-password';
    }
}
