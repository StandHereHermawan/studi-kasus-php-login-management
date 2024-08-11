<?php

namespace AriefKarditya\LocalDomainPhp\Model;

use AriefKarditya\LocalDomainPhp\Domain\User;

class UserProfileUpdateResponse
{
    private User $user;

    public function __construct() {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function createObjectUser(
        string $id,
        string $name,
        string $password,
    ): User {
        $user = new User();
        $user->setId($id);
        $user->setName($name);
        $user->setPassword($password);
        $this->user = $user;
        return $user;
    }
}
