<?php

namespace Frontastic\Common\MvcBundle;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class MockTokenContextTest extends TestCase
{
    /**
     * @test
     */
    public function it_grants_access_from_token_roles() : void
    {
        $user = \Phake::mock(UserInterface::class);
        \Phake::when($user)->getRoles()->thenReturn(['ROLE_USER', 'ROLE_ADMIN']);

        $context = new MockTokenContext($user);

        $this->assertTrue($context->isGranted('ROLE_USER'));
    }
}
