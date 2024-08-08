<?php

namespace AriefKarditya\LocalDomainPhp\Model;

use AriefKarditya\LocalDomainPhp\Domain\User;

class UserRegisterRequest
{
    private string $id;
    private string $name;
    private string $password;

    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setFieldFromDomainUser(User $user)
    {
        $this->id = $user->getId();
        $this->name = $user->getName();
        $this->password = $user->getPassword();
    }
}
