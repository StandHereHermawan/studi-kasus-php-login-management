<?php

namespace AriefKarditya\LocalDomainPhp\Model;

use AriefKarditya\LocalDomainPhp\Domain\User;

class UserProfileUpdateRequest
{
    private ?string $id;
    private ?string $name;

    public function __construct() {}

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function setFieldByUser(User $user)
    {
        $this->id = $user->getId();
        $this->name = $user->getName();
    }
}
