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
    public $salutation;

    /**
     * @var string
     */
    public $prename;

    /**
     * @var string
     */
    public $lastname;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var \DateTime
     */
    public $birthday;

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
    public $confirmationToken;

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

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
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
