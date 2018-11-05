<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Collection;

use Kore\DataObject\DataObject;

class Account extends DataObject implements UserInterface, \Serializable
{
    /**
     * @var string
     */
    public $accountId;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var Group[]
     */
    public $groups = [];

    /**
     * @var string
     */
    private $confirmationToken;

    /**
     * @var string
     */
    public $confirmed = false;

    /**
     * @var DateTime
     */
    public $tokenValidUntil;

    public function setPassword(string $password)
    {
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
    }

    public function isValidPassword(string $password): bool
    {
        return $this->confirmed &&
            password_verify($password, $this->passwordHash);
    }

    public function generateConfirmationToken($validInterval = 'P7D'): string
    {
        $this->confirmationToken = md5(microtime());
        $this->tokenValidUntil = (new \DateTime())->add(new \DateInterval($validInterval));

        return $this->confirmationToken;
    }

    public function clearConfirmationToken()
    {
        $this->confirmationToken = null;
        $this->tokenValidUntil = null;
    }

    public function isValidConfirmationToken(string $confirmationToken): bool
    {
        return (($this->tokenValidUntil > new \DateTime('now')) &&
            ($confirmationToken === $this->confirmationToken));
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {
        return $this->passwordHash;
    }

    public function getSalt()
    {
        // Dummy method required by Symfony3, but makes no sense
    }

    public function eraseCredentials()
    {
        unset($this->confirmationToken);
        unset($this->passwordHash);
    }

    public function assertPermission(string $required)
    {
        $hasPermission = false;
        $groups = $this->groups ?: [];
        foreach ($groups as $group) {
            foreach ($group->permissions as $permission) {
                if (strpos($required, $permission) === 0) {
                    $hasPermission = true;
                    break 2;
                }
            }
        }

        if (!$hasPermission) {
            throw new PermissionRequiredException(
                'The user does not have the required permission: ' . $required
            );
        }
    }

    /**
     * @HACK: We ensure there is no doctrine collection in the user when
     * writing it into the session. The Collection is an active class
     * containing MANY other objects. We should actually only write the user
     * mail (ID) into the session.
     */
    public function cleanForSession(): User
    {
        $user = clone $this;

        if ($user->groups instanceof Collection) {
            $user->groups = $user->groups->toArray();
        }

        return $user;
    }

    public function serialize()
    {
        $variables = get_object_vars($this);

        foreach ($variables as $key => $value) {
            if ($value instanceof Collection) {
                $variables[$key] = $value->toArray();
            }
        }

        return \serialize($variables);
    }

    public function unserialize($serialized)
    {
        foreach (\unserialize($serialized) as $key => $value) {
            $this->$key = $value;
        }
    }
}
