<?php

namespace AriefKarditya\LocalDomainPhp\Model;

class UserLoginRequest
{
    private ?string $id = null;
    private ?string $password = null;

    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setFieldFromDomainUser(User $user): void
    {
        $this->id = $user->getId();
        $this->password = $user->getPassword();
    }
}
