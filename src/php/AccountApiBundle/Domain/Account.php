<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Collection;

/**
 * @type
 */
class Account extends ApiDataObject implements UserInterface, \Serializable
{
    /**
     * @var string
     * @required
     */
    public $accountId;

    /**
     * @var string
     * @required
     */
    public $email;

    /**
     * @var string
     */
    public $salutation;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var \DateTime
     */
    public $birthday;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var Group[]
     * @required
     */
    public $groups = [];

    /**
     * @var string
     */
    public $confirmationToken;

    /**
     * @var string
     */
    public $confirmed = false;

    /**
     * @var \DateTime
     */
    public $tokenValidUntil;

    /**
     * @var \Frontastic\Common\AccountApiBundle\Domain\Address[]
     * @required
     */
    public $addresses = [];

    /**
     * @var string|null
     */
    public $authToken;

    /**
     * Access original object from backend
     *
     * This should only be used if you need very specific features
     * right NOW. Please notify Frontastic about your need so that
     * we can integrate those twith the common API. Any usage off
     * this property might make your code unstable against future
     * changes.
     *
     * @var mixed
     */
    public $dangerousInnerAccount;

    public function setPassword(string $password)
    {
        // This must use a deterministic hashing if we want to hash here at
        // all. Backends like Comemrcetools already hash themselves. With a
        // dynamic hashing mechanism like password_hash (random salt) this
        // would not verify.
        //
        // This hashing here would basically just be an additional transport
        // hashing and ensure backends like Commercetools never sees any real
        // password.
        $this->passwordHash = $password;
    }

    public function isValidPassword(string $password): bool
    {
        // We are not calling isValidPassword since Commercetools also hashes
        // the password and does not return the original hash, so that we can't
        // compare hashes any more.
        return false;
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
        unset($this->authToken);
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
    public function cleanForSession(): Account
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
